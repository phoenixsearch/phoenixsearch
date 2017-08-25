<?php

namespace pheonixsearch\core;

use pheonixsearch\types\DaemonInterface;

declare(ticks=1);

class Daemon implements DaemonInterface
{
    private $stopServer = false;

    public function run()
    {
        if ($this->isDaemonActive(self::PID_FILE)) {
            echo 'Daemon is already active';
            exit(1);
        }

        // создаем дочерний процесс
        $child_pid = pcntl_fork();
        if ($child_pid > 0) {
            // выходим из родительского, привязанного к консоли, процесса
            exit(0);
        }
        posix_setsid();
        file_put_contents(self::PID_FILE, getmypid());
        pcntl_signal(SIGTERM, "childSignalled");

        $childProcess = [];
        while (false === $this->stopServer) {
            if (false === $this->stopServer && (count($childProcess) < self::MAX_CHILD_PROCESSES)) {
                $pid = pcntl_fork();
                if ($pid == -1) {
                    //TODO: ошибка - не смогли создать процесс

                } elseif ($pid) {
                    //процесс создан
                    $childProcess[$pid] = true;
                } else {
                    $pid = getmypid();
                    //TODO: дочерний процесс - тут рабочая нагрузка
                    exit;
                }
            } else {
                //чтоб не гонять цикл вхолостую
                sleep(self::DELAY);
            }
            //проверяем, умер ли один из детей
            while ($signaled_pid = pcntl_waitpid(-1, $status, WNOHANG)) {
                if ($signaled_pid == -1) {
                    //детей не осталось
                    $childProcess = [];
                    break;
                } else {
                    unset($childProcess[$signaled_pid]);
                }
            }
        }
    }

    private function isDaemonActive($pidFile)
    {
        if (is_file($pidFile)) {
            $pid = file_get_contents($pidFile);
            //проверяем на наличие процесса
            if (posix_kill($pid, 0)) {
                //демон уже запущен
                return true;
            } else {
                //pid-файл есть, но процесса нет
                if (!unlink($pidFile)) {
                    //не могу уничтожить pid-файл. ошибка
                    exit(-1);
                }
            }
        }
        return false;
    }

    public function sigHandler($sigNum)
    {
        switch ($sigNum) {
            case SIGTERM: {
                $this->stopServer = true;
                break;
            }
            default: {
                //все остальные сигналы
            }
        }
    }

    private function childSignalled(int $sigNum)
    {
        $this->sigHandler($sigNum);
    }
}
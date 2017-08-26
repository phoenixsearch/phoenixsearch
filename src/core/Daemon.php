<?php

namespace pheonixsearch\core;

use pheonixsearch\helpers\DaemonOutput;
use pheonixsearch\types\DaemonInterface;

declare(ticks = 1);

class Daemon implements DaemonInterface
{
    private $stopServer = false;

    public function run(string $pName)
    {
        if ($this->isDaemonActive(self::PID_FILE)) {
            echo 'Daemon is already active' . PHP_EOL;
            exit(1);
        }

        // create process
        $child_pid = pcntl_fork();
        if ($child_pid > 0) {
            // exit from parent process attached to console
            exit(0);
        }
        posix_setsid();
        file_put_contents(self::PID_FILE, getmypid());
        // todo: signals handler
        // give a title to a process
        cli_set_process_title($pName);
        $childProcess = [];
        while (false === $this->stopServer) {
            if (false === $this->stopServer && (count($childProcess) < self::MAX_CHILD_PROCESSES)) {
                $pid = pcntl_fork();
                if ($pid == -1) {
                    echo 'Process can\'t be created' . PHP_EOL;
                    exit(1);
                } elseif ($pid) {
                    // process successfully created
                    $childProcess[$pid] = true;
                } else {
                    // todo: execute job
                    $pid = getmypid();
                    DaemonOutput::print($pid, 'running task');
                    exit(0);
                }
            } else {
                // to prevent running idle
                sleep(self::DELAY);
            }
            // check if some child is dead
            while ($signaledPid = pcntl_waitpid(-1, $status, WNOHANG)) {
                if ($signaledPid == -1) {
                    // there are no children
                    $childProcess = [];
                    break;
                } else {
                    // exit process
                    unset($childProcess[$signaledPid]);
                }
            }
        }
    }

    private function isDaemonActive($pidFile)
    {
        if (is_file($pidFile)) {
            $pid = file_get_contents($pidFile);
            // check for process existence
            if (posix_kill($pid, 0)) {
                // daemon already started
                return true;
            } else {
                // pid file is present, but there is no process
                if (false === unlink($pidFile)) {
                    // can't delete pid-file - error
                    exit(-1);
                }
            }
        }
        return false;
    }
}
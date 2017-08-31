<?php

namespace pheonixsearch\core;

use pheonixsearch\helpers\DaemonOutput;
use pheonixsearch\types\CoreInterface;
use pheonixsearch\types\DaemonInterface;
use pheonixsearch\types\IndexInterface;

declare(ticks=1);

class Daemon implements DaemonInterface
{
    private $stopServer = false;

    public function run(string $pTitle)
    {
        if ($this->isDaemonActive(self::PID_FILE)) {
            echo 'Daemon is already active' . PHP_EOL;
            exit(1);
        }

        // create process
        $childPid = pcntl_fork();
        if ($childPid > 0) {
            // exit from parent process attached to console
            exit(0);
        }
        posix_setsid();
        file_put_contents(self::PID_FILE, getmypid());
        // todo: signals handler
        // give a title to a process (only for Linux, with luv)
        if (mb_strpos(PHP_OS, CoreInterface::DAEMON_TITLE_OS) !== false) {
            cli_set_process_title($pTitle);
        }
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
                } else { // child processes
                    $ipcKey = ftok(self::PID_FILE, CoreInterface::FTOK_PROJECT_NAME);
                    $queue  = msg_get_queue($ipcKey);
                    $stat   = msg_stat_queue($queue);
                    if ($stat['msg_qnum'] > 0 && true === msg_receive($queue, 0,
                            $msgType, self::MAX_MESSAGE_SIZE, $msg)
                    ) {
                        $pid = getmypid();
                        DaemonOutput::print($pid, 'running task type: ' . $msgType . ' ...');
                        if ($msgType === CoreInterface::MSG_TYPE_DELETE_INDEX) {
                            $this->deleteIndex($msg);
                        }
                        if ($msgType === CoreInterface::MSG_TYPE_REINDEX) {
                            $this->reindex($msg);
                        }
                        DaemonOutput::print($pid, 'task type: ' . $msgType . ' successfully executed.');
                    }
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

    private function deleteIndex(array $msg)
    {
        \pheonixsearch\core\Environment::setEnvironment();
        putenv('APP_MODE=command');
        $handler = new \pheonixsearch\core\RequestHandler();
        $handler->setRequestMethod(\pheonixsearch\types\HttpBase::HTTP_METHOD_DELETE);
        $index = '/' . $msg[IndexInterface::INDEX];
        $type  = empty($msg[IndexInterface::TYPE]) ? '' : '/' . $msg[IndexInterface::TYPE] . '/';
        $handler->setRoutePath($index . $type);
        $del = new \pheonixsearch\core\Delete($handler);
        $del->clearAllIndexData();
    }

    private function reindex(array $msg)
    {
        \pheonixsearch\core\Environment::setEnvironment();
        putenv('APP_MODE=command');
        $handler = new \pheonixsearch\core\RequestHandler();
        $handler->setRequestMethod(\pheonixsearch\types\HttpBase::HTTP_METHOD_POST);
        $reindexPath = '/' . IndexInterface::REINDEX;
        $handler->setRoutePath($reindexPath);
        $del = new \pheonixsearch\core\Index($handler);
        $del->reindexData($msg);
    }
}
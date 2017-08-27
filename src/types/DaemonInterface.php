<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.08.17
 * Time: 19:49
 */

namespace pheonixsearch\types;


interface DaemonInterface
{
    const DELAY               = 60; // 60 seconds
    const MAX_CHILD_PROCESSES = 3;
    const PID_FILE            = '/tmp/phoenix_search.pid';
    const MAX_MESSAGE_SIZE    = 8192;

    public function run(string $pTitle);
}
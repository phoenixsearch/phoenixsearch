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
    const MAX_CHILD_PROCESSES = 32;
    const PID_FILE            = '/tmp/phoenix_search.pid';

    public function run();
}
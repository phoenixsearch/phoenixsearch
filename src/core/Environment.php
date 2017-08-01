<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 01.08.17
 * Time: 19:40
 */

namespace pheonixsearch\core;


class Environment
{
    public function setEnvironment()
    {
        $fp = fopen('.env', 'r');
        while ($line = fgets($fp)) {
            putenv(str_replace(PHP_EOL, '', $line));
        }
    }
}
<?php

namespace pheonixsearch\helpers;

class DaemonOutput
{
    public static function print(int $pid, string $info): void
    {
        echo $pid . ' [' . date('d M H:i:s') . '] * ' . $info . PHP_EOL;
    }
}
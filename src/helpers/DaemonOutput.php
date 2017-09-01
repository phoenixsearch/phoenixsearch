<?php

namespace pheonixsearch\helpers;

use pheonixsearch\types\CoreInterface;

class DaemonOutput
{
    const OPERATION_TITLES = [
        CoreInterface::MSG_TYPE_DELETE_INDEX => '"delete index"',
        CoreInterface::MSG_TYPE_REINDEX      => '"re-index"',
    ];

    public static function print(int $pid, string $info): void
    {
        echo $pid . ' [' . date('d M H:i:s') . '] * ' . $info . PHP_EOL;
    }
}
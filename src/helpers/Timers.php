<?php

namespace pheonixsearch\helpers;


class Timers
{
    public static function millitime() {
        $microtime = microtime();
        $comps = explode(' ', $microtime);
        return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
    }
}
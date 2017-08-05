<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05.08.17
 * Time: 22:22
 */

namespace pheonixsearch\helpers;


class Timers
{
    public static function millitime() {
        $microtime = microtime();
        $comps = explode(' ', $microtime);
        return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.08.17
 * Time: 19:33
 */

namespace pheonixsearch\helpers;


class Json
{
    public static function encode(array $json, int $opts)
    {
        return json_encode($json, $opts);
    }

    public static function decode(string $json)
    {
        return json_decode($json, true, 512);
    }
}
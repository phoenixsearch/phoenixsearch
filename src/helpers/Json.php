<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.08.17
 * Time: 19:33
 */

namespace pheonixsearch\helpers;


use pheonixsearch\types\CoreInterface;

class Json
{
    public static function encode(array $json, int $opts = 0)
    {
        return json_encode($json, $opts);
    }

    public static function decode(string $json)
    {
        return json_decode($json, true, 512);
    }

    public static function parse(string $json): array
    {
        return unserialize(str_replace(CoreInterface::DOUBLE_QUOTES_ESC, CoreInterface::DOUBLE_QUOTES, $json));
    }
}
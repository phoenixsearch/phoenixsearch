<?php
namespace pheonixsearch\helpers;

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
}
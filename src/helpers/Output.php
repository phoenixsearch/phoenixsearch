<?php
namespace pheonixsearch\helpers;

use pheonixsearch\core\StdFields;

class Output
{
    public static function jsonSearch(StdFields $stdFields, array $arr, $opts = 0)
    {
        $total    = count($arr);
        $response = [
            'took'      => $stdFields->getTook(),
            'timed_out' => $stdFields->isTimedOut(),
            'hits'      => [
                'total' => $total,
                'hits'  => $arr,
            ],
        ];
        echo Json::encode($response, $opts);
        exit(0);
    }

    public static function construct()
    {

    }
}
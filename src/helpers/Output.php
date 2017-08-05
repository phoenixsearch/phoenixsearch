<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05.08.17
 * Time: 13:07
 */

namespace pheonixsearch\helpers;


use pheonixsearch\core\StdFields;

class Output
{
    public static function jsonSearch(StdFields $fields, array $arr, $opts = 0)
    {
        $total    = count($arr);
        $response = [
            'took'      => $fields->took,
            'timed_out' => $fields->timedOut,
            'hits'      => [
                'total' => $total,
                'hits'  => [
                    '_index'  => $fields->index,
                    '_type'   => $fields->type,
                    '_id'     => $fields->id,
                    '_source' => $arr,
                ],
            ],
        ];
        echo Json::encode($response, $opts);
        exit(0);
    }

    public static function construct()
    {

    }
}
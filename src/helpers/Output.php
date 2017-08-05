<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05.08.17
 * Time: 13:07
 */

namespace pheonixsearch\helpers;


class Output
{
    public static function json(array $arr, $opts = 0)
    {

        echo Json::encode($arr, $opts);
        exit(0);
    }
}
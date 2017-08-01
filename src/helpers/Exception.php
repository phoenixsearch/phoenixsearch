<?php

namespace pheonixsearch\helpers;

class Exception
{
    public static function encode($class, $line, $code, $message)
    {
        return json_encode(
            [
                'code'    => $code,
                'message' => $message,
                'class'   => $class,
                'line'    => $line,
            ],
            JSON_PRETTY_PRINT
        );
    }
}
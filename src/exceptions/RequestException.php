<?php

namespace pheonixsearch\exceptions;

class RequestException extends BaseException
{
    public function __construct($message = "", $code = 0)
    {
        parent::__construct($message, $code);
    }
}
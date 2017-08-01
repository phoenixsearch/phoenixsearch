<?php
namespace pheonixsearch\exceptions;

class UriException extends BaseException
{
    public function __construct($message = "", $code = 0)
    {
        parent::__construct($message, $code);
    }
}
<?php
namespace pheonixsearch\exceptions;

class DirectoryException extends BaseException
{
    public function __construct($message = "", $code = 0)
    {
        parent::__construct($message, $code);
    }
}
<?php

namespace pheonixsearch\exceptions;

use pheonixsearch\helpers\Exception;

class BaseException extends \Exception
{
    public function __construct($message = "", $code = 0)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        echo Exception::encode($this->getFile(), $this->getLine(), $this->getCode(), $this->getMessage());
        exit(1);
    }
}
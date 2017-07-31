<?php

namespace pheonixsearch\route;

use pheonixsearch\exceptions\RequestException;
use pheonixsearch\helpers\Request;
use pheonixsearch\types\Errors;
use pheonixsearch\types\HttpBase;

class Entry extends AbstractEntry
{
    private $requestBodyJson   = '';
    private $requestBodyObject = null;
    private $requestMethod     = '';

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($this->requestMethod !== HttpBase::HTTP_METHOD_DELETE) {
            $this->setRequestBody();
        }
    }

    public function run()
    {
        call_user_func(
            [
                $this, $this->getIndexMethod($this->requestMethod)
            ], $this->requestBodyObject
        );
    }

    private function setRequestBody()
    {
        $this->requestBodyJson = Request::getJsonString();
        if (empty($this->requestBodyJson)) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_BODY_IS_EMPTY], Errors::REQUEST_BODY_IS_EMPTY);
        }
        $this->requestBodyObject = Request::getJsonBody($this->requestBodyJson);
    }
}
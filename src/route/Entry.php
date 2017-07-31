<?php

namespace pheonixsearch\route;

use pheonixsearch\exceptions\RequestException;
use pheonixsearch\types\Errors;
use pheonixsearch\types\HttpBase;

class Entry extends AbstractEntry
{
    private $requestBodyJson = '';
    private $requestMethod   = '';

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($this->requestMethod !== HttpBase::HTTP_METHOD_DELETE) {
            $this->requestBodyJson = stream_get_contents(STDIN);
            if (empty($this->requestBodyJson)) {
                throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_BODY_IS_EMPTY], Errors::REQUEST_BODY_IS_EMPTY);
            }
        }
    }

    public function run()
    {
        $bodyObject = json_decode($this->requestBodyJson);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_BODY_IS_NOT_JSON], Errors::REQUEST_BODY_IS_NOT_JSON);
        }
        call_user_func([
            $this, $this->getIndexMethod($this->requestMethod)
        ], $bodyObject);
    }

    private function getRequestBodyObject()
    {

    }
}
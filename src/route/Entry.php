<?php

namespace pheonixsearch\route;

use pheonixsearch\exceptions\RequestException;
use pheonixsearch\types\Errors;

class Entry extends AbstractEntry
{
    private $requestBody   = '';
    private $requestMethod = '';

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestBody = stream_get_contents(STDIN);
        if (empty($this->requestBody)) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_BODY_IS_EMPTY], Errors::REQUEST_BODY_IS_EMPTY);
        }
    }

    public function run()
    {
        $bodyObject = json_decode($this->requestBody);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_BODY_IS_NOT_JSON], Errors::REQUEST_BODY_IS_NOT_JSON);
        }

    }
}
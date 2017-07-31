<?php
namespace pheonixsearch\helpers;

use pheonixsearch\exceptions\RequestException;
use pheonixsearch\types\Errors;

class Request
{
    public static function getJsonBody(string $requestBodyJson, int $opts = 0)
    {
        $object = json_decode($requestBodyJson, false, 512, $opts);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_BODY_IS_NOT_JSON], Errors::REQUEST_BODY_IS_NOT_JSON);
        }
        return $object;
    }

    public static function getJsonString()
    {
        $fp = fopen('php://input', 'r');
        return stream_get_contents($fp);
    }
}
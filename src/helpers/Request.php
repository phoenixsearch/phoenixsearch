<?php
namespace pheonixsearch\helpers;

use pheonixsearch\exceptions\RequestException;
use pheonixsearch\types\Errors;

class Request
{
    public static function getJsonBody(string $requestBodyJson)
    {
        $object = Json::decode($requestBodyJson);
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
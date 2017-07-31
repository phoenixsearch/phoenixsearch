<?php

namespace pheonixsearch\helpers;

class Request
{
    public static function getJsonBody(int $opts = 0)
    {
        $object = json_decode($this->requestBodyJson, false, 512, $opts);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_BODY_IS_NOT_JSON], Errors::REQUEST_BODY_IS_NOT_JSON);
        }
        return $object;
    }

    public static function getJsonString()
    {
        return stream_get_contents(STDIN);
    }
}
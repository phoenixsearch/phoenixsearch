<?php

namespace pheonixsearch\types;

interface Errors
{
    // request errors
    const REQUEST_BODY_IS_EMPTY    = 101;
    const REQUEST_BODY_IS_NOT_JSON = 102;
    const REQUEST_MESSAGES         = [
        self::REQUEST_BODY_IS_EMPTY    => 'Request body is empty',
        self::REQUEST_BODY_IS_NOT_JSON => 'Request json body is invalid',
    ];
}
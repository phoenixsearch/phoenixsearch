<?php

namespace pheonixsearch\core;


use pheonixsearch\exceptions\RequestException;
use pheonixsearch\helpers\Request;
use pheonixsearch\types\EntryInterface;
use pheonixsearch\types\Errors;
use pheonixsearch\types\HttpBase;

class RequestHandler
{
    private $requestBodyJson = '';
    private $requestBodyObject = null;
    private $routePath = null;
    private $routeQuery = null;

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestBodyJson = Request::getJsonString();
        if (empty($this->requestBodyJson)) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_BODY_IS_EMPTY], Errors::REQUEST_BODY_IS_EMPTY);
        }
        if ($this->requestMethod !== HttpBase::HTTP_METHOD_DELETE) {
            $this->requestBodyObject = Request::getJsonBody($this->requestBodyJson);
        }
        $parsedUri        = parse_url($_SERVER['REQUEST_URI']);
        $this->routePath  = empty($parsedUri[EntryInterface::URI_PATH]) ? null : $parsedUri[EntryInterface::URI_PATH];
        $this->routeQuery = empty($parsedUri[EntryInterface::URI_QUERY]) ? null : $parsedUri[EntryInterface::URI_QUERY];
    }
}
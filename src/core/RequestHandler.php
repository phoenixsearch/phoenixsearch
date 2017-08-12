<?php

namespace pheonixsearch\core;

use pheonixsearch\exceptions\RequestException;
use pheonixsearch\helpers\Request;
use pheonixsearch\types\EntryInterface;
use pheonixsearch\types\Errors;
use pheonixsearch\types\HttpBase;
use pheonixsearch\types\IndexInterface;

class RequestHandler
{
    private $requestBodyJson  = '';
    private $requestBodyArray = null;
    private $routePath        = null;
    private $routeQuery       = null;
    private $requestMethod    = '';

    // options
    private $offset    = 0;
    private $limit     = 10000;
    private $highlight = false;

    public function __construct()
    {
        $this->setRequestMethod($_SERVER['REQUEST_METHOD']);
        $this->setRequestBodyJson(Request::getJsonString());
        if (empty($this->requestBodyJson) && in_array($this->requestMethod,
                [HttpBase::HTTP_METHOD_GET, HttpBase::HTTP_METHOD_DELETE]) === false) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_BODY_IS_EMPTY],
                Errors::REQUEST_BODY_IS_EMPTY);
        }
        if ($this->requestMethod !== HttpBase::HTTP_METHOD_DELETE) {
            $this->setRequestBodyArray(Request::getJsonBody($this->requestBodyJson));
            if (empty($this->requestBodyArray[IndexInterface::OFFSET]) === false) {
                $this->setOffset($this->requestBodyArray[IndexInterface::OFFSET]);
            }
            if (empty($this->requestBodyArray[IndexInterface::LIMIT]) === false) {
                $this->setLimit($this->requestBodyArray[IndexInterface::LIMIT]);
            }
        }
        $parsedUri        = parse_url($_SERVER['REQUEST_URI']);
        $this->setRoutePath(empty($parsedUri[EntryInterface::URI_PATH]) ? null : $parsedUri[EntryInterface::URI_PATH]);
        $this->setRouteQuery(empty($parsedUri[EntryInterface::URI_QUERY]) ? null : $parsedUri[EntryInterface::URI_QUERY]);
    }

    /**
     * @return null
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }

    /**
     * @param null $routePath
     */
    public function setRoutePath($routePath)
    {
        $this->routePath = $routePath;
    }

    /**
     * @return null
     */
    public function getRouteQuery()
    {
        return $this->routeQuery;
    }

    /**
     * @param null $routeQuery
     */
    public function setRouteQuery($routeQuery)
    {
        $this->routeQuery = $routeQuery;
    }

    /**
     * @return bool|string
     */
    public function getRequestBodyJson()
    {
        return $this->requestBodyJson;
    }

    /**
     * @param bool|string $requestBodyJson
     */
    public function setRequestBodyJson($requestBodyJson)
    {
        $this->requestBodyJson = $requestBodyJson;
    }

    /**
     * @return mixed|null
     */
    public function getRequestBodyArray()
    {
        return $this->requestBodyArray;
    }

    /**
     * @param mixed|null $requestBodyArray
     */
    public function setRequestBodyArray($requestBodyArray)
    {
        $this->requestBodyArray = $requestBodyArray;
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * @param string $requestMethod
     */
    public function setRequestMethod(string $requestMethod)
    {
        $this->requestMethod = $requestMethod;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return bool
     */
    public function isHighlight(): bool
    {
        return $this->highlight;
    }

    /**
     * @param bool $highlight
     */
    public function setHighlight(bool $highlight)
    {
        $this->highlight = $highlight;
    }

}
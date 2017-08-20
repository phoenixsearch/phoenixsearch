<?php

namespace pheonixsearch\core;

use pheonixsearch\exceptions\RequestException;
use pheonixsearch\helpers\Request;
use pheonixsearch\types\CoreInterface;
use pheonixsearch\types\EntryInterface;
use pheonixsearch\types\Errors;
use pheonixsearch\types\HttpBase;
use pheonixsearch\types\IndexInterface;

class RequestHandler
{
    private $requestBodyJson  = '';
    private $requestBodyArray = [];
    private $routePath        = null;
    private $routeQuery       = null;
    private $requestMethod    = '';

    // options
    private $offset          = 0;
    private $limit           = CoreInterface::DEFAULT_LIMIT;
    private $highlight       = false;
    private $preTags         = '';
    private $postTags        = '';
    private $highlightFields = [];

    public function __construct()
    {
        $this->setRequestMethod($_SERVER['REQUEST_METHOD']);
        $this->setRequestBodyJson(Request::getJsonString());
        if (empty($this->requestBodyJson) && in_array($this->requestMethod,
                [HttpBase::HTTP_METHOD_GET, HttpBase::HTTP_METHOD_DELETE]) === false
        ) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_BODY_IS_EMPTY],
                Errors::REQUEST_BODY_IS_EMPTY);
        }
        if (empty($this->requestBodyJson) === false
            && $this->requestMethod !== HttpBase::HTTP_METHOD_DELETE
        ) {
            $this->setRequestBodyArray(Request::getJsonBody($this->requestBodyJson));
        }
        if ($this->requestMethod === HttpBase::HTTP_METHOD_GET
            || $this->requestMethod === HttpBase::HTTP_METHOD_POST
        ) {
            if (empty($this->requestBodyArray[IndexInterface::OFFSET]) === false) {
                $this->setOffset($this->requestBodyArray[IndexInterface::OFFSET]);
            }
            if (empty($this->requestBodyArray[IndexInterface::LIMIT]) === false) {
                $this->setLimit($this->requestBodyArray[IndexInterface::LIMIT]);
            }
            if (empty($this->requestBodyArray[IndexInterface::HIGHLIGHT]) === false) {
                $this->setHighlight(true);
            }
            if (empty($this->requestBodyArray[IndexInterface::HIGHLIGHT][IndexInterface::FIELDS]) === false) {
                $this->setHighlightFields($this->requestBodyArray[IndexInterface::HIGHLIGHT][IndexInterface::FIELDS]);
            }
            if (empty($this->requestBodyArray[IndexInterface::HIGHLIGHT][IndexInterface::PRE_TAGS]) === false) {
                $this->setPreTags(implode('', $this->requestBodyArray[IndexInterface::HIGHLIGHT][IndexInterface::PRE_TAGS]));
            }
            if (empty($this->requestBodyArray[IndexInterface::HIGHLIGHT][IndexInterface::POST_TAGS]) === false) {
                $this->setPostTags(implode('', $this->requestBodyArray[IndexInterface::HIGHLIGHT][IndexInterface::POST_TAGS]));
            }
        }
        $parsedUri = parse_url($_SERVER['REQUEST_URI']);
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

    /**
     * @return string
     */
    public function getPreTags(): string
    {
        return $this->preTags;
    }

    /**
     * @param string $preTags
     */
    public function setPreTags(string $preTags)
    {
        $this->preTags = $preTags;
    }

    /**
     * @return string
     */
    public function getPostTags(): string
    {
        return $this->postTags;
    }

    /**
     * @param string $postTags
     */
    public function setPostTags(string $postTags)
    {
        $this->postTags = $postTags;
    }

    /**
     * @return array
     */
    public function getHighlightFields(): array
    {
        return $this->highlightFields;
    }

    /**
     * @param array $highlightFields
     */
    public function setHighlightFields(array $highlightFields)
    {
        $this->highlightFields = $highlightFields;
    }

    /**
     * @return array
     */
    public function getRoutePathEntities(): array
    {
        return explode('/', $this->routePath);
    }

}
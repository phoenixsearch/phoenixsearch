<?php

namespace pheonixsearch\route;

use pheonixsearch\core\Index;
use pheonixsearch\core\RequestHandler;
use pheonixsearch\types\HttpBase;
use pheonixsearch\types\EntryInterface;

abstract class AbstractEntry implements EntryInterface
{
    protected $path           = '';
    private   $requestHandler = null;

    private $requestMethodMap = [
        HttpBase::HTTP_METHOD_GET    => HttpBase::HTTP_GET_METHOD,
        HttpBase::HTTP_METHOD_POST   => HttpBase::HTTP_POST_METHOD,
        HttpBase::HTTP_METHOD_PUT    => HttpBase::HTTP_PUT_METHOD,
        HttpBase::HTTP_METHOD_DELETE => HttpBase::HTTP_DELETE_METHOD,
    ];

    protected function __construct(RequestHandler $handler)
    {
        $this->requestHandler = $handler;
    }

    /**
     *
     * @param string $httpMethod http method to match
     *
     * @return string   method to call
     */
    public function getIndexMethod(string $httpMethod)
    {
        return empty($this->requestMethodMap[$httpMethod]) ? false : $this->requestMethodMap[$httpMethod];
    }

    /**
     *
     */
    protected function index()
    {
        $index = new Index($this->requestHandler);
    }

    /**
     * @param array     $uri
     * @param \stdClass $object
     * @param string    $json
     */
    protected function search(array $uri, \stdClass $object, string $json)
    {
    }

    /**
     * @param array     $uri
     * @param \stdClass $object
     * @param string    $json
     */
    protected function update(array $uri, \stdClass $object, string $json)
    {
        $index = new Index($uri, $object, $json);
        $index->buildIndex();
    }

    /**
     * @param array     $uri
     * @param \stdClass $object
     * @param string    $json
     */
    protected function delete(array $uri, \stdClass $object, string $json)
    {
    }
}
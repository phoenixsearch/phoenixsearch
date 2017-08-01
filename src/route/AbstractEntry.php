<?php

namespace pheonixsearch\route;

use pheonixsearch\core\Index;
use pheonixsearch\types\HttpBase;
use pheonixsearch\types\EntryInterface;

abstract class AbstractEntry implements EntryInterface
{
    protected $path = '';

    private $requestMethodMap = [
        HttpBase::HTTP_METHOD_GET    => HttpBase::HTTP_GET_METHOD,
        HttpBase::HTTP_METHOD_POST   => HttpBase::HTTP_POST_METHOD,
        HttpBase::HTTP_METHOD_PUT    => HttpBase::HTTP_PUT_METHOD,
        HttpBase::HTTP_METHOD_DELETE => HttpBase::HTTP_DELETE_METHOD,
    ];

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

    protected function index(array $uri, \stdClass $object)
    {
        $index = new Index($uri, $object);

    }

    protected function search(array $uri, \stdClass $object)
    {
    }

    protected function update(array $uri, \stdClass $object)
    {
        $index = new Index($uri, $object);
        $index->buildIndex();
    }

    protected function delete()
    {
    }
}
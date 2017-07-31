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

    protected function index(\stdClass $object)
    {
        $index = new Index($object);

    }

    protected function search()
    {
    }

    protected function update()
    {
        echo 124;
    }

    protected function delete()
    {
    }
}
<?php

namespace pheonixsearch\route;

use pheonixsearch\core\Delete;
use pheonixsearch\core\Index;
use pheonixsearch\core\RequestHandler;
use pheonixsearch\core\Search;
use pheonixsearch\types\HttpBase;
use pheonixsearch\types\EntryInterface;
use pheonixsearch\types\IndexInterface;

abstract class AbstractEntry implements EntryInterface
{
    protected $path           = '';
    private   $requestHandler = null;

    private $requestMethodMap = [
        HttpBase::HTTP_METHOD_GET    => HttpBase::HTTP_GET_METHOD,
        HttpBase::HTTP_METHOD_POST   => HttpBase::HTTP_GET_METHOD,
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
        $routeEntities = $this->requestHandler->getRoutePathEntities();
        if (empty($routeEntities[0]) === false
            && empty($routeEntities[1]) === false
            && $routeEntities[0] === IndexInterface::CAT
            && $routeEntities[1] === IndexInterface::INDICES
        ) { // info method call
            return HttpBase::HTTP_INFO_METHOD;
        }
        return empty($this->requestMethodMap[$httpMethod]) ? false : $this->requestMethodMap[$httpMethod];
    }

    /**
     *
     */
    protected function info()
    {

    }

    /**
     *
     */
    protected function search()
    {
        $search = new Search($this->requestHandler);
        $search->performSearch();
    }

    /**
     *
     */
    protected function update()
    {
        $index = new Index($this->requestHandler);
        $index->buildIndex();
    }

    /**
     *
     */
    protected function delete()
    {
        $delete = new Delete($this->requestHandler);
        $delete->delete();
    }
}
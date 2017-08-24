<?php

namespace pheonixsearch\route;

use pheonixsearch\core\CatIndices;
use pheonixsearch\core\Delete;
use pheonixsearch\core\Index;
use pheonixsearch\core\RequestHandler;
use pheonixsearch\core\Search;
use pheonixsearch\helpers\Request;
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

    /**
     * AbstractEntry constructor.
     * @param RequestHandler $handler
     */
    protected function __construct(RequestHandler $handler)
    {
        $this->requestHandler = $handler;
    }

    /**
     * Routing detection method with method match
     * @param string $httpMethod http method to match
     *
     * @return string   method to call
     */
    public function getIndexMethod(string $httpMethod)
    {
        $routeEntities = $this->requestHandler->getRoutePathEntities();
        if ($httpMethod === HttpBase::HTTP_METHOD_GET
            && empty($routeEntities[1]) === false
            && empty($routeEntities[2]) === false
            && $routeEntities[1] === IndexInterface::CAT
            && $routeEntities[2] === IndexInterface::INDICES
        ) { // info method call
            return HttpBase::HTTP_INFO_METHOD;
        }
        if ($httpMethod === HttpBase::HTTP_METHOD_GET
            && empty($routeEntities[1]) === false
            && empty($routeEntities[2])
            && empty($this->requestHandler->getRequestBodyArray())
        ) { // index info call - we got only GET and index route
            return HttpBase::HTTP_INDEX_INFO_METHOD;
        }
        if ($httpMethod === HttpBase::HTTP_METHOD_DELETE
            && empty($routeEntities[1]) === false
            && empty($routeEntities[3])) { // id is empty - delete all index data
            return HttpBase::HTTP_DELETE_INDEX_METHOD;
        }
        return empty($this->requestMethodMap[$httpMethod]) ? false : $this->requestMethodMap[$httpMethod];
    }

    /**
     *  Gets all info about indices
     */
    protected function info()
    {
        $info = new CatIndices($this->requestHandler);
        $info->getCat();
    }

    /**
     * Gets index detailed info with props
     */
    protected function indexInfo()
    {
        $info = new CatIndices($this->requestHandler);
        $info->getCatIndex();
    }

    /**
     * Searches for documents by word or phrase
     */
    protected function search()
    {
        $search = new Search($this->requestHandler);
        $search->performSearch();
    }

    /**
     * Inserts or updates document
     */
    protected function update()
    {
        $index = new Index($this->requestHandler);
        $index->buildIndex();
    }

    /**
     * Deletes document by id
     */
    protected function delete()
    {
        $delete = new Delete($this->requestHandler);
        $delete->delete();
    }

    /**
     *  Deletes all entities for particular index
     */
    protected function deleteIndex()
    {
        $delete = new Delete($this->requestHandler);
        $delete->deleteIndex();
    }
}
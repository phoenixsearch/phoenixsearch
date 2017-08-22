<?php

use pheonixsearch\core\RequestHandler;
use pheonixsearch\core\Search;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    private $requestHandler = null;

    public static function setUpBeforeClass()
    {
        putenv('REDIS_SCHEME=tcp');
        putenv('REDIS_HOST=127.0.0.1');
        putenv('REDIS_PORT=6379');
        putenv('REDIS_CLUSTER=false');
        putenv('REDIS_REPLICATION=false');
        putenv('APP_MODE=testing');
    }

//    public function testUpdate()
//    {
//    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSearch()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $routePath                 = '/myindex/myindextype';
        $routeQuery                = 'pretty';
        $_SERVER['REQUEST_URI']    = $routePath . '?' . $routeQuery;
        $offset                    = 0;
        $limit                     = 10;
        $this->requestHandler      = new RequestHandler();
        $this->requestHandler->setOffset($offset);
        $this->requestHandler->setLimit($limit);
        $json                      = '{   
    "offset":' . $offset . ', 
    "limit":' . $limit . ', 
    "highlight" : {
        "pre_tags" : ["<tag1>", "<tag2>"],
        "post_tags" : ["</tag1>", "</tag2>"],
        "fields" : {
            "name" : {}, "text" : {}
        }
    },
    "query" : {
        "term" : { "text" : "Lorem ipsum" }
    }
}';
        $this->requestHandler->setRequestBodyJson($json);
        $this->requestHandler->setRequestBodyArray(json_decode($json, true));
        $search = new Search($this->requestHandler);
        $search->performSearch();
        $this->assertEquals($_SERVER['REQUEST_METHOD'], $this->requestHandler->getRequestMethod());
        $this->assertEquals($offset, $this->requestHandler->getOffset());
        $this->assertEquals($limit, $this->requestHandler->getLimit());
        $this->assertEquals($routePath, $this->requestHandler->getRoutePath());
        $this->assertEquals($routeQuery, $this->requestHandler->getRouteQuery());
    }

//    public function testDelete()
//    {
//    }

//    public function testInfo()
//    {
//    }
}
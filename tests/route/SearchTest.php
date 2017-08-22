<?php

use pheonixsearch\core\RequestHandler;
use pheonixsearch\core\Search;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    private $requestHandler = null;

    public static function setUpBeforeClass()
    {
    }

    public function testUpdate()
    {
    }

    public function testSearch()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/myindex/myindextype?pretty';
        $this->requestHandler      = new RequestHandler();
        $json                      = '{   
    "offset":0, 
    "limit":10, 
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
    }

    public function testDelete()
    {
    }
}
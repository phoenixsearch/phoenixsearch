<?php

use pheonixsearch\core\RequestHandler;
use pheonixsearch\core\Search;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    private $requestHandler = null;

    public function setUpBeforeClass()
    {
    }

    public function testUpdate()
    {

    }

    public function testSearch()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->requestHandler = new RequestHandler();
        $this->requestHandler->setRequestBodyJson('');
        $search = new Search($this->requestHandler);
        $search->performSearch();
    }

    public function testDelete()
    {

    }
}
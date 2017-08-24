<?php

use pheonixsearch\core\Index;
use pheonixsearch\core\RequestHandler;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    private $requestHandler = null;
    private $docId          = 0;

    public static function setUpBeforeClass()
    {
        putenv('REDIS_SCHEME=tcp');
        putenv('REDIS_HOST=127.0.0.1');
        putenv('REDIS_PORT=6379');
        putenv('REDIS_CLUSTER=false');
        putenv('REDIS_REPLICATION=false');
        putenv('APP_MODE=testing');
    }

    public function testCreateNewIndex()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $routePath                 = '/' . uniqid();
        $routeQuery                = 'pretty';
        $_SERVER['REQUEST_URI']    = $routePath . '?' . $routeQuery;
        $json                      = '{
            "title": "Lorem ipsum is a pseudo-Latin text used in web design",
            "text": "Lorem ipsum is a pseudo-Latin text used in web design, typography, layout, and printing in place of English to emphasise design elements over content. It\'s also called placeholder (or filler) text. It\'s a convenient tool for mock-ups. It helps to outline the visual elements of a document or presentation, eg typography, font, or layout. Lorem ipsum is mostly a part of a Latin text by the classical author and philosopher Cicero. Its words and letters have been changed by addition or removal, so to deliberately render its content nonsensical; it\'s not genuine, correct, or comprehensible Latin anymore. While lorem ipsum\'s still resembles classical Latin, it actually has no meaning whatsoever. As Cicero\'s text doesn\'t contain the letters K, W, or Z, alien to latin, these, and others are often inserted randomly to mimic the typographic appearence of European languages, as are digraphs not to be found in the original.",
            "data": "2017-08-21"
        }';
        $this->requestHandler      = new RequestHandler();
        $this->requestHandler->setRequestBodyJson($json);
        $this->requestHandler->setRequestBodyArray(json_decode($json, true));
        $this->requestHandler->setRoutePath($routePath);
        $this->requestHandler->setRouteQuery($routeQuery);
        $this->requestHandler->setRequestMethod($_SERVER['REQUEST_METHOD']);
        $index = new Index($this->requestHandler);
        $index->buildIndex();
        $this->docId = $index->getStdFields()->getId();
        $this->assertEquals($_SERVER['REQUEST_METHOD'], $this->requestHandler->getRequestMethod());
        $this->assertEquals($routePath, $this->requestHandler->getRoutePath());
        $this->assertEquals($routeQuery, $this->requestHandler->getRouteQuery());
    }
}
<?php

namespace pheonixsearch\core;

use pheonixsearch\exceptions\UriException;
use pheonixsearch\storage\RedisConnector;
use pheonixsearch\types\CoreInterface;
use pheonixsearch\types\EntryInterface;
use pheonixsearch\types\Errors;
use Predis\Client;

class Core implements CoreInterface
{
    private $routePath  = null;
    private $routeQuery = null;

    private $index     = '';
    private $indexType = '';
    private $id        = 0;
    private $indexKey  = '';

    /** @var Client $redisConn */
    private $redisConn = null;
    /** @var RequestHandler $requestHandler */
    private $requestHandler = null;

    protected function __construct(RequestHandler $handler)
    {
        $this->redisConn  = RedisConnector::getInstance();
        $this->routePath  = $handler->getRoutePath();
        $this->routeQuery = $handler->getRouteQuery();
        // parse index/type from path
        $pathArray = explode('/', $this->routePath);
        if (empty($pathArray[1]) === false) {
            $this->index     = $pathArray[1];
            $this->indexType = empty($pathArray[2]) ? '' : $pathArray[2];
            $this->id        = empty($pathArray[3]) ? 0 : $pathArray[3];
        } else {
            throw new UriException(Errors::REQUEST_MESSAGES[Errors::REQUEST_URI_EMPTY_INDEX], Errors::REQUEST_URI_EMPTY_INDEX);
        }
        $this->requestHandler = $handler;
        $this->setIndexKey();
    }

    protected function insertWord(string $word)
    {
        $wordHash = md5($word);
        $lkey     = $this->indexKey . $wordHash; // index_-_-_type_-_-_md5(word)
//        $lrange = $this->redisConn->lrange($lkey, 0, -1);
        $incr = $this->redisConn->incr($this->indexKey);
        $this->redisConn->lpush($lkey, [$incr]);
        $this->redisConn->hset($this->indexKey, $incr, $this->requestHandler->getRequestBodyJson());
    }

    /**
     *  Glues the index with indexType by glue _-_-_, if there is no indexType
     *  index will be appended by glue anyway to avoid redundant logic
     */
    private function setIndexKey()
    {
        $this->indexKey = $this->index . (empty($this->indexType)
                ? self::INDEX_GLUE
                : (self::INDEX_GLUE . $this->indexType . self::INDEX_GLUE));
    }
}
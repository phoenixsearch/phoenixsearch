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
    private $routePath = null;
    private $routeQuery = null;

    private $index = '';
    private $indexType = '';
    private $id = 0;
    private $indexKey = '';

    /** @var Client $redisConn */
    private $redisConn = null;

    protected function __construct(array $uri, \stdClass $object, string $json)
    {
        $this->redisConn = RedisConnector::getInstance();
        $this->routePath  = $uri[EntryInterface::URI_PATH];
        $this->routeQuery = $uri[EntryInterface::URI_QUERY];
        // parse index/type from path
        $pathArray = explode('/', $this->routePath);
        if (empty($pathArray[1]) === false) {
            $this->index     = $pathArray[1];
            $this->indexType = empty($pathArray[2]) ? '' : $pathArray[2];
            $this->id        = empty($pathArray[3]) ? 0 : $pathArray[3];
        } else {
            throw new UriException(Errors::REQUEST_MESSAGES[Errors::REQUEST_URI_EMPTY_INDEX], Errors::REQUEST_URI_EMPTY_INDEX);
        }
        $this->setIndexKey();
    }

    protected function insertWord(string $word)
    {
        $wordHash = md5($word);
        // todo: if the word is new construct mappings key_-_-_type md5(word) -> PK
        $this->redisConn->lpush($this->indexKey . $wordHash, [$this->redisConn->incr($this->indexKey)]);
        // todo: if the word is in storage then get the last PK from LIST and LPUT PK+1 with this doc

    }

    /**
     *  Glues the index with indexType by glue, if there is no indexType
     *  index will be appended by glue anyway to avoid redundant logic
     */
    private function setIndexKey()
    {
        $this->indexKey = $this->index . (empty($this->indexType)
                ? self::INDEX_GLUE
                : (self::INDEX_GLUE . $this->indexType . self::INDEX_GLUE));
    }
}
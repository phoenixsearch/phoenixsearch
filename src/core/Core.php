<?php

namespace pheonixsearch\core;

use pheonixsearch\exceptions\UriException;
use pheonixsearch\helpers\Json;
use pheonixsearch\helpers\Output;
use pheonixsearch\storage\RedisConnector;
use pheonixsearch\types\CoreInterface;
use pheonixsearch\types\Errors;
use pheonixsearch\types\IndexInterface;
use Predis\Client;

class Core implements CoreInterface
{
    private $routePath = null;
    private $routeQuery = null;

    private $index = '';
    private $indexType = '';
    private $id = 0;
    private $hashIndexKey = '';
    private $listIndexKey = '';

    private $words = [];

    /** @var Client $redisConn */
    private $redisConn = null;
    /** @var RequestHandler $requestHandler */
    private $requestHandler = null;
    /** @var StdFields $stdFields */
    private $stdFields = null;

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
        $this->setStdFields();
        $this->setHashIndexKey();
        $this->setListIndexKey();
    }

    protected function insertWord(string $word)
    {
        $wordHash  = md5($word);
        $jsonArray = $this->requestHandler->getRequestBodyArray();
        $lkey      = $this->listIndexKey . $wordHash;
        $hkey      = $this->hashIndexKey . $wordHash;
        $incr      = $this->redisConn->incr($this->listIndexKey);
        $this->redisConn->lpush($lkey, [$incr]);
        $jsonArray['_id']        = $incr;
        $jsonArray['_timestamp'] = time();
        $jsonToStore             = str_replace(self::DOUBLE_QUOTES, self::DOUBLE_QUOTES_ESC,
            serialize($jsonArray));
        $this->redisConn->hset($hkey, $incr, $jsonToStore);
    }

    protected function searchPhrase(array $fieldValue)
    {
        $opts   = 0;
        $result = [];
        if (CoreInterface::JSON_PRETTY_PRINT === $this->routeQuery) {
            $opts = JSON_PRETTY_PRINT;
        }
        foreach ($fieldValue as $field => $value) {
            $this->words = explode(IndexInterface::SYMBOL_SPACE, $value);
            $cntWords    = count($this->words);
            foreach ($this->words as &$word) {
                $wordHash = md5($word);
                $lkey     = $this->listIndexKey . $wordHash;
                $lrange   = $this->redisConn->lrange($lkey, self::LRANGE_DEFAULT_START, self::LRANGE_DEFAULT_STOP);
                if (empty($lrange) === false) {
                    $hkey    = $this->hashIndexKey . $wordHash;
                    $indices = array_values($lrange);
                    $docs    = $this->redisConn->hmget($hkey, $indices);
                    if ($cntWords > 1) { // intersect search
                        $result = $this->setMatches($docs, $value);
                    } else { // one word
                        foreach ($docs as $doc) {
                            $result[] = Json::parse($doc);
                        }
                    }
                }
            }
        }
        Output::jsonSearch($this->stdFields, $result, $opts);
    }

    private function setMatches(array $docs, string $phrase)
    {
        $matched = [];
        foreach ($docs as $index => &$doc) {
            if (mb_strpos($doc, $phrase) !== false) {
                $matched[] = Json::parse($doc);
            }
        }
        return $matched;
    }

    /**
     *  Glues the index with indexType by glue _-_-_, if there is no indexType
     *  index will be appended by glue anyway to avoid redundant logic
     */
    private function setHashIndexKey()
    {
        $this->hashIndexKey = $this->index . (empty($this->indexType)
                ? self::HASH_INDEX_GLUE
                : (self::HASH_INDEX_GLUE . $this->indexType . self::HASH_INDEX_GLUE));
    }

    /**
     *  Glues the index with indexType by glue _-_-_, if there is no indexType
     *  index will be appended by glue anyway to avoid redundant logic
     */
    private function setListIndexKey()
    {
        $this->listIndexKey = $this->index . (empty($this->indexType)
                ? self::LIST_INDEX_GLUE
                : (self::LIST_INDEX_GLUE . $this->indexType . self::LIST_INDEX_GLUE));
    }

    private function setStdFields()
    {
        $this->stdFields        = new StdFields();
        $this->stdFields->index = $this->index;
        $this->stdFields->type  = $this->indexType;
        $this->stdFields->id    = $this->id;
    }
}
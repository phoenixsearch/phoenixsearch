<?php

namespace pheonixsearch\core;

use pheonixsearch\exceptions\UriException;
use pheonixsearch\helpers\Json;
use pheonixsearch\helpers\Timers;
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
    private $incrKey = '';

    private $words = [];

    /** @var Client $redisConn */
    private $redisConn = null;
    /** @var RequestHandler $requestHandler */
    private $requestHandler = null;
    /** @var StdFields $stdFields */
    private $stdFields = null;

    private $docHashes = [];
    private $wordHashes = [];
    private $result = [];

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
        $this->setIncrKey();
    }

    protected function insertWord(string $word)
    {
        $wordHash = md5($word);
        // prevent doubling repeated words
        if (in_array($wordHash, $this->wordHashes) === false) {
            $this->wordHashes[] = $wordHash;
            $jsonArray          = $this->requestHandler->getRequestBodyArray();
            $lkey               = $this->listIndexKey . $wordHash;
            $hkey               = $this->hashIndexKey . $wordHash;
            $incr               = $this->redisConn->incr($this->listIndexKey);
            $this->redisConn->lpush($lkey, [$incr]);
//        $jsonArray['_timestamp'] = time(); // it brakes hash compare on intersection
            $doc = str_replace(self::DOUBLE_QUOTES, self::DOUBLE_QUOTES_ESC,
                serialize($jsonArray));
            $this->redisConn->hset($hkey, $incr, $doc);
            $this->stdFields->setCreated(true);
            if ($this->stdFields->getId() === 0) {
                $this->setIndexData($doc);
            }
        }
    }

    protected function searchPhrase(array $fieldValue)
    {
        $tStart = Timers::millitime();
        foreach ($fieldValue as $field => $phrase) {
            $this->words = explode(IndexInterface::SYMBOL_SPACE, $phrase);
            $cntWords    = count($this->words);
            foreach ($this->words as &$word) { // perf by ref
                $wordHash = md5($word);
                $lkey     = $this->listIndexKey . $wordHash;
                $lrange   = $this->redisConn->lrange($lkey, self::LRANGE_DEFAULT_START, self::LRANGE_DEFAULT_STOP);
                if (empty($lrange) === false) {
                    $hkey    = $this->hashIndexKey . $wordHash;
                    $indices = array_values($lrange);
                    $docs    = $this->redisConn->hmget($hkey, $indices);
                    if ($cntWords > 1) { // intersect search
                        $this->setMatches($docs, $phrase);
                    } else if ($cntWords === 1) {
                        $this->setMatch($docs);
                    }
                }
            }
            unset($this->docHashes);
        }
        $took = Timers::millitime() - $tStart;
        $this->stdFields->setTook($took);
        $this->stdFields->setHits($this->result);
    }

    private function setMatches(array $docs, string $phrase)
    {
        foreach ($docs as $index => &$doc) { // perf by ref
            $docHash = md5($doc);
            if (mb_strpos($doc, $phrase) !== false && in_array($docHash, $this->docHashes) === false) {
                $this->setIndexData($doc);
                $this->result[]    = [
                    IndexInterface::INDEX     => $this->stdFields->getIndex(),
                    IndexInterface::TYPE      => $this->stdFields->getType(),
                    IndexInterface::ID        => $this->stdFields->getId(),
                    IndexInterface::TIMESTAMP => $this->stdFields->getTimestamp(),
                    IndexInterface::SOURCE    => Json::parse($doc),
                ];
                $this->docHashes[] = $docHash;
            }
        }
    }

    private function setMatch(array $docs)
    {
        foreach ($docs as &$doc) { // perf by ref
            $this->result[] = [
                IndexInterface::INDEX  => $this->stdFields->getIndex(),
                IndexInterface::TYPE   => $this->stdFields->getType(),
                IndexInterface::ID     => $this->stdFields->getId(),
                IndexInterface::SOURCE => Json::parse($doc),
            ];
        }
    }

    /**
     *  Glues the index with indexType by glue :, if there is no indexType
     *  index will be appended by glue anyway to avoid redundant logic
     */
    private function setHashIndexKey()
    {
        $this->hashIndexKey = $this->index . (empty($this->indexType)
                ? self::HASH_INDEX_GLUE
                : (self::HASH_INDEX_GLUE . $this->indexType . self::HASH_INDEX_GLUE));
    }

    private function setIncrKey()
    {
        $this->incrKey = $this->index . (empty($this->indexType) ? ''
                : (self::HASH_INDEX_GLUE . $this->indexType . ''));
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
        $this->stdFields = new StdFields();
        $this->stdFields->setIndex($this->index);
        $this->stdFields->setType($this->indexType);
        $opts = 0;
        if (CoreInterface::JSON_PRETTY_PRINT === $this->routeQuery) {
            $opts = JSON_PRETTY_PRINT;
        }
        $this->stdFields->setOpts($opts);
    }

    public function getStdFields()
    {
        return $this->stdFields;
    }

    private function setIndexData(string $doc)
    {
        $docSha = sha1($doc);
        $data   = unserialize($this->redisConn->hget($this->incrKey, $docSha));
        if (empty($data)) {
            $id   = $this->redisConn->incr($this->hashIndexKey);
            $t    = time();
            $data = [
                IndexInterface::ID        => $id,
                IndexInterface::TIMESTAMP => $t,
            ];
            $this->redisConn->hset($this->incrKey, $docSha, serialize($data));
        }
        $this->stdFields->setId($data[IndexInterface::ID]);
        $this->stdFields->setTimestamp($data[IndexInterface::TIMESTAMP]);
    }
}
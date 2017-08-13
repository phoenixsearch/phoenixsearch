<?php

namespace pheonixsearch\core;

use pheonixsearch\exceptions\UriException;
use pheonixsearch\helpers\Highlighter;
use pheonixsearch\helpers\Json;
use pheonixsearch\helpers\Timers;
use pheonixsearch\storage\RedisConnector;
use pheonixsearch\types\CoreInterface;
use pheonixsearch\types\Errors;
use pheonixsearch\types\HttpBase;
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
    public $requestHandler = null;
    /** @var StdFields $stdFields */
    private $stdFields = null;

    private $docHashes = [];
    private $wordHashes = [];
    private $result = [];

    private $requestDocument = '';
    private $requestSource = '';

    private $listWordKeys = [];
    private $hashWordKeys = [];

    private $offset = 0;
    private $limit = CoreInterface::DEFAULT_LIMIT;
    private $found = 0; // incremented counter between phrases & words

    public $highlight = false;
    public $preTags = '';
    public $postTags = '';

    /**
     * Core constructor.
     * @param RequestHandler $handler
     * @throws UriException
     */
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
        if ($this->requestHandler->getRequestMethod() === HttpBase::HTTP_METHOD_GET) { // search
            $this->offset    = $this->requestHandler->getOffset();
            $this->limit     = $this->requestHandler->getLimit();
            $this->highlight = $this->requestHandler->isHighlight();
            $this->preTags   = $this->requestHandler->getPreTags();
            $this->postTags  = $this->requestHandler->getPostTags();
        }
        $this->setStdFields();
        $this->setHashIndexKey();
        $this->setListIndexKey();
        $this->setIncrKey();
    }

    /**
     * @param string $word
     */
    protected function insertWord(string $word): void
    {
        $wordHash = md5($word);
        // prevent doubling repeated words
        if (in_array($wordHash, $this->wordHashes) === false) {
            $this->wordHashes[] = $wordHash;
            $lKey               = $this->listIndexKey . $wordHash;
            if ($this->stdFields->getId() === 0) {
                $this->setIndexData($lKey);
            }
            $this->setRequestDocument();
            $hKey                 = $this->hashIndexKey . $wordHash;
            $this->listWordKeys[] = $lKey;
            $this->hashWordKeys[] = $hKey;
            $incr                 = $this->redisConn->incr($this->listIndexKey);
            $this->redisConn->lpush($lKey, [$incr]);
            $this->redisConn->hset($hKey, $incr, $this->requestDocument);
        }
    }

    /**
     * @return null|string
     */
    protected function getDocInfo(): ?string
    {
        $docSha = sha1($this->requestSource);
        return $this->redisConn->hget($this->incrKey, $docSha);
    }

    /**
     * @param array $info
     */
    protected function setDocInfo(array $info): void
    {
        $docSha = sha1($this->requestSource);
        $this->redisConn->hset($this->incrKey, $docSha, serialize($info));
    }

    /**
     * @param string $docInfo
     * @return bool
     */
    protected function updateDocInfo(string $docInfo): bool
    {
        $docArr                            = unserialize($docInfo);
        $docArr[IndexInterface::TIMESTAMP] = time();
        $docArr[IndexInterface::VERSION]   = ++$docArr[IndexInterface::VERSION];
        $this->stdFields->setVersion($docArr[IndexInterface::VERSION]);
        $this->stdFields->setId($docArr[IndexInterface::ID]);
        $this->setDocInfo($docArr);
        return true;
    }

    /**
     * @param array $fieldValue
     */
    protected function searchPhrase(array $fieldValue): void
    {
        $tStart = Timers::millitime();
        foreach ($fieldValue as $field => $phrase) {
            $this->words = explode(IndexInterface::SYMBOL_SPACE, $phrase);
            $cntWords    = count($this->words);
            foreach ($this->words as &$word) {
                $wordHash = md5($word);
                $hkey     = $this->hashIndexKey . $wordHash;
                $docs     = $this->redisConn->hvals($hkey);
                if ($cntWords > 1) { // intersect search (means search by phrase in each doc for every word)
                    $done = $this->setMatches($docs, $phrase);
                    if (true === $done) {
                        break 2;
                    }
                } else {
                    if ($cntWords === 1) {
                        $done = $this->setMatch($docs, $word);
                        if (true === $done) {
                            break 2;
                        }
                    }
                }
            }
            unset($this->docHashes);
        }
        $took = Timers::millitime() - $tStart;
        $this->stdFields->setTook($took);
        $this->stdFields->setHits($this->result);
        $this->stdFields->setTotal(count($this->result));
    }

    protected function deleteDocument(): void
    {
        $incrMatch = $this->incrKey . CoreInterface::HASH_INDEX_GLUE . IndexInterface::ID_DOC_MATCH;
        // save id -> key for fast delete/update ops
        $docHash = $this->redisConn->hget($incrMatch, $this->id);
        $docData = unserialize($this->redisConn->hget($this->incrKey, $docHash));
        $this->stdFields->setOpType(IndexInterface::RESULT_FOUND);
        $this->stdFields->setOpStatus(false);
        $this->stdFields->setResult(IndexInterface::RESULT_NOT_FOUND);
        if (empty($docData) === false) {
            $this->stdFields->setOpType(IndexInterface::RESULT_FOUND);
            $this->stdFields->setOpStatus(true);
            // loop through saved index_key_md5(word) list and delete them
            $this->redisConn->del($docData[IndexInterface::LIST_WORDS_KEY]);
            // loop through saved index:key:md5(word) hashes and delete them
            $this->redisConn->del($docData[IndexInterface::HASH_WORDS_KEY]);
            // delete doc match
            $this->redisConn->hdel($incrMatch, [$this->id]);
            // delete doc data
            $this->redisConn->hdel($this->incrKey, [$docHash]);
            $this->stdFields->setResult(IndexInterface::RESULT_DELETED);
            $this->stdFields->setVersion($docData[IndexInterface::VERSION]);
        }

        $this->stdFields->setIndex($this->index);
        $this->stdFields->setType($this->indexType);
        $this->stdFields->setId($this->id);
    }

    /**
     * Finds documents by phrase match
     * @param array  $docs   an array of index => document
     * @param string $phrase the phrase to search
     * @return bool true if limit has been reached, false otherwise
     */
    private function setMatches(array $docs, string $phrase): bool
    {
        foreach ($docs as &$doc) { // perf by ref
            $docHash = md5($doc); // for fast search duplicates only
            if (empty($this->docHashes[$docHash])
                && mb_strpos($doc, $phrase, null, CoreInterface::DEFAULT_ENCODING) !== false
            ) { // avoid doubling
                if (++$this->found < $this->offset) {
                    continue;
                }
                if ($this->found >= $this->limit) {
                    return true;
                }
                $resultArray               = Json::parse($doc);
                $this->result[]            = Highlighter::highlight($this, $resultArray, $phrase);
                $this->docHashes[$docHash] = 1;
            }
        }
        return false;
    }

    /**
     * Finds documents by word match
     * @param array  $docs
     * @param string $word
     * @return bool true if limit has been reached, false otherwise
     */
    private function setMatch(array $docs, string $word): bool
    {
        foreach ($docs as &$doc) { // perf by ref
            if (++$this->found < $this->offset) {
                continue;
            }
            if ($this->found >= $this->limit) {
                return true;
            }
            $resultArray    = Json::parse($doc);
            $this->result[] = Highlighter::highlight($this, $resultArray, $word);
        }
        return false;
    }

    /**
     *  Glues the index with indexType by glue :, if there is no indexType
     *  index will be appended by glue anyway to avoid redundant logic
     */
    private function setHashIndexKey(): void
    {
        $this->hashIndexKey = $this->index . (empty($this->indexType)
                ? self::HASH_INDEX_GLUE
                : (self::HASH_INDEX_GLUE . $this->indexType . self::HASH_INDEX_GLUE));
    }

    private function setIncrKey(): void
    {
        $this->incrKey = $this->index . (empty($this->indexType) ? ''
                : (self::HASH_INDEX_GLUE . $this->indexType . ''));
    }

    /**
     *  Glues the index with indexType by glue _-_-_, if there is no indexType
     *  index will be appended by glue anyway to avoid redundant logic
     */
    private function setListIndexKey(): void
    {
        $this->listIndexKey = $this->index . (empty($this->indexType)
                ? self::LIST_INDEX_GLUE
                : (self::LIST_INDEX_GLUE . $this->indexType . self::LIST_INDEX_GLUE));
    }

    private function setStdFields(): void
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

    public function getStdFields(): ?StdFields
    {
        return $this->stdFields;
    }

    /**
     * @param string $lKey
     * @return array
     */
    private function setIndexData(string $lKey): array
    {
        $data        = [];
        $wordIndices = [];
        $indices     = [];
        $docSha      = sha1($this->requestSource);
        $docShaData  = $this->redisConn->hget($this->incrKey, $docSha);
        if (empty($docShaData) === false) {
            $data = unserialize($docShaData);
        }

        $range = $this->redisConn->lrange($lKey, self::LRANGE_DEFAULT_START, self::LRANGE_DEFAULT_STOP);
        if (empty($range) === false) {
            $indices     = array_values($range);
            $wordIndices = empty($data[IndexInterface::WORD_INDICES]) ? $indices :
                array_diff($indices, $data[IndexInterface::WORD_INDICES]);
        }
        // insert new hashed doc with incr ID and DATA or fulfill _word_indices if there are more
        if (empty($data) || empty($wordIndices) === false) {
            $id        = $this->redisConn->incr($this->hashIndexKey);
            $t         = time();
            $data      = [
                IndexInterface::ID           => $id,
                IndexInterface::TIMESTAMP    => $t,
                IndexInterface::WORD_INDICES => $indices,
                IndexInterface::VERSION      => 1,
            ];
            $incrMatch = $this->incrKey . CoreInterface::HASH_INDEX_GLUE . IndexInterface::ID_DOC_MATCH;
            // save id -> key for fast delete/update ops
            $this->redisConn->hset($incrMatch, $id, $docSha);
            $this->redisConn->hset($this->incrKey, $docSha, serialize($data));
        }
        $this->stdFields->setId($data[IndexInterface::ID]);
        $this->stdFields->setTimestamp($data[IndexInterface::TIMESTAMP]);
        return $data;
    }

    protected function setRequestDocument(): void
    {
        $jsonArray                            = [];
        $jsonArray[IndexInterface::INDEX]     = $this->stdFields->getIndex();
        $jsonArray[IndexInterface::TYPE]      = $this->stdFields->getType();
        $jsonArray[IndexInterface::ID]        = $this->stdFields->getId();
        $jsonArray[IndexInterface::TIMESTAMP] = $this->stdFields->getTimestamp();
        $jsonArray[IndexInterface::SOURCE]    = $this->requestHandler->getRequestBodyArray();
        $this->requestDocument                = str_replace(
            self::DOUBLE_QUOTES, self::DOUBLE_QUOTES_ESC,
            serialize($jsonArray)
        );
    }

    /**
     *  Sets the only source doc from input stream
     */
    protected function setSourceDocument(): void
    {
        $jsonArray           = $this->requestHandler->getRequestBodyArray();
        $this->requestSource = str_replace(
            self::DOUBLE_QUOTES, self::DOUBLE_QUOTES_ESC,
            serialize($jsonArray)
        );
    }

    protected function setDictHashData(): void
    {
        $docSha     = sha1($this->requestSource);
        $docShaData = $this->redisConn->hget($this->incrKey, $docSha);
        $data       = unserialize($docShaData);

        $data[IndexInterface::LIST_WORDS_KEY] = $this->listWordKeys;
        $data[IndexInterface::HASH_WORDS_KEY] = $this->hashWordKeys;
        $this->redisConn->hset($this->incrKey, $docSha, serialize($data));
    }
}
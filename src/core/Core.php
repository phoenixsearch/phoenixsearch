<?php

namespace pheonixsearch\core;

use pheonixsearch\exceptions\RequestException;
use pheonixsearch\exceptions\UriException;
use pheonixsearch\helpers\Highlighter;
use pheonixsearch\helpers\Timers;
use pheonixsearch\types\CoreInterface;
use pheonixsearch\types\Errors;
use pheonixsearch\types\HttpBase;
use pheonixsearch\types\IndexInterface;

class Core extends BaseCore
{
    private $words = [];

    private $docHashes  = [];
    private $wordHashes = [];
    private $result     = [];

    private $listWordKeys = [];
    private $hashWordKeys = [];

    private $offset = 0;
    private $limit  = CoreInterface::DEFAULT_LIMIT;
    private $found  = 0; // incremented counter between phrases & words

    public $highlight = false;
    public $preTags   = '';
    public $postTags  = '';

    /**
     * Core constructor.
     *
     * @param RequestHandler $handler
     *
     * @throws UriException
     */
    protected function __construct(RequestHandler $handler)
    {
        parent::__construct($handler);
        if ($this->requestHandler->getRequestMethod() === HttpBase::HTTP_METHOD_GET
            || $this->requestHandler->getRequestMethod() === HttpBase::HTTP_METHOD_POST
        ) { // search
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
     * @param string $field
     */
    protected function insertWord(string $word, string $field): void
    {
        $wordHash = md5($field . $word);
        // prevent doubling repeated words
        if (empty($this->wordHashes[$wordHash])) {
            $this->wordHashes[$wordHash] = 1;
            $lKey                        = $this->listIndexKey . $wordHash;
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
     * @param string $docInfo
     *
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
                $wordHash = md5($field . $word);
                $hkey     = $this->hashIndexKey . $wordHash;
                $docs     = $this->redisConn->hvals($hkey);
                if ($cntWords > 1) { // intersect search (means search by phrase in each doc for every word)
                    $done = $this->setMatches($docs, $phrase, $field);
                    if (true === $done) {
                        break 2;
                    }
                } else { // if === 1
                    $done = $this->setMatch($docs, $word);
                    if (true === $done) {
                        break 2;
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

    /**
     * Searches document by uri routed ID
     */
    protected function searchById(): void
    {
        if (empty($this->id)) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_URI_DOC_EMPTY_ID], Errors::REQUEST_URI_DOC_EMPTY_ID);
        }
        $incrMatch = $this->incrKey . CoreInterface::HASH_INDEX_GLUE . IndexInterface::ID_DOC_MATCH;
        // get the document hash
        $docSha = $this->redisConn->hget($incrMatch, $this->id);
        // get serialized data
        $data = unserialize($this->redisConn->hget($this->incrKey, $docSha));
        if (empty($data)) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_URI_DOC_ID_NOT_FOUND], Errors::REQUEST_URI_DOC_ID_NOT_FOUND);
        }
        $this->stdFields->setOpType(IndexInterface::RESULT_FOUND);
        $this->stdFields->setOpStatus(true);
        $source = $this->unser($data[IndexInterface::SOURCE]);
        $this->stdFields->setSource($source);
        $this->stdFields->setId($data[IndexInterface::ID]);
        $this->stdFields->setVersion($data[IndexInterface::VERSION]);
        $this->stdFields->setTimestamp($data[IndexInterface::TIMESTAMP]);
    }

    /**
     *  Deletes all indices and data related to document ID
     */
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
            $this->decrInfo($this->stdFields);
        }

        $this->stdFields->setIndex($this->index);
        $this->stdFields->setType($this->indexType);
        $this->stdFields->setId($this->id);
    }

    /**
     *  Deletes all documents and related data in index:indexType or just index
     * @throws RequestException
     */
    protected function clearIndex()
    {
        $incrMatch = $this->incrKey . CoreInterface::HASH_INDEX_GLUE . IndexInterface::ID_DOC_MATCH;
        $matches = $this->redisConn->hgetall($incrMatch);
        if (empty($matches)) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_INDEX_NOT_FOUND], Errors::REQUEST_INDEX_NOT_FOUND);
        }
        foreach ($matches as $k => $v) {
            if (true === is_numeric($k)) { // id detected - not doc hash that mapped to id
                $this->id = $k;
                $this->deleteDocument();
            }
        }
    }

    /**
     * Finds documents by phrase match
     *
     * @param array  $docs   an array of index => document
     * @param string $phrase the phrase to search
     * @param string $field  for md5 field oriented search
     *
     * @return bool true if limit has been reached, false otherwise
     */
    private function setMatches(array $docs, string $phrase, string $field): bool
    {
        foreach ($docs as &$doc) { // perf by ref
            $docHash = md5($doc); // for fast search duplicates only
            if (empty($this->docHashes[$docHash])) { // avoid doubling
                $resultArray = $this->unser($doc);
                // search by defined field
                if (mb_strpos($resultArray[IndexInterface::SOURCE][$field], $phrase, null, CoreInterface::DEFAULT_ENCODING) !==
                    false
                ) {
                    if (++$this->found <= $this->offset) {
                        continue;
                    }
                    $this->result[]            = Highlighter::highlight($this, $resultArray, $phrase);
                    $this->docHashes[$docHash] = 1;
                    if ($this->found >= ($this->offset + $this->limit)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Finds documents by word match
     *
     * @param array  $docs
     * @param string $word
     *
     * @return bool true if limit has been reached, false otherwise
     */
    private function setMatch(array $docs, string $word): bool
    {
        foreach ($docs as &$doc) { // perf by ref
            if (++$this->found <= $this->offset) {
                continue;
            }
            if ($this->found > ($this->offset + $this->limit)) {
                return true;
            }
            $resultArray    = $this->unser($doc);
            $this->result[] = Highlighter::highlight($this, $resultArray, $word);
        }

        return false;
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
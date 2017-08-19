<?php

namespace pheonixsearch\core;

use pheonixsearch\exceptions\UriException;
use pheonixsearch\storage\RedisConnector;
use pheonixsearch\types\CoreInterface;
use pheonixsearch\types\Errors;
use pheonixsearch\types\IndexInterface;
use pheonixsearch\types\TypesInterface;
use Predis\Client;

class BaseCore implements CoreInterface
{
    use Serializer,
        Info;

    /** @var Client $redisConn */
    protected $redisConn = null;
    /** @var StdFields $stdFields */
    protected $stdFields = null;
    /** @var RequestHandler $requestHandler */
    public $requestHandler = null;

    protected $requestDocument = '';
    protected $requestSource = '';
    protected $hashIndexKey  = '';
    protected $listIndexKey  = '';
    protected $incrKey       = '';

    protected $routePath  = null;
    protected $routeQuery = null;

    protected $index     = '';
    protected $indexType = '';
    protected $id        = 0;

    /**
     * BaseCore constructor.
     * @param RequestHandler $handler
     * @throws UriException
     */
    protected function __construct(RequestHandler $handler)
    {
        $this->requestHandler = $handler;
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
     *  Glues the index with indexType by glue :, if there is no indexType
     *  index will be appended by glue anyway to avoid redundant logic
     */
    protected function setHashIndexKey(): void
    {
        $this->hashIndexKey = $this->index . (empty($this->indexType)
                ? self::HASH_INDEX_GLUE
                : (self::HASH_INDEX_GLUE . $this->indexType . self::HASH_INDEX_GLUE));
    }

    protected function setIncrKey(): void
    {
        $this->incrKey = $this->index . (empty($this->indexType) ? ''
                : (self::HASH_INDEX_GLUE . $this->indexType . ''));
    }

    /**
     *  Glues the index with indexType by glue _-_-_, if there is no indexType
     *  index will be appended by glue anyway to avoid redundant logic
     */
    protected function setListIndexKey(): void
    {
        $this->listIndexKey = $this->index . (empty($this->indexType)
                ? self::LIST_INDEX_GLUE
                : (self::LIST_INDEX_GLUE . $this->indexType . self::LIST_INDEX_GLUE));
    }

    protected function setStdFields(): void
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

    protected function setRequestDocument(): void
    {
        $jsonArray                            = [];
        $jsonArray[IndexInterface::INDEX]     = $this->stdFields->getIndex();
        $jsonArray[IndexInterface::TYPE]      = $this->stdFields->getType();
        $jsonArray[IndexInterface::ID]        = $this->stdFields->getId();
        $jsonArray[IndexInterface::TIMESTAMP] = $this->stdFields->getTimestamp();
        $jsonArray[IndexInterface::SOURCE]    = $this->requestHandler->getRequestBodyArray();
        $this->requestDocument                = $this->ser($jsonArray);
    }

    /**
     *  Sets the only source doc from input stream
     */
    protected function setSourceDocument(): void
    {
        $jsonArray           = $this->requestHandler->getRequestBodyArray();
        $this->requestSource = $this->ser($jsonArray);
    }

    /**
     *  Setting the dynamic index structure
     */
    protected function setCanonicalIndex(): void
    {
        $structure = $this->redisConn->hget($this->incrKey, IndexInterface::STRUCTURE);
        if (empty($structure)) {
            $jsonArray               = $this->requestHandler->getRequestBodyArray();
            $data[$this->index] = [
                IndexInterface::ALIASES  => [],
                IndexInterface::MAPPINGS => [$this->indexType => [IndexInterface::PROPERTIES => []]],
            ];
            foreach ($jsonArray as $field => $value) {
                $data[$this->index][IndexInterface::MAPPINGS][$this->indexType][IndexInterface::PROPERTIES] = [
                    $field => [
                        IndexInterface::FIELD_TYPE => TypesInterface::TEXT,
                        IndexInterface::FIELDS     => [
                            TypesInterface::WHITESPACE => [
                                IndexInterface::FIELD_TYPE   => TypesInterface::WHITESPACE,
                                IndexInterface::IGNORE_ABOVE => CoreInterface::DEFAULT_IGNORE,
                            ],
                        ],
                    ],
                ];
            }
            $this->redisConn->hset($this->incrKey, IndexInterface::STRUCTURE, $this->ser($data));
        }
    }

    /**
     * @param string $lKey
     *
     * @return array
     */
    protected function setIndexData(string $lKey): array
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
                IndexInterface::ID           => $id, // needed to use without serialization
                IndexInterface::TIMESTAMP    => $t, // needed to use without serialization
                IndexInterface::WORD_INDICES => $indices,
                IndexInterface::VERSION      => 1,
                IndexInterface::SOURCE       => $this->requestSource,
            ];
            $incrMatch = $this->incrKey . CoreInterface::HASH_INDEX_GLUE . IndexInterface::ID_DOC_MATCH;
            // save id -> key for fast delete/update ops
            $this->redisConn->hset($incrMatch, $id, $docSha);
            $this->redisConn->hset($this->incrKey, $docSha, serialize($data));
        }
        $this->setInfo($this->stdFields);
        $this->stdFields->setId($data[IndexInterface::ID]);
        $this->stdFields->setTimestamp($data[IndexInterface::TIMESTAMP]);

        return $data;
    }
}
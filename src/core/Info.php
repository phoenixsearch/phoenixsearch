<?php

namespace pheonixsearch\core;

use pheonixsearch\exceptions\RequestException;
use pheonixsearch\types\Errors;
use pheonixsearch\types\IndexInterface;
use pheonixsearch\types\InfoInterface;
use Predis\Client;

/**
 * Trait Info
 *
 * @package pheonixsearch\core
 *
 * @property Client redisConn
 */
trait Info
{
    /**
     * Sets system and index info
     * @param StdFields $stdFields
     *
     * @return bool
     */
    public function setInfo(StdFields $stdFields): bool
    {
        $index      = $stdFields->getIndex();
        $data       = [
            IndexInterface::INDEX       => $index,
            InfoInterface::DOCS_COUNT   => 1,
            InfoInterface::DOCS_DELETED => 0,
        ];
        $systemInfo = [
            InfoInterface::STORE_SIZE => InfoInterface::DEFAULT_USED_MEMORY,
        ];
        $hashData   = $this->redisConn->hget(InfoInterface::INFO_INDICES, $index);
        if (empty($hashData) === false) {
            $d                                     = $this->unser($hashData);
            $d[InfoInterface::DOCS_COUNT]          = ++$d[InfoInterface::DOCS_COUNT];
            $info                                  = $this->redisConn->info();
            $systemInfo[InfoInterface::STORE_SIZE] = $info[InfoInterface::MEMORY][InfoInterface::INFO_USED_MEMORY];
            $this->redisConn->hset(InfoInterface::INFO_INDICES, $index, $this->ser($d));
            $this->redisConn->hset(
                InfoInterface::INFO_INDICES, InfoInterface::SYSTEM_INFO, $this->ser(
                [
                    InfoInterface::STORE_SIZE => $info[InfoInterface::MEMORY][InfoInterface::INFO_USED_MEMORY],
                ]
            )
            );

            return false;
        }
        $this->redisConn->hset(InfoInterface::INFO_INDICES, InfoInterface::SYSTEM_INFO, $this->ser($systemInfo));
        $this->redisConn->hset(InfoInterface::INFO_INDICES, $index, $this->ser($data));

        return true;
    }

    /**
     * Decrement doc info on delete
     * @param StdFields $stdFields
     */
    public function decrInfo(StdFields $stdFields)
    {
        $index                          = $stdFields->getIndex();
        $hashData                       = $this->redisConn->hget(InfoInterface::INFO_INDICES, $index);
        $d                              = $this->unser($hashData);
        $d[InfoInterface::DOCS_DELETED] = ++$d[InfoInterface::DOCS_DELETED];
        $d[InfoInterface::DOCS_COUNT]   = --$d[InfoInterface::DOCS_COUNT];
        $info                           = $this->redisConn->info();
        $d[InfoInterface::STORE_SIZE]   = $info[InfoInterface::MEMORY][InfoInterface::INFO_USED_MEMORY];
        $this->redisConn->hset(
            InfoInterface::INFO_INDICES, InfoInterface::SYSTEM_INFO, $this->ser(
            [
                InfoInterface::STORE_SIZE => $info[InfoInterface::MEMORY][InfoInterface::INFO_USED_MEMORY],
            ]
        )
        );
        $this->redisConn->hset(InfoInterface::INFO_INDICES, $index, $this->ser($d));
    }

    public function getInfo(): array
    {
        $indicesInfo = $this->redisConn->hvals(InfoInterface::INFO_INDICES);
        foreach ($indicesInfo as $k => $val) {
            $indicesInfo[$k] = $this->unser($val);
        }

        return $indicesInfo;
    }

    public function getIndexInfo(): array
    {
        $indexInfo = $this->redisConn->hget($this->index, IndexInterface::STRUCTURE);
        if (empty($indexInfo)) {
            throw new RequestException(Errors::REQUEST_MESSAGES[Errors::REQUEST_INDEX_NOT_FOUND], Errors::REQUEST_INDEX_NOT_FOUND);
        }
        return $this->unser($indexInfo);
    }
}
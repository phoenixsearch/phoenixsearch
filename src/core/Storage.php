<?php

namespace pheonixsearch\core;

use pheonixsearch\exceptions\UriException;
use pheonixsearch\types\Errors;
use pheonixsearch\types\StorageInterface;

class Storage implements StorageInterface
{
    private $index      = '';
    private $indexType  = '';
    private $id         = 0;

    protected function __construct(string $routePath, string $routeQuery)
    {
        // parse index/type from path
        $pathArray = explode('/', $routePath);
        if (empty($pathArray[1]) === false) {
            $this->index     = $pathArray[1];
            $this->indexType = empty($pathArray[2]) ? '' : $pathArray[2];
            $this->id        = empty($pathArray[3]) ? 0 : $pathArray[3];
        } else {
            throw new UriException(Errors::REQUEST_MESSAGES[Errors::REQUEST_URI_EMPTY_INDEX], Errors::REQUEST_URI_EMPTY_INDEX);
        }
    }

    /**
     * Gets mappings md5(word) -> sha1(doc) mappings
     *
     * @param string $wordHash
     * @return array|string
     */
    protected function getMps(string $wordHash): array
    {
        $fileContent = $this->getMpContent($wordHash);
    }

    protected function getTps(string $wordHash): array
    {
        $fileContent = $this->getTpContent($wordHash);
    }

    protected function getIdx(array $hashedDocKeys): string
    {
        $fileContent = $this->getIdxContent($hashedDocKeys);
        foreach ($hashedDocKeys as $docKey) {

        }
    }

    private function getMpContent(string $wordHash): string
    {

    }

    private function getTpContent(string $wordHash): string
    {

    }

    private function getIdxContent(array $hashedDocKeys): string
    {

    }

    private function createIndex()
    {

    }
}
<?php

namespace pheonixsearch\core;

use pheonixsearch\types\StorageInterface;

class Storage implements StorageInterface
{
    private $storageDir = self::BASE_STORAGE_DIR;

    /**
     * Gets mappings md5(word) -> sha1(doc) mappings
     *
     * @param string $wordHash
     *
     * @return string
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
}
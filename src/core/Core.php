<?php

namespace pheonixsearch\core;

use pheonixsearch\types\EntryInterface;

class Core extends Storage
{
    private $hashedJson;
    private $routePath  = null;
    private $routeQuery = null;

    protected function __construct(array $uri, string $hashedJson)
    {
        $this->hashedJson = $hashedJson;
        $this->routePath  = $uri[EntryInterface::URI_PATH];
        $this->routeQuery = $uri[EntryInterface::URI_QUERY];
    }

    protected function insertWord(string $wordHash)
    {
        $mps = $this->getMps($wordHash);
        $tps = $this->getTps($wordHash);

    }

}
<?php

namespace pheonixsearch\core;

use pheonixsearch\types\IndexInterface;

class Search extends Core
{
    private $jsonArray       = null;
    private $searchStructure = [];

    public function __construct(RequestHandler $requestHandler)
    {
        $this->jsonArray = $requestHandler->getRequestBodyArray();
        parent::__construct($requestHandler);
    }

    public function buildSearch()
    {
        $this->searchPhrase();
    }

    private function parseStructure()
    {
        foreach ($this->jsonArray as $key => $value) { // ex.: name => Alice Hacker
            if ($key === IndexInterface::QUERY) {

            }
        }
    }
}
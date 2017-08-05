<?php

namespace pheonixsearch\core;

use pheonixsearch\types\IndexInterface;

class Search extends Core
{
    private $jsonArray = null;

    public function __construct(RequestHandler $requestHandler)
    {
        $this->jsonArray = $requestHandler->getRequestBodyArray();
        parent::__construct($requestHandler);
    }

    public function buildSearch()
    {
        $fieldValueMap = $this->parseStructure();
        $this->searchPhrase($fieldValueMap);
    }

    private function parseStructure()
    {
        $fieldValueMap = [];
        foreach ($this->jsonArray as $key => $value) { // ex.: name => Alice Hacker
            if ($key === IndexInterface::QUERY && empty($value[IndexInterface::TERM]) === false) {
                foreach ($value[IndexInterface::TERM] as $field => $val) {
                    $fieldValueMap[$field] = $val;
                }
            }
        }
        return $fieldValueMap;
    }
}
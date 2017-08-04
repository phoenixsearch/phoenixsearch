<?php

namespace pheonixsearch\core;

use pheonixsearch\types\IndexInterface;

class Index extends Core
{
    private $jsonArray = null;

    public function __construct(RequestHandler $requestHandler)
    {
        $this->jsonArray = $requestHandler->getRequestBodyArray();
        parent::__construct($requestHandler);
    }

    public function buildIndex()
    {
        foreach ($this->jsonArray as &$value) { // ex.: name => Alice Hacker
            $words = explode(IndexInterface::SYMBOL_SPACE, $value);
            foreach ($words as $word) {
                $this->insertWord($word);
            }
        }
    }
}
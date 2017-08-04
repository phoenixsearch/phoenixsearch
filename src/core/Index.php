<?php

namespace pheonixsearch\core;

use pheonixsearch\types\IndexInterface;

class Index extends Core
{
    private $jsonObject = null;

    public function __construct(RequestHandler $requestHandler)
    {
        $this->jsonObject = $requestHandler->getRequestBodyArray();
        parent::__construct($requestHandler);
    }

    public function buildIndex()
    {
        foreach ($this->jsonObject as $key => $value) { // ex.: name => Alice Hacker
            $words = explode(IndexInterface::SYMBOL_SPACE, $value);
            foreach ($words as $word) {
                $this->insertWord($word);
            }
        }
    }
}
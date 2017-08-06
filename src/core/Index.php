<?php

namespace pheonixsearch\core;

use pheonixsearch\helpers\Output;
use pheonixsearch\helpers\Timers;
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
        $tStart = Timers::millitime();
        foreach ($this->jsonArray as &$value) { // ex.: name => Alice Hacker
            $words = explode(IndexInterface::SYMBOL_SPACE, $value);
            foreach ($words as &$word) {
                $this->insertWord($word);
            }
        }
        $took = Timers::millitime() - $tStart;
        $stdFields = $this->getStdFields();
        $stdFields->setTook($took);
        $stdFields->setCreated(true);
        Output::jsonIndex($stdFields);
    }
}
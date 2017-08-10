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
        $this->setRequestDocument();
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
        $this->setDictHashData();
        $stdFields = $this->getStdFields();
        $stdFields->setTook($took);
        $stdFields->setOpType(IndexInterface::RESULT_CREATED);
        $stdFields->setOpStatus(true);
        $stdFields->setResult(IndexInterface::RESULT_CREATED);
        Output::jsonIndex($stdFields);
    }
}
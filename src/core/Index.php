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
        $stdFields = $this->getStdFields();
        $created = false;
        if ($this->checkSameDoc() !== null) { // insert
            foreach ($this->jsonArray as &$value) { // ex.: name => Alice Hacker
                $words = explode(IndexInterface::SYMBOL_SPACE, $value);
                foreach ($words as &$word) {
                    $this->insertWord($word);
                }
            }
            $this->setDictHashData();
            $stdFields->setResult(IndexInterface::RESULT_CREATED);
            $created = true;
        } else { // update

        }
        $took = Timers::millitime() - $tStart;
        $stdFields->setTook($took);
        $stdFields->setOpType(IndexInterface::RESULT_CREATED);
        $stdFields->setOpStatus($created);
        Output::jsonIndex($stdFields);
    }
}
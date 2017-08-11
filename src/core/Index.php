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
        $docInfo = $this->getDocInfo();
        if ($docInfo === null) { // insert
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
            $this->updateDocInfo($docInfo);
            $stdFields->setResult(IndexInterface::RESULT_UPDATED);
        }
        $took = Timers::millitime() - $tStart;
        $stdFields->setOpType(IndexInterface::RESULT_CREATED);
        $stdFields->setOpStatus($created);
        $stdFields->setTook($took);
        Output::jsonIndex($stdFields);
    }
}
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
        $this->setSourceDocument();
    }

    public function buildIndex()
    {
        $tStart = Timers::millitime();
        $stdFields = $this->getStdFields();
        $created = false;
        $docInfo = $this->getDocInfo();
        if ($docInfo === null) { // insert
            $this->setCanonicalIndex();
            $this->insert();
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

    public function transfer()
    {

    }

    /**
     *  Inserts data into inverted index pre-building property fields
     */
    private function insert(): void
    {
        foreach ($this->jsonArray as $field => &$value) { // ex.: name => Alice Hacker
            $words = explode(IndexInterface::SYMBOL_SPACE, $value);
            foreach ($words as &$word) {
                $this->insertWord($word, $field);
            }
        }
        $this->setDictHashData();
    }
}
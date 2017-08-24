<?php

namespace pheonixsearch\core;


use pheonixsearch\helpers\Output;
use pheonixsearch\helpers\Timers;

class Delete extends Core
{
    private $jsonArray = null;

    /**
     * Delete constructor.
     *
     * @param RequestHandler $requestHandler
     */
    public function __construct(RequestHandler $requestHandler)
    {
        $this->jsonArray = $requestHandler->getRequestBodyArray();
        parent::__construct($requestHandler);
    }

    /**
     * Deletes document by id
     */
    public function delete()
    {
        $tStart = Timers::millitime();
        $this->deleteDocument();
        $took = Timers::millitime() - $tStart;
        $stdFields = $this->getStdFields();
        $stdFields->setTook($took);
        Output::jsonIndex($stdFields);
    }

    /**
     *  Deletes all entities for particular index
     */
    public function deleteIndex()
    {
        $tStart = Timers::millitime();
        $this->clearIndex();
        $took = Timers::millitime() - $tStart;
        $stdFields = $this->getStdFields();
        $stdFields->setTook($took);
    }
}
<?php

namespace pheonixsearch\core;


use pheonixsearch\helpers\Output;
use pheonixsearch\helpers\Timers;

class Delete extends Core
{
    private $jsonArray = null;

    public function __construct(RequestHandler $requestHandler)
    {
        $this->jsonArray = $requestHandler->getRequestBodyArray();
        parent::__construct($requestHandler);
    }

    public function delete()
    {
        $tStart = Timers::millitime();
        $this->deleteDocument();
        $took = Timers::millitime() - $tStart;
        $stdFields = $this->getStdFields();
        $stdFields->setTook($took);
        Output::jsonIndex($stdFields);
    }
}
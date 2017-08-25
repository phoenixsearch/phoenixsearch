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
    public function delete(): void
    {
        $tStart = Timers::millitime();
        $this->deleteDocument();
        $took      = Timers::millitime() - $tStart;
        $stdFields = $this->getStdFields();
        $stdFields->setTook($took);
        Output::jsonIndex($stdFields);
    }

    /**
     *  Deletes all entities for particular index
     */
    public function deleteIndex(): void
    {
        exec('nohup /usr/bin/php -f ./src/commands/deleteIndex.php ' . $this->getStdFields()->getIndex()
            . ' ' . $this->getStdFields()->getType() . ' > /dev/null 2>&1 &');
        Output::out([
            'acknowledged' => true,
        ], $this->getStdFields());
    }

    public function clearAllIndexData(): void
    {
        $this->clearIndex();
    }
}
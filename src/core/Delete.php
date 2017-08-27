<?php

namespace pheonixsearch\core;


use pheonixsearch\helpers\Output;
use pheonixsearch\helpers\Timers;
use pheonixsearch\types\CoreInterface;
use pheonixsearch\types\DaemonInterface;
use pheonixsearch\types\IndexInterface;

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
        $msgKey = ftok(DaemonInterface::PID_FILE, CoreInterface::FTOK_PROJECT_NAME);
        $seg    = msg_get_queue($msgKey);
        msg_send($seg, CoreInterface::MSG_TYPE_DELETE_INDEX, [
            IndexInterface::INDEX => $this->getStdFields()->getIndex(),
            IndexInterface::TYPE  => $this->getStdFields()->getType(),
        ]);
        Output::out([
            'acknowledged' => true,
        ], $this->getStdFields());
    }

    public function clearAllIndexData(): void
    {
        $this->clearIndex();
    }
}
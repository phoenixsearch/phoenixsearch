<?php

namespace pheonixsearch\core;


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
        $this->deleteDocument();
    }
}
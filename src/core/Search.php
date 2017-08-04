<?php

namespace pheonixsearch\core;

class Search extends Core
{
    private $jsonObject = null;

    public function __construct(RequestHandler $requestHandler)
    {
        $this->jsonObject = $requestHandler->getRequestBodyArray();
        parent::__construct($requestHandler);
    }

    public function buildSearch()
    {
        $this->searchPhrase();
    }
}
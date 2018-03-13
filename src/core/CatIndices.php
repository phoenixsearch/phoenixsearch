<?php

namespace pheonixsearch\core;


use pheonixsearch\helpers\Output;

class CatIndices extends Core
{
    private $jsonArray = null;

    public function __construct(RequestHandler $requestHandler)
    {
        $this->jsonArray = $requestHandler->getRequestBodyArray();
        parent::__construct($requestHandler);
    }

    public function getCat()
    {
        $info = $this->getInfo();
        Output::jsonInfo($info, $this->getStdFields());
    }

    public function getCatIndex()
    {
        $info = $this->getIndexInfo();
        Output::jsonInfo($info, $this->getStdFields());
    }
}
<?php
namespace pheonixsearch\core;

class Index extends Core
{
    private $jsonObject = null;

    public function __construct(\stdClass $object)
    {
        $this->jsonObject = $object;
    }

    public function buildIndex()
    {

    }
}
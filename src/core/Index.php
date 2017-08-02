<?php
namespace pheonixsearch\core;

use pheonixsearch\types\IndexInterface;

class Index extends Core
{
    private $jsonObject     = null;

    public function __construct(array $uri, \stdClass $object, string $json)
    {
        $this->jsonObject     = $object;
        parent::__construct($uri, $object, $json);
    }

    public function buildIndex()
    {
        foreach ($this->jsonObject as $key => $value) { // ex.: name => Alice Hacker
            $words = explode(IndexInterface::SYMBOL_SPACE, $value);
            foreach ($words as $word) {
                $this->storeHashes($word);
            }
        }
    }

    private function storeHashes(string $word)
    {
        $this->insertWord($word);
    }

}
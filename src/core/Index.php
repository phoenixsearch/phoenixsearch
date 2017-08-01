<?php
namespace pheonixsearch\core;

use pheonixsearch\types\IndexInterface;

class Index extends Core
{
    private $jsonObject     = null;
    // stores the serialized document
    private $serializedJson = '';
    // store the serialized hashed document
    private $hashedJson     = '';

    public function __construct(array $uri, \stdClass $object)
    {
        $this->jsonObject     = $object;
        $this->serializedJson = serialize($this->jsonObject);
        $this->hashedJson     = sha1($this->serializedJson);
        parent::__construct($uri, $this->hashedJson);
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
        $wordHash = md5($word);
        // todo: construct mappings type_md5(word)->sha1(docs)
        $this->insertWord($wordHash);
        // todo: sha1-docs key to serialized doc
    }

}
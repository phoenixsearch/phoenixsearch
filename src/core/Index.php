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

    public function __construct(\stdClass $object)
    {
        $this->jsonObject     = $object;
        $this->serializedJson = serialize($this->jsonObject);
        $this->hashedJson     = sha1($this->serializedJson);
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
        // todo: files with name pair index_/_type (ES like)

        // todo: construct mappings md5-wods->sha1-docs

        // todo: sha1-docs key to serialized doc
    }
}
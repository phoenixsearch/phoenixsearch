<?php

namespace pheonixsearch\helpers;

use pheonixsearch\core\StdFields;
use pheonixsearch\types\IndexInterface;

class Output
{
    public static function jsonSearch(StdFields $stdFields)
    {
        $response = [
            IndexInterface::TOOK      => $stdFields->getTook(),
            IndexInterface::TIMED_OUT => $stdFields->isTimedOut(),
            IndexInterface::HITS      => [
                IndexInterface::TOOK => $stdFields->getTotal(),
                IndexInterface::HITS => $stdFields->getHits(),
            ],
        ];
        static::out($response, $stdFields);
    }

    public static function jsonIndex(StdFields $stdFields)
    {
        $response = [
            IndexInterface::TOOK    => $stdFields->getTook(),
            IndexInterface::INDEX   => $stdFields->getIndex(),
            IndexInterface::TYPE    => $stdFields->getType(),
            IndexInterface::CREATED => $stdFields->getIndex(),
            IndexInterface::ID      => $stdFields->getId(),
        ];
        static::out($response, $stdFields);
    }

    public static function out(array $response, StdFields $stdFields)
    {
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
        header('Content-Type: application/json');
        echo Json::encode($response, $stdFields->getOpts());
        exit(0);
    }
}
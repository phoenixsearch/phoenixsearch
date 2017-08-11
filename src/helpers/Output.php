<?php

namespace pheonixsearch\helpers;

use pheonixsearch\core\StdFields;
use pheonixsearch\types\IndexInterface;
use pheonixsearch\types\StdInterface;

class Output
{
    public static function jsonSearch(StdFields $stdFields)
    {
        $response = [
            IndexInterface::TOOK      => $stdFields->getTook(),
            IndexInterface::TIMED_OUT => $stdFields->isTimedOut(),
            IndexInterface::HITS      => [
                IndexInterface::TOTAL => $stdFields->getTotal(),
                IndexInterface::HITS  => $stdFields->getHits(),
            ],
        ];
        static::out($response, $stdFields);
    }

    public static function jsonIndex(StdFields $stdFields)
    {
        $response = [
            $stdFields->getOpType() => $stdFields->isOpStatus(),
            IndexInterface::TOOK    => $stdFields->getTook(),
            IndexInterface::INDEX   => $stdFields->getIndex(),
            IndexInterface::TYPE    => $stdFields->getType(),
            IndexInterface::ID      => $stdFields->getId(),
        ];
        if ($stdFields->isBitSet(StdInterface::BIT_RESULT)) {
            $response[IndexInterface::RESULT] = $stdFields->getResult();
        }
        if ($stdFields->isBitSet(StdInterface::BIT_VERSION)) {
            $response[IndexInterface::VERSION] = $stdFields->getVersion();
        }
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
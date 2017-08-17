<?php
namespace pheonixsearch\helpers;

use pheonixsearch\core\StdFields;
use pheonixsearch\types\IndexInterface;

class Output
{
    public static function jsonGetById(StdFields $stdFields): void
    {
        $response = [
            IndexInterface::INDEX   => $stdFields->getIndex(),
            IndexInterface::TYPE    => $stdFields->getType(),
            IndexInterface::ID      => $stdFields->getId(),
            IndexInterface::VERSION => $stdFields->getVersion(),
            $stdFields->getOpType() => $stdFields->isOpStatus(),
            IndexInterface::SOURCE  => $stdFields->getSource(),
        ];
        static::out($response, $stdFields);
    }

    public static function jsonSearch(StdFields $stdFields): void
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

    public static function jsonIndex(StdFields $stdFields): void
    {
        $response = [
            $stdFields->getOpType() => $stdFields->isOpStatus(),
            IndexInterface::TOOK    => $stdFields->getTook(),
            IndexInterface::INDEX   => $stdFields->getIndex(),
            IndexInterface::TYPE    => $stdFields->getType(),
            IndexInterface::ID      => $stdFields->getId(),
            IndexInterface::RESULT  => $stdFields->getResult(),
            IndexInterface::VERSION => $stdFields->getVersion(),
        ];
        static::out($response, $stdFields);
    }

    public static function jsonInfo(array $info, $stdFields): void
    {
        static::out($info, $stdFields);
    }

    public static function out(array $response, StdFields $stdFields): void
    {
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
        header('Content-Type: application/json');
        echo Json::encode($response, $stdFields->getOpts());
        exit(0);
    }
}
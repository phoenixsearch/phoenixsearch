<?php
require_once __DIR__ . '/vendor/autoload.php';

use pheonixsearch\core\Environment;
use pheonixsearch\helpers\Console;

if ($argc > 1) {
    $env = new Environment();
    $env->setEnvironment();
    $key    = $argv[1];
    $envKey = getenv('APP_KEY');
    if ((string)$key === (string)$envKey) {
        // opts with auth
        $daemon = new \pheonixsearch\core\Daemon();
        $daemon->run(\pheonixsearch\types\CoreInterface::PROCESS_TITLE);
    }
} else {
    Console::out('Usage: php phoenixsearchd.php <key>', Console::COLOR_YELLOW);
}
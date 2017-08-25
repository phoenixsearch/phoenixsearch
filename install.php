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

    }
} else {
    Console::out('Usage: php install.php <key>', Console::COLOR_YELLOW);
}
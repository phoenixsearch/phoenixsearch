<?php
require_once __DIR__ . '/vendor/autoload.php';

use pheonixsearch\core\Environment;
use pheonixsearch\helpers\Console;
use pheonixsearch\types\StorageInterface;

if ($argc > 1) {
    $env = new Environment();
    $env->setEnvironment();
    $key = $argv[1];
    $envKey = getenv('APP_KEY');
    if ((string) $key === (string) $envKey) {
        if (is_dir(StorageInterface::BASE_STORAGE_DIR) === false) {
            if (mkdir(StorageInterface::BASE_STORAGE_DIR,
                    StorageInterface::BASE_STORAGE_DIR_MODE, true) === false) {
                Console::out('Access forbidden', Console::COLOR_RED);
            } else {
                Console::out('Directory: ' . StorageInterface::BASE_STORAGE_DIR
                    . ' has been successfully created.', Console::COLOR_GREEN);
            }
        }
    }
} else {
    Console::out('Usage: php install.php <key>', Console::COLOR_YELLOW);
}
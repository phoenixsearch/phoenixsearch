<?php
if ($argc > 1) {
    require_once __DIR__ . '/../../vendor/autoload.php';
    \pheonixsearch\core\Environment::setEnvironment();
    putenv('APP_MODE=command');
    $handler = new \pheonixsearch\core\RequestHandler();
    $handler->setRequestMethod(\pheonixsearch\types\HttpBase::HTTP_METHOD_DELETE);
    $index = $argv[1];
    $type  = $argv[2] ?? '';
    $handler->setRoutePath('/' . $index . '/' . (($type === '') ? '' : '/' . $type . '/'));
    $del   = new \pheonixsearch\core\Delete($handler);
    $del->clearAllIndexData();
}
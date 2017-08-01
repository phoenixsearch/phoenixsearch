<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php';
use pheonixsearch\route\Entry;

(new Entry())->run();

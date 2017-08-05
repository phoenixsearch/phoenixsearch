<?php
namespace pheonixsearch\core;

class StdFields
{
    // time took to process in ms
    public $took    = 0;
    // index name
    public $index   = '';
    // index type
    public $type    = '';
    // external prioritized over internal
    public $id      = 0;

    public $score   = 0.0;
    // if new document created
    public $created = true;
    // whether time is out and stop process by returning results
    public $timedOut = false;
}
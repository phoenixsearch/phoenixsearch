<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.08.17
 * Time: 21:27
 */

namespace pheonixsearch\types;


interface CoreInterface
{
    const DEFAULT_ENCODING  = 'utf-8';
    const HASH_INDEX_GLUE   = ':';
    const LIST_INDEX_GLUE   = '___';
    const DOUBLE_QUOTES     = '"';
    const DOUBLE_QUOTES_ESC = '\"';

    const LRANGE_DEFAULT_START = 0;
    const LRANGE_DEFAULT_STOP  = -1;

    const JSON_PRETTY_PRINT = 'pretty';

    const DEFAULT_LIMIT = 10000;
}
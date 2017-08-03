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
    const HASH_INDEX_GLUE   = ':';
    const LIST_INDEX_GLUE   = '___';
    const DOUBLE_QUOTES     = '"';
    const DOUBLE_QUOTES_ESC = '\"';
}
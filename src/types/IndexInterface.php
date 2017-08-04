<?php
/**
 * Created by PhpStorm.
 * User: arthur
 * Date: 31/07/2017
 * Time: 12:10
 */

namespace pheonixsearch\types;

interface IndexInterface
{
    const SYMBOL_SPACE = ' ';

    // search query entities
    const QUERY = 'query';
    const TERM  = 'term';
}
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

    // response - StdFields
    const TOOK      = 'took';
    const TIMED_OUT = 'timed_out';
    const HITS      = 'hits';
    const TOTAL     = 'total';
    // system reserved keywords
    const INDEX     = '_index';
    const TYPE      = '_type';
    const SOURCE    = '_source';
    const CREATED   = '_created';
    const ID        = '_id';
}
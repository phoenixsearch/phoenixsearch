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
    // offset/limit
    const OFFSET    = 'offset';
    const LIMIT     = 'limit';
    // highlight settings
    const HIGHLIGHT = 'highlight';
    const PRE_TAGS  = 'pre_tags';
    const POST_TAGS = 'post_tags';
    // canonical values
    const FIELDS = 'fields';

    // response - StdFields
    const TOOK      = 'took';
    const TIMED_OUT = 'timed_out';
    const HITS      = 'hits';
    const TOTAL     = 'total';
    // system reserved keywords
    const INDEX          = '_index';
    const TYPE           = '_type';
    const SOURCE         = '_source';
    const ID             = '_id';
    const TIMESTAMP      = '_timestamp';
    const VERSION        = '_version';
    const ALL            = '_all';
    const RESULT         = 'result';

    // op results
    const RESULT_DELETED   = 'deleted';
    const RESULT_CREATED   = 'created';
    const RESULT_UPDATED   = 'updated';
    const RESULT_FOUND     = 'found';
    const RESULT_NOT_FOUND = 'not found';

    // arrays to hold hash keys
    const WORD_INDICES   = '_word_indices';
    const LIST_WORDS_KEY = '_list_words_key';
    const HASH_WORDS_KEY = '_hash_words_key';

    const ID_DOC_MATCH = 'MATCHING';
}
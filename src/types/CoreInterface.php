<?php

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

    const DEFAULT_LIMIT  = 10000;
    const DEFAULT_IGNORE = 0;

    const PROCESS_TITLE     = 'phoenixsearch';
    const FTOK_PROJECT_NAME = 'P';
    // System V IPC message types
    const MSG_TYPE_DELETE_INDEX = 1;
    const MSG_TYPE_REINDEX      = 2;

    const INDEX_HASH_PATTERN = '[a-z0-9]*';

    const DAEMON_TITLE_OS = 'Linux';
}
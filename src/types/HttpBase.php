<?php

namespace pheonixsearch\types;

interface HttpBase
{
    const HTTP_METHOD_POST   = 'POST';
    const HTTP_METHOD_GET    = 'GET';
    const HTTP_METHOD_PUT    = 'PUT';
    const HTTP_METHOD_PATCH  = 'PATCH';
    const HTTP_METHOD_DELETE = 'DELETE';

    const HTTP_GET_METHOD          = 'search';
    const HTTP_POST_METHOD         = 'index';
    const HTTP_PUT_METHOD          = 'update';
    const HTTP_DELETE_METHOD       = 'delete';
    const HTTP_INFO_METHOD         = 'info';
    const HTTP_INDEX_INFO_METHOD   = 'indexInfo';
    const HTTP_DELETE_INDEX_METHOD = 'deleteIndex';
    const HTTP_REINDEX_METHOD      = 'reindex';
}
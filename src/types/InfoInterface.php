<?php

namespace pheonixsearch\types;


interface InfoInterface
{
    const INFO_INDICES        = 'info:indices';
    const SYSTEM_INFO         = 'system_info';
    const DOCS_COUNT          = 'docs_count';
    const DOCS_DELETED        = 'docs_deleted';
    const STORE_SIZE          = 'store_size';
    const MEMORY              = 'Memory';
    const INFO_USED_MEMORY    = 'used_memory_human';
    const DEFAULT_USED_MEMORY = '1M';
}
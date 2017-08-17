<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16.08.17
 * Time: 19:12
 */

namespace pheonixsearch\types;


interface InfoInterface
{
    const INFO_INDICES        = 'info:indices';
    const DOCS_COUNT          = 'docs_count';
    const DOCS_DELETED        = 'docs_deleted';
    const STORE_SIZE          = 'store_size';
    const MEMORY              = 'Memory';
    const INFO_USED_MEMORY    = 'used_memory_human';
    const DEFAULT_USED_MEMORY = '!M';
}
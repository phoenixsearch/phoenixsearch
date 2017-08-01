<?php
/**
 * Created by PhpStorm.
 * User: arthur
 * Date: 01/08/2017
 * Time: 13:25
 */

namespace pheonixsearch\types;

interface StorageInterface
{
    const BASE_STORAGE_DIR      = '/var/lib/pheonixsearch/';
    const BASE_STORAGE_DIR_MODE = 0755;
    // file extensions
    const MP_FILE_EXT  = '.mp';
    const TP_FILE_EXT  = '.tp';
    const IDX_FILE_EXT = '.idx';
}
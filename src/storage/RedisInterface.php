<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.08.17
 * Time: 21:45
 */

namespace pheonixsearch\storage;


interface RedisInterface
{
    const REDIS_SCHEME = 'REDIS_SCHEME';
    const REDIS_HOST = 'REDIS_HOST';
    const REDIS_PORT = 'REDIS_PORT';

    const REDIS_CLUSTER = 'REDIS_CLUSTER';
    const REDIS_CLUSTER_PARAMS = 'REDIS_CLUSTER_PARAMS';
    const REDIS_CLUSTER_OPTIONS = 'REDIS_CLUSTER_OPTIONS';

    const REDIS_REPLICATION = 'REDIS_REPLICATION';
    const REDIS_REPLICATION_PARAMS = 'REDIS_REPLICATION_PARAMS';
    const REDIS_REPLICATION_OPTIONS = 'REDIS_REPLICATION_OPTIONS';

    const REDIS_PASSWORD = 'REDIS_PASSWORD';
}
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.08.17
 * Time: 21:37
 */

namespace pheonixsearch\storage;


use pheonixsearch\core\Environment;
use Predis\Client;

class RedisConnector implements RedisInterface
{
    /**
     * Returns the *Client* instance of this class.
     *
     * @staticvar Client $instance The *Singleton* instances of this class.
     *
     * @return Client The *Client* instance.
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            echo $scheme             = Environment::getEnv(self::REDIS_SCHEME);die;
            $host               = Environment::getEnv(self::REDIS_HOST);
            $port               = Environment::getEnv(self::REDIS_PORT);
            $isRedisCluster     = Environment::getEnv(self::REDIS_CLUSTER);
            $isRedisReplication = Environment::getEnv(self::REDIS_REPLICATION);

            if (true === $isRedisCluster) {
                $params   = Environment::getEnv(self::REDIS_CLUSTER_PARAMS);
                $options  = Environment::getEnv(self::REDIS_CLUSTER_OPTIONS);
                $instance = new Client($params, $options);
            } else if (true === $isRedisReplication) {
                $params   = Environment::getEnv(self::REDIS_REPLICATION_PARAMS);
                $options  = Environment::getEnv(self::REDIS_REPLICATION_OPTIONS);
                $instance = new Client($params, $options);
            } else { // default simple host connection
                $instance = new Client([
                    'scheme' => $scheme,
                    'host'   => $host,
                    'port'   => $port,
                ]);
            }
            $redisPassword = Environment::getEnv(self::REDIS_PASSWORD);
            if (null !== $redisPassword) {
                $instance->auth($redisPassword);
            }
        }

        return $instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }

    /**
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * @return void
     */
    private function __wakeup()
    {
    }
}
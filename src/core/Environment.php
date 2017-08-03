<?php

namespace pheonixsearch\core;

use Closure;

class Environment
{
    public function setEnvironment()
    {
        $fp = fopen('.env', 'r');
        while (($line = fgets($fp)) !== false) {
            if (strpos($line, '#') === false) {
                echo $line;
                putenv(str_replace(PHP_EOL, '', $line));
            }
        }
    }

    /**
     * Gets the value of an environment variable.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function getEnv($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default instanceof Closure ? $default() : $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        return $value;
    }
}
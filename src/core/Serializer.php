<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15.08.17
 * Time: 20:59
 */

namespace pheonixsearch\core;


use pheonixsearch\types\CoreInterface;

trait Serializer
{
    /**
     * @param array $data
     * @return string
     */
    protected function ser(array $data): string
    {
        return str_replace(
            CoreInterface::DOUBLE_QUOTES, CoreInterface::DOUBLE_QUOTES_ESC,
            serialize($data)
        );
    }

    /**
     * @param string $data
     * @return array
     */
    protected function unser(string $data): array
    {
        return unserialize(
            str_replace(
                CoreInterface::DOUBLE_QUOTES_ESC, CoreInterface::DOUBLE_QUOTES,
                $data)
        );
    }
}
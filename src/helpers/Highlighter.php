<?php

namespace pheonixsearch\helpers;


use pheonixsearch\core\Core;
use pheonixsearch\types\IndexInterface;

class Highlighter
{
    public static function highlight(Core $core, array $resultArray, string $phrase): array
    {
        if (true === $core->highlight) {
            $replacement = $core->preTags . $phrase . $core->postTags;
            if (array_key_exists(IndexInterface::ALL, $core->requestHandler->getHighlightFields())) {
                foreach ($resultArray as $f => &$text) {
                    $text = str_replace($phrase, $replacement, $text);
                }
            } else {
                foreach ($resultArray as $f => &$text) {
                    if (array_key_exists($f, $core->requestHandler->getHighlightFields()) === false) {
                        $text = str_replace($phrase, $replacement, $text);
                    }
                }
            }
        }
        return $resultArray;
    }
}
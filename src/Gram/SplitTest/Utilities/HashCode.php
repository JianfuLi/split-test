<?php

namespace Gram\SplitTest\Utilities;

/**
 * Class HashCode
 *
 * @package Gram\SplitTest\Utilities
 */
class HashCode
{
    static function getHashCode($str)
    {
        if (!is_string($str)) {
            $str = strval($str);
        }

        $h = 0;
        for ($i = 0, $n = strlen($str); $i < $n; $i++) {
            $h = $h * 31 + ord($str{$i});
            $h = $h & $h;
        }
        return $h & 2147483647; //转换为正数
    }
} 
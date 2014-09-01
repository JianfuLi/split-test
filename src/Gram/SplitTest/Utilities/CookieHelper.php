<?php

namespace Gram\SplitTest\Utilities;

/**
 * Class CookieHelper
 *
 * @package Gram\SplitTest\Utilities
 */
class CookieHelper
{
    static function get($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    static function set($name, $value = null, $expire = null, $path = null, $domain = null, $httpOnly = null)
    {
        setcookie(
            $name,
            $value,
            $expire,
            $path,
            $domain,
            false,
            $httpOnly
        );
    }

    static function remove($name)
    {
        setcookie(
            $name,
            null,
            time() - 86400
        );
    }
} 
<?php

namespace App\core\Cookie;

class Cookie implements CookieInterface
{
    public function set($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httpOnly = false): void
    {
        setcookie($key, $value, $expire, $path, $domain, $secure, $httpOnly);
    }
    public function get($key): string|array|null
    {
        return $_COOKIE[$key] ?? null;
    }
    public function delete($key, $path = '/', $domain = null, $secure = false, $httpOnly = true): void
    {
        setcookie($key, '', time() - 3600 * 24 * 30 * 12, $path, $domain, $secure, $httpOnly);
    }

    public function has(): array|false
    {
        if (!empty($_COOKIE)) return $_COOKIE;
        return false;
    }
}
<?php

namespace App\core\Cookie;

interface CookieInterface
{
    public function set($key, $value, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true): void;
    public function get($key): string|array|null;
    public function delete($key): void;
    public function has(): array|false;
}
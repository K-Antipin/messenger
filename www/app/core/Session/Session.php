<?php

namespace App\core\Session;

class Session implements SessionInterface
{
    public function __construct()
    {
        if (! isset($_SESSION)) session_start();
    }

    public function set($keys, $value = null): void
    {
        if (is_array($keys) || is_object($keys)) {
            foreach ($keys as $key => $val) {
                if ($key === 'hash' || $key === 'user_ip') continue;
                $_SESSION[$key] = $val;
            }
        }
        
        if (isset($value)) $_SESSION[$keys] = $value;
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function getFlash(string $key, $default = null)
    {
        $value = $this->get($key, $default);
        $this->remove($key);

        return $value;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        session_destroy();
    }
}

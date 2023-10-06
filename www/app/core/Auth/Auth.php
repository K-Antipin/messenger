<?php

namespace App\core\Auth;

class Auth implements AuthInterface
{
    public function check(): bool
    {
        return isset($_SESSION['auth']);
    }
}
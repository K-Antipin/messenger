<?php

namespace App\core\Auth;

interface AuthInterface
{
    public function check(): bool;
}
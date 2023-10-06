<?php

namespace App\core\Http;

interface RedirectInterface
{
    public function to(string $url);
}

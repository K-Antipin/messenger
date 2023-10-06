<?php

namespace App\core\View;

interface ViewInterface
{
    public function render($content_view, $template_view = null, $payload = null): void;
}
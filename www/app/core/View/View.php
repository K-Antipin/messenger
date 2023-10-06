<?php

namespace App\core\View;

use App\core\Auth\Auth;
use App\core\Session\Session;

class View implements ViewInterface
{
    public function __construct(
        private $session = new Session,
        private $auth = new Auth
    ) {
    }

    /**
     * Summary of render
     * @param mixed $content_view
     * @param mixed $template_view
     * @param mixed $payload
     * @return void
     */
    public function render($content_view, $template_view = null, $payload = null): void
    {
        if ($template_view) {
            include_once LAYOUT . $template_view;
        }
    }
}
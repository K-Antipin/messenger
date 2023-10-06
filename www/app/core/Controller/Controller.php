<?php

namespace App\core\Controller;

use App\core\Auth\Auth;
use App\core\View\View;
use App\core\Cookie\Cookie;
use App\core\Session\Session;
use App\core\Http\Request;
use App\core\Database\Database;

class Controller implements ControllerInterface
{
    public function __construct(
        protected $view = new View,
        protected $auth = new Auth,
        protected $session = new Session,
        protected $cookie = new Cookie,
        // protected $request = new Request,
        protected $database = new Database,
    ) {
    }

    public function index(): void
    {

    }
}
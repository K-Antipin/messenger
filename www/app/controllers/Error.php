<?php

namespace App\controllers;

use App\core\Controller\Controller;

class Error extends Controller
{
    public function index(): void
    {
        $this->view->render('error.phtml', 'template.phtml');
    }

}
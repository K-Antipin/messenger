<?php

namespace App\controllers;

use App\core\Controller\Controller;
use App\data\DB;
use stdClass;
use RedBeanPHP\R;
use RedBeanPHP\RedException;

class Home extends Controller
{
    public function index(): void
    {
        $payload = [];
        if ($this->auth->check()) {
            $payload['contacts'] = R::getAll(
                'SELECT `contacts`.`contact`, `users`.`email`, `users`.`name`, `users`.`nickname`, `users`.`display`, `users`.`avatar`, `users`.`id` FROM `contacts` INNER JOIN users ON (contacts.contact = users.id) WHERE `user` = ?',
                [
                    $this->cookie->get('id')
                ]
            );
            $payload['groups'] = R::getAll(
                'SELECT `groups`.`group_id`, `groups`.`user_id`, `users`.`email`, `users`.`nickname`, `users`.`display`, `users`.`avatar`, `users`.`id` FROM `groups` INNER JOIN users ON (groups.user_id = users.id) WHERE `user_id` = ?',
                [
                    $this->cookie->get('id')
                ]
            );
        }
        
        $this->view->render('home.phtml', 'template.phtml', $payload);
    }

    // public function about()
    // {
    //     $this->view->render('about.phtml', 'template.phtml');
    // }
    public function help(): void
    {
        $this->view->render('help.phtml', 'template.phtml');
    }

    public function search(): void
    {
        if (isset($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $contact = $this->database->findOne('users', [
                'email' => $_POST['search'],
                'nickname' => $_POST['search']
            ], 'email = :email OR nickname = :nickname');
            if (isset($contact)) {
                if ((bool) $contact['display'] && $_POST['search'] == $contact['email']) {
                    die(json_encode(['error' => 'not found']));
                } else {
                    die(json_encode([
                        'id' => $contact['id'],
                    ]));
                }
            }
            die(json_encode(['error' => 'not found']));
        }
    }


    public function contact(): void
    {
        if (isset($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $entity = new stdClass;
            $entity->user = $_SESSION['id'];
            $entity->contact = $_POST['contact'];
            $entity2 = new stdClass;
            $entity2->user = $_POST['contact'];
            $entity2->contact = $_SESSION['id'];
            $contacts = $this->database->create($entity, 'contacts');
            $contacts2 = $this->database->create($entity2, 'contacts');
            // var_dump($contacts);
        }
    }

    private function defaultData(): array
    {
        return [
            'session' => $this->session,
            'auth' => $this->auth
        ];
    }

    public function addgroup(): void
    {
        if ($this->auth->check() && $_POST['table'] === 'groups') {
            $payload = R::getAll(
                'SELECT `groups`.`group_id`, `groups`.`user_id`, `users`.`email`, `users`.`nickname`, `users`.`display`, `users`.`avatar`, `users`.`id` FROM `groups` INNER JOIN users ON (groups.user_id = users.id) WHERE `user_id` = ?',
                [
                    $this->cookie->get('id')
                ]
            );
            die(json_encode($payload));
        }
    }
}
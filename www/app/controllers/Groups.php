<?php

namespace App\controllers;

use App\core\Controller\Controller;
use App\data\DB;
use App\models\entities\Comment;
use App\models\entities\Group;
use RedBeanPHP\R;
use SebastianBergmann\Type\VoidType;
use stdClass;

class Groups extends Controller
{
    public function index(): void
    {
        // var_dump($this->database::count( 'groups' ) + 1);
        // die;
        $entity = new stdClass();
        $entity->group_id = $this->database::count( 'groups' ) + 1;
        $entity->user_id = $this->session->get('id');
        $id = $this->database->create($entity, 'groups');
        if ($id)
            header('Location: /');
        die('Неизвестная ошибка');
    }

    public function show($group_id): void
    {
        if (isset($_SESSION['auth']) && $_SESSION['auth']) {
            $groups = $this->database->getAll(
                'SELECT `groups`.`group_id`, `groups`.`user_id`, `users`.`email`, `users`.`nickname`, `users`.`display`, `users`.`avatar`, `users`.`id` FROM `groups` INNER JOIN users ON (groups.user_id = users.id) WHERE `group_id` = ?',
                [
                    $group_id[0]
                ]
            );

            $this->view->render('group.phtml', 'template.phtml', $groups);
        }
    }

    public function add(): void
    {
        if ($this->auth->check()) {
            $check = $this->database::find('groups', 'group_id = ?', [$_POST['group_id']]);
            foreach ($check as $item) {
                if ($item->user_id == $_POST['user_id']) {
                    die(json_encode(false));
                }
            }
            
            $obj = new stdClass;
            $obj->group_id = $_POST['group_id'];
            $obj->user_id = $_POST['user_id'];
            $id = $this->database->create($obj, 'groups');
            die(json_encode($id));
        }
    }
}
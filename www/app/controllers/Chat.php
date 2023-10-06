<?php

namespace App\controllers;

use App\core\Controller\Controller;
use stdClass;

// use App\data\DB;
class Chat extends Controller
{
    public function index(): void
    {
        if ($this->auth->check()) {
            // echo json_encode('dasdasdasda');
            $chats = $this->database::getAll(
                'SELECT `messenges`.*, `users`.`email`, `users`.`nickname`, `users`.`display` FROM `messenges` INNER JOIN users ON (messenges.from_id = users.id) WHERE (`to_id` = ? AND `from_id` = ?) OR (`to_id` = ? AND `from_id` = ?) ORDER BY `messenges`.`id` DESC',
                [
                    $_POST[0],
                    $_POST[1],
                    $_POST[1],
                    $_POST[0],
                ]
            );
            echo json_encode($chats);
        }
    }

    public function group(): void
    {
        if ($this->auth->check()) {
            $chats = $this->database::getAll(
                'SELECT `groupmess`.*, `users`.`email`, `users`.`nickname`, `users`.`display` FROM `groupmess` INNER JOIN users ON (groupmess.from_id = users.id) WHERE `group_id` = ? ORDER BY `groupmess`.`id` DESC',
                [
                    $_POST[1]
                ]
            );
            echo json_encode($chats);
            return;
        }
        var_dump($_POST);       
    }

    public function update(): void
    {
        if ($this->auth->check()) {
            $update = new stdClass;
            $update->id = $_POST['id'];
            $update->text = $_POST['text'];
            $table = $_POST['table'];

            die(json_encode($this->database->update($update ,$table)));
        }
    }

    public function delete(): void
    {
        // var_dump($_POST);
        if ($this->auth->check()) {
            $table = $_POST['table'];
            $ids = $_POST['id'];

            die(json_encode($this->database->delete($table, $ids)));
        }
    }
}
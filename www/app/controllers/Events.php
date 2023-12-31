<?php

namespace App\controllers;

use App\core\Controller\Controller;
use App\models\entities\Comment;
use App\models\entities\File;
use stdClass;

class Events extends Controller
{

    public function createMess(): void
    {
        if (
            !isset($_POST)
            || $_SERVER["REQUEST_METHOD"] !== "POST"
        ) {
            die(json_encode(['error' => 'Неизвестная ошибка']));
        }

        $entity = new stdClass();
        $entity->user_id = $_POST['userId'];
        $entity->text = $_POST['message'];
        $entity->img_id = $_POST['imgId'];
        $comment = new Comment($entity);
        $id = $this->database->create($comment, 'comment');
        die(json_encode(['mess' => $id]));
    }

    public function showMess(): void
    {
        // \var_dump($this->database->findAll('comment'));
        if (
            !isset($_POST)
            || $_SERVER["REQUEST_METHOD"] !== "POST"
        ) {
            die(json_encode(['error' => 'Неизвестная ошибка']));
        }
        $mess = $this->database->getAll("SELECT comment.comment, comment.id, comment.user_id, comment.created, users.name  FROM `comment` INNER JOIN users ON (comment.user_id = users.id) WHERE comment.img_id = ?", $_POST['imgId']);
        die(json_encode(['mess' => $mess]));
    }

    public function deleteMess(): void
    {
        if (
            !isset($_POST)
            || $_SERVER["REQUEST_METHOD"] !== "POST"
        ) {
            die(json_encode(['error' => 'Неизвестная ошибка']));
        }

        $id = $this->database->delete('comment', (int) $_POST['messId']);
        die(json_encode(['mess' => $id]));
    }

    public function createFiles(): void
    {

        if (
            !isset($_POST)
            || $_SERVER["REQUEST_METHOD"] !== "POST"
        ) {
            die(json_encode(['error' => 'Неизвестная ошибка']));
        }
        if (!\file_exists(UPLOAD_DIR))
            mkdir(UPLOAD_DIR, 0700);

        $arrId = [];

        for ($i = 0; $i < count($_FILES['files']['name']); $i++) {

            $fileName = $_FILES['files']['name'][$i];

            if ($_FILES['files']['size'][$i] > UPLOAD_MAX_SIZE || $_FILES['files']['error'][$i] === 1 || $_FILES['files']['error'][$i] === 2) {
                die(json_encode('Недопустимый размер файла ' . $fileName));
            }

            if (!in_array($_FILES['files']['type'][$i], ALLOWED_TYPES)) {
                die(json_encode('Недопустимый формат файла ' . $fileName));
            }

            $filePath = UPLOAD_DIR . DIRECTORY_SEPARATOR . basename($fileName);

            if (!move_uploaded_file($_FILES['files']['tmp_name'][$i], $filePath)) {
                die(json_encode('Ошибка загрузки файла ' . $fileName));
            } else {
                $entity = new stdClass();
                $entity->file_name = $fileName;
                $entity->user_id = $_COOKIE['id'];

                $file = new File($entity);
                $id = $this->database->create($file, 'images');
                $arrId[] = $id;
            }
        }

        die(json_encode(['mess' => $arrId]));
    }

    public function deleteFiles(): void
    {
        if (
            !isset($_POST)
            || $_SERVER["REQUEST_METHOD"] !== "POST"
        ) {
            die(json_encode(['error' => 'Неизвестная ошибка']));
        }
        $answer = [];
        $imgDelId = (int) $_POST['imgDelId'];
        $imgName = $this->database->findOne('images', [$imgDelId]);
        $answer['idImg'] = $this->database->delete('images', $imgDelId);
        $answer['idMess'] = $this->database->delete('comment', $imgDelId, 'img_id = ?');
        $answer['delFile'] = unlink(UPLOAD_DIR . \DIRECTORY_SEPARATOR . $imgName->file_name);
        die(\json_encode(['mess' => $answer]));
    }
}
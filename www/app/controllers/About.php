<?php

namespace App\controllers;

use App\core\Controller\Controller;
use App\data\DB;
use phpDocumentor\Reflection\Location;
use stdClass;
use RedBeanPHP\R;

/**
 * @var \App\core\Database\Database $database;
 */

class About extends Controller
{
    public function index(): void
    {
        if (!isset($_SESSION['auth']))
            header('Location: /');
        $payload = R::findOne('users', 'id = ?', [$this->session->get('id')]);
        $this->view->render('about.phtml', 'template.phtml', $payload);
    }

    public function update(): void
    {

        if (isset($_FILES['avatar'])) {
            
            if (!\file_exists(UPLOAD_DIR))
                mkdir(UPLOAD_DIR, 0700);

            $temp = explode('.', $_FILES['avatar']['name']);
            $fileName = $this->session->get('id') . '.' . $temp[count($temp) - 1];

            if ($_FILES['avatar']['size'] > UPLOAD_MAX_SIZE || $_FILES['avatar']['error'] === 1 || $_FILES['avatar']['error'] === 2) {
                die(json_encode(['warning' => 'Недопустимый размер файла, max 1 Mb']));
            }

            if (!in_array($_FILES['avatar']['type'], ALLOWED_TYPES)) {
                die(json_encode(['warning' => 'Недопустимый формат файла']));
            }

            $filePath = UPLOAD_DIR . DIRECTORY_SEPARATOR . basename($fileName);

            if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $filePath)) {
                die(json_encode(['warning' => 'Ошибка загрузки файла']));
            } else {
                $entity = new stdClass();
                $entity->id = $_SESSION["id"];
                $entity->avatar = $fileName;
                $id = $this->database->update($entity, 'users');
                if ($id)
                    die(json_encode(['avatar' => $fileName]));
            }
        }

        if (isset($_POST)) {
            $check = $this->database->findOne('users', 'nickname = ?', [$_POST['nickname']]);

            if (isset($check) && $check['id'] != $_SESSION['id'])
                die(json_encode(['error' => 'Логин занят']));

            $entity = new stdClass();
            $entity->id = $_SESSION["id"];
            foreach ($_POST as $k => $v) {
                if ($k != 'checkbox') {
                    $entity->$k = $v;
                }
                if ($k == 'checkbox' && $v == 'on') {
                    $entity->display = 1;
                    $_SESSION['display'] = 1;
                }
                if ($k == 'notification' && $v == 'on') {
                    $entity->notification = 1;
                    $_SESSION['notification'] = 1;
                }
            }
            if (!empty($entity->nickname))
                $_SESSION['nickname'] = $entity->nickname;
            if (!array_key_exists('checkbox', $_POST))
                $entity->display = 0;
            if (!array_key_exists('notification', $_POST))
                $entity->notification = 0;
            $id = $this->database->update($entity, 'users');
            if ($id)
                echo (json_encode(['nickname' => $_POST['nickname']]));
        }
    }

    public function delete($payload): void
    {
        if(file_exists(UPLOAD_DIR . DIRECTORY_SEPARATOR . $payload[0])) {
            unlink(UPLOAD_DIR . DIRECTORY_SEPARATOR . $payload[0]);
            $this->database->update([
                'avatar' => null,
                'id' => $this->session->get('id')
            ], 'users');
        }
        
        header('Location: /about');
    }
    
}
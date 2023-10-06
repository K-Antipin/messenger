<?php

namespace App\controllers;

use App\core\Controller\Controller;
// use App\data\DB;
use App\models\entities\User;
use stdClass;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\HtmlFormatter;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use RedBeanPHP\R;
use RedBeanPHP\RedException;

class Admin extends Controller
{
    public function index(): void
    {
        if (!$this->auth->check())
            header('Location: /');

        $this->view->render('user/users.phtml', 'template.phtml', 'admin');
    }

    //не реализовано
    public function add(): void
    {
        $this->view->render('user/add.phtml', 'template.phtml');
    }

    //не реализовано
    public function create(): void
    {
        if (
            !isset($_POST)
            || $_SERVER["REQUEST_METHOD"] !== "POST"
        ) {
            header('Location: /admin/add');
        }

        $entity = new stdClass();
        $entity->username = $_POST['username'];
        $entity->email = $_POST['email'];
        $entity->role = $_POST['role'];
        $userId = $this->database->create($entity, 'users');
        if ($userId) {
            header('Location: /admin');
        }
    }
    public function show($data): void
    {
        if (!empty($data) && intval($data[0])) {
            $id = $data[0];
            $payload = $this->database->findOne('users', [$id]);
        }

        if (!isset($payload) || $payload['id'] === 0) {
            header('Location: /error');
        }
        $this->view->render('user/show.phtml', 'template.phtml', $payload);
    }



    public function loginIn(): void
    {
        if (isset($_POST["token"]) && ($_POST["token"] == $_SESSION["CSRF"])) {
            if (!empty($_POST['login']) && !empty($_POST['password'])) {
                $user = $this->database->findOne('users', 'email = ?', [$_POST['login']]);
                $hash = $this->generateCode();
                if (!isset($user)) {
                    $log = new Logger('mylogger');
                    $log->pushHandler(new StreamHandler('mylog.log', Logger::INFO));
                    $log->info('Auth error', ['mess' => 'Пользователь не найден', 'login' => $_POST['login'], 'password' => $_POST['password']]);
                    header('Location: /?auth=notFound');
                } else if (password_verify($_POST['password'], $user->password)) {
                    $obj = new stdClass;
                    $obj->id = $user->id;
                    $obj->user_hash = $hash;
                    $obj->user_ip = ip2long($_SERVER['REMOTE_ADDR']);
                    $obj->updated = time();
                    $this->database->update($obj, 'users');
                    $this->cookie->set("id", $user['id'], time() + 60 * 60 * 24 * 30, "/");
                    if (isset($_POST['save']) && $_POST['save'] === 'on')
                        $this->cookie->set('hash', $hash, time() + 60 * 60 * 24 * 30, '/', $_SERVER['SERVER_NAME'], false, true);
                    $this->session->set($user);
                    $this->session->set('auth', true);
                    $this->session->set('nickname', (bool) $user['display'] ? $user['nickname'] : $user['email']);
                    $this->session->set('hash', $hash);
                    header('Location: /');
                } else {
                    $log = new Logger('mylogger');
                    $log->pushHandler(new StreamHandler('mylog.log', Logger::INFO));
                    $log->info('Auth error', ['mess' => 'Не верный пароль', 'login' => $_POST['login'], 'password' => $_POST['password']]);
                    header('Location: /?auth=passError');
                }
            } else {
                $log = new Logger('mylogger');
                $log->pushHandler(new StreamHandler('mylog.log', Logger::INFO));
                $log->info('Auth error', ['mess' => 'Неизвестная ошибка', 'login' => $_POST['login'], 'password' => $_POST['password']]);
                header('Location: /?auth=error');
            }
        }
    }

    public function register(): void
    {
        // die(json_encode($_POST));
        if (isset($_POST["token"]) && ($_POST["token"] == $_SESSION["CSRF"])) {
            // die(json_encode($_POST));
            // var_dump($_POST);
            if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['passwordConfirm'])) {
                // var_dump($_POST);
                $LoginExists = $this->database->findOne('users', 'email = ?', [$_POST['email']]);
                // var_dump($LoginExists);
                if (!isset($LoginExists)) {
                    // die(json_encode(["error" => "Все ок" , $_POST]));
                    if ($_POST['password'] !== $_POST['passwordConfirm']) {
                        header('Location: /?register=pass&name=' . $_POST['name'] . (isset($_POST['nickname']) ? '&nickname=' . $_POST['nickname'] : '') . '&email=' . $_POST['email']);
                    }
                    $hash = $this->generateCode();
                    $user = new stdClass;
                    $user->name = $_POST['name'];
                    $user->nickname = $_POST['nickname'];
                    $user->email = $_POST['email'];
                    $user->role = 'user';
                    $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $user->user_hash = $hash;
                    $user->user_ip = ip2long($_SERVER['REMOTE_ADDR']);
                    $user->created = time();
                    $id = $this->database->create($user, 'users');
                    if ($id) {
                        setcookie("id", $id, time() + 60 * 60 * 24 * 30, "/");
                        if (isset($_POST['save']) && $_POST['save'] === 'on')
                            setcookie('hash', $hash, time() + 60 * 60 * 24 * 30, '/', $_SERVER['SERVER_NAME'], false, true);
                        // var_dump($user);
                        $this->session->set($user);
                        $this->session->set('id', $id);
                        $this->session->set('hash', $hash);
                        $this->session->set('nickname', $user->email);
                        $this->session->set('auth', true);
                        $this->send_email($user->email);
                        header('Location: /');
                    } else {
                        die(json_encode(['error' => 'Неизвестная ошибка']));
                    }
                } else {
                    header('Location: /?register=exists');
                }
            } else {
                die(json_encode(['error' => 'Неизвестная ошибка']));
            }
        }
    }

    public function registerVk(): void
    {
        // die;
        // Параметры приложения
        $clientId = '51624627'; // ID приложения
        $clientSecret = 'ExYGMabGKhn6iy3Gh9jj'; // Защищённый ключ
        $redirectUri = 'http://application.local/admin/registerVk'; // Адрес, на который будет переадресован пользователь после прохождения авторизации
        $version = '5.126'; // Адрес, на который будет переадресован пользователь после прохождения авторизации

        // Формируем ссылку для авторизации
        $params = array(
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'v' => $version,
            'scope' => 'email,offline',
        );

        if (empty($_GET['code'])) {
            \header('Location: http://oauth.vk.com/authorize?' . http_build_query($params));
        }


        $params = array(
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $_GET['code'],
            'redirect_uri' => $redirectUri
        );

        if (!$content = @file_get_contents('https://oauth.vk.com/access_token?' . http_build_query($params))) {
            $error = error_get_last();
            throw new Exception('HTTP request failed. Error: ' . $error['message']);
        }

        $response = json_decode($content);

        // Если при получении токена произошла ошибка
        if (isset($response->error)) {
            throw new Exception('При получении токена произошла ошибка. Error: ' . $response->error . '. Error description: ' . $response->error_description);
        }
        // \var_dump($response);
        if (!empty($response->access_token)) {
            $params = array(
                'v' => $version,
                'uids' => $response->user_id,
                'access_token' => $response->access_token,
            );

            $info = file_get_contents('https://api.vk.com/method/users.get?' . http_build_query($params));
            $info = json_decode($info);

            $LoginExists = $this->database->findOne('users', 'email = ?', [$response->email]);
            $hash = $this->generateCode();

            if (!isset($LoginExists)) {
                $obj = new stdClass;
                $obj->name = $info->response[0]->first_name;
                $obj->nickname = '';
                $obj->email = $response->email;
                $obj->role = 'VK';
                $obj->password = password_hash($response->access_token, PASSWORD_DEFAULT);
                $obj->user_hash = $hash;
                $obj->user_ip = ip2long($_SERVER['REMOTE_ADDR']);
                $obj->created = time();
                $id = $this->database->create($obj, 'users');
                if ($id) {
                    setcookie("id", $id, time() + 60 * 60 * 24 * 30, "/");
                    setcookie('hash', $hash, time() + 60 * 60 * 24 * 30, '/', $_SERVER['SERVER_NAME'], false, true);
                    $this->session->set($obj);
                    $this->session->set('id', $id);
                    $this->session->set('hash', $hash);
                    $this->session->set('nickname', $obj->email);
                    $this->session->set('auth', true);
                    $this->send_email($obj->email);
                    \header('Location: /');
                } else {
                    die(json_encode(['error' => 'Неизвестная ошибка']));
                }
            } else {
                $obj = new stdClass;
                $obj->id = $LoginExists['id'];
                $obj->user_hash = $hash;
                $obj->user_ip = ip2long($_SERVER['REMOTE_ADDR']);
                $obj->updated = time();
                $this->database->update($obj, 'users');
                setcookie("id", $LoginExists['id'], time() + 60 * 60 * 24 * 30, "/");
                setcookie('hash', $hash, time() + 60 * 60 * 24 * 30, '/', $_SERVER['SERVER_NAME'], false, true);
                $this->session->set($LoginExists);
                $this->session->set('hash', $hash);
                $this->session->set('nickname', $LoginExists['display'] ? $LoginExists['nickname'] : $LoginExists['email']);
                $this->session->set('auth', true);
                \header('Location: /');
            }
        }
    }

    public function send_email($email): void
    {
        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';

        // Настройки SMTP
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 0;

        $mail->Host = 'ssl://smtp.yandex.ru';
        $mail->Port = 465;
        $mail->Username = 'mymessenger123@yandex.ru';
        $mail->Password = 'jrnxtrjoqpcqpuuj';

        // От кого
        $mail->setFrom('mymessenger123@yandex.ru');

        // Кому
        $mail->addAddress($email);

        // Тема письма
        $mail->Subject = 'Поздравляем с регистрацией!';

        // Тело письма
        $body = '<p><strong>Поздравляем с регистрацией!</strong></p>';
        $mail->msgHTML($body);

        $mail->send();
    }

    public function exit(): void
    {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        setcookie('id', '', time() - 3600 * 24 * 30 * 12, '/');
        setcookie('hash', '', time() - 3600 * 24 * 30 * 12, '/', $_SERVER['SERVER_NAME'], false, true);
        header('Location: /');
    }

    private function generateCode(): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < 10) {
            $code .= $chars[mt_rand(0, $clen)];
        }
        return $code;
    }
}
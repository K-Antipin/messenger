<?php

use Workerman\Worker;
use Workerman\Lib\Timer;
use App\core\Database\Database;
use App\data\DB;
use App\models\entities\Comment;


# Подключаем библиотеку Workerman
require_once __DIR__ . '/vendor/autoload.php';

$connections = []; // сюда будем складывать все подключения
$userId = '';

// Стартуем WebSocket-сервер на порту 27800
$worker = new Worker("websocket://0.0.0.0:8888");

$worker->onConnect = function ($connection) use (&$connections): void {
    // Эта функция выполняется при подключении пользователя к WebSocket-серверу
    $connection->onWebSocketConnect = function ($connection) use (&$connections) {

        // var_dump(Database::testConnection());
        // Достаём имя пользователя, если оно было указано
        if (isset($_GET['userId'])) {
            $user = DB::findOne('users', [$_GET['userId']]);
        }


        if ($user) {
            // Проверяем уникальность имени в чате
            $userNickname = (bool) $user->display ?  $user->nickname : $user->email;

            // Добавляем соединение в список
            $connection->userName = $user->name;
            $connection->userNickname = $userNickname;
            $connection->userId = $user->id;
            $connection->userEmail = $user->email;
            $connection->userNotification = (bool) $user->notification;
            // $connection->connectionId = $connection->id;

            $connections[$user->id] = $connection;

            // Собираем список всех пользователей
            $users = [];
            foreach ($connections as $c) {
                $users[] = [
                    'userId' => $c->userId,
                    'connectionId' => $c->id,
                ];
            }

            // Отправляем пользователю данные авторизации
            $messageData = [
                'action' => 'Authorized',
                'userId' => $connection->userId,
                'userName' => $connection->userName,
                'userNickname' => $connection->userNickname,
                'userEmail' => $connection->userEmail,
                'userNotification' => $connection->userNotification,
                'users' => $users
            ];
            $connection->send(json_encode($messageData));

            // Оповещаем всех пользователей о новом участнике в чате
            $messageData = [
                'action' => 'Connected',
                'userId' => $connection->userId,
                'connectionId' => $connection->id,
                'userName' => $connection->userName,
                'userNickname' => $connection->userNickname,
                'userNotification' => $connection->userNotification,
                'userEmail' => $connection->userEmail
            ];
            $message = json_encode($messageData);

            foreach ($connections as $c) {
                $c->send($message);
            }
        }
    };
};

$worker->onClose = function ($connection) use (&$connections): void {
    // Эта функция выполняется при закрытии соединения
    if (!isset($connections[$connection->userId])) {
        return;
    }

    // Удаляем соединение из списка
    unset($connections[$connection->userId]);

    // Оповещаем всех пользователей о выходе участника из чата
    $messageData = [
        'action' => 'Disconnected',
        'userId' => $connection->userId,
        'userNickname' => $connection->userNickname,
        'userEmail' => $connection->userEmail
    ];
    $message = json_encode($messageData);

    foreach ($connections as $c) {
        $c->send($message);
    }
};

$worker->onMessage = function ($connection, $message) use (&$connections): void {

    $messageData = json_decode($message, true);
    var_dump($messageData);
    // $messageData['userNickname'] = $connection->userNickname;

    // Преобразуем специальные символы в HTML-сущности в тексте сообщения
    $messageData['text'] = htmlspecialchars($messageData['text']);
    // Заменяем текст заключенный в фигурные скобки на жирный
    // (позже будет описано зачем и почему)
    $messageData['text'] = preg_replace('/\{(.*)\}/u', '<b>\\1</b>', $messageData['text']);

    if (isset($connections[$messageData['toUserId']])) {
        // Отправляем приватное сообщение указанному пользователю
        $connections[$messageData['toUserId']]->send(json_encode($messageData));
    }

    if ($messageData['action'] === 'PrivateMessage') {
        $entity = new stdClass();
        $entity->to_id = $messageData['toUserId'];
        $entity->from_id = $messageData['fromUserId'];
        $entity->text = $messageData['text'];
        $comment = new Comment($entity);

        $id = DB::create($comment, 'messenges');
    }

    if ($messageData['action'] === 'GroupMessage') {
        $users = DB::find(
            'groups',
            'group_id = ?',
            [
                $messageData['groupId']
            ]
        );

        var_dump($users);
        foreach ($users as $user) {
            if (isset($connections[$user['user_id']])) {
                if ($user['user_id'] == $messageData['fromUserId']) continue;
                $connections[$user['user_id']]->send(json_encode($messageData));
            }
        }

        $entity = new stdClass();
        $entity->from_id = $messageData['fromUserId'];
        $entity->group_id = $messageData['groupId'];
        $entity->text = $messageData['text'];

        $id = DB::create($entity, 'groupmess');
    }

};

Worker::runAll();
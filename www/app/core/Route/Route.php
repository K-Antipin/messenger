<?php

namespace App\core\Route;

define('CONTROLLERS_NAMESPACE', 'App\\controllers\\');

use App\core\Http;

class Route implements RouteInterface
{
    public static function start(): void
    {
        // \var_dump(DB::findAll('users'));
        // значения по умолчанию
        $controllerClassname = 'home';
        $payload = [];

        // разбиваем адресную строку на серию запросов в формате /контроллер/действие/дополнительные/параметры/запроса
        $routes = explode('/', $_SERVER["REQUEST_URI"]);
        // var_dump($routes);
        if (str_contains($routes[1], '?'))
            $routes[1] = strstr($routes[1], '?', true);

        // проверяем, указан ли контроллер и перезаписываем значение по умолчанию
        if (!empty($routes[1])) {
            $controllerClassname = $routes[1];
        }
        // \var_dump($controllerClassname);

        // проверяем, указано ли действие и перезаписываем значение по умолчанию
        $actionName = empty($routes[2]) ? 'index' : $routes[2];
        // var_dump($actionName);

        // проверяем, указаны ли доп. параметры и перезаписываем значение по умолчанию
        if (!empty($routes[3])) {
            $payload = array_slice($routes, 3);
        }

        // создаём контроллер с указанием пространства имён (для автолоудера)
        $controllerName = CONTROLLERS_NAMESPACE . ucfirst(strtolower($controllerClassname));
        // \var_dump($controllerName);

        // создаём строку с предполагаемым названием файла нужного нам контроллера
        $controllerFile = ucfirst(strtolower($controllerClassname)) . '.php';
        // var_dump($controllerFile);

        // создаём строку с указанием пути к файлу контроллера
        $controller_path = CONTROLLER . $controllerFile;
        // \var_dump($controller_path);
        // die;

        // проверяем наличие файла по данному пути и подключаем этот файл, если он есть
        // var_dump($controller_path);
        if (file_exists($controller_path)) {
            include_once $controller_path;
        } else {
            // var_dump($routes);
            // die;
            Route::Error(); // иначе, выводим сообщение об ошибке
        }

        // создаём экземпляр контроллера
        $controller = new $controllerName();

        // используем переменную method для удобства. Этот шаг необязателен
        $method = strtok($actionName, '?') ? strtok($actionName, '?') : $actionName;

        // проверяем наличие метода в классе контроллера
        if (method_exists($controller, $method)) {
            $controller->$method($payload); // запуск метода = функции
        } else {
            var_dump($controller, $method, $payload);
            die;
            Route::Error(); // иначе, выводим сообщение об ошибке
        }
    }

    // метод перенаправления на страницу ошибки
    public static function Error(): void
    {
        header('Location: /error');
    }

    // public static function check(): void
    // {
    //     die;
    //     if (!isset($_SESSION['auth'])) {
    //         setcookie('id', '', time() - 3600 * 24 * 30 * 12, '/');
    //         setcookie('hash', '', time() - 3600 * 24 * 30 * 12, '/', $_SERVER['SERVER_NAME'], false, true);
    //         $_SESSION = array();
    //         if (ini_get("session.use_cookies")) {
    //             $params = session_get_cookie_params();
    //             setcookie(
    //                 session_name(),
    //                 '',
    //                 time() - 42000,
    //                 $params["path"],
    //                 $params["domain"],
    //                 $params["secure"],
    //                 $params["httponly"]
    //             );
    //         }
    //         session_destroy();
    //         header('Location: /');
    //     }
    // }
}
# Модуль 33. Messenger. Практическая работа.

## Используемые технологии
* HTML
* PHP
* Docker
* SQL
* Composer

P.S. Какоето время сайт недоступен, пока composer устанавливает зависимости.

### Для запуска необходимо:
1. Установить Docker
2. Установить Docker Compose
3. Звпустить установщик контейнеров из терминала `docker-compose up -d`
4. Восстановить базу данных из дампа в корне приложения (не обязательно)
5. Зайти в контейнер `php` и ввести комманду `php server.php start` или `php server.php start -d` для `deamon mode`
6. Чтобы приложение было доступно по адрессу application.local, его нужно внести в hosts `127.0.0.1 application.local`
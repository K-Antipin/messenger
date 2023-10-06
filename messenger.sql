-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: db:3306
-- Время создания: Окт 05 2023 г., 15:05
-- Версия сервера: 8.1.0
-- Версия PHP: 8.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `messenger`
--

-- --------------------------------------------------------

--
-- Структура таблицы `contacts`
--

CREATE TABLE `contacts` (
  `id` int UNSIGNED NOT NULL,
  `user` int UNSIGNED DEFAULT NULL,
  `contact` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Дамп данных таблицы `contacts`
--

INSERT INTO `contacts` (`id`, `user`, `contact`) VALUES
(1, 6, 3),
(2, 3, 6),
(3, 6, 9);

-- --------------------------------------------------------

--
-- Структура таблицы `groupmess`
--

CREATE TABLE `groupmess` (
  `id` int UNSIGNED NOT NULL,
  `group_id` int UNSIGNED DEFAULT NULL,
  `from_id` int UNSIGNED DEFAULT NULL,
  `text` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Дамп данных таблицы `groupmess`
--

INSERT INTO `groupmess` (`id`, `group_id`, `from_id`, `text`) VALUES
(1, 1, 5, 'привет'),
(2, 1, 5, 'цукцук'),
(3, 1, 6, 'привет'),
(4, 3, 6, 'привет'),
(5, 3, 6, 'qwe'),
(6, 3, 6, 'asdasdsa'),
(7, 2, 3, 'adasd'),
(8, 2, 9, 'dadadas'),
(9, 2, 6, 'куку'),
(10, 6, 3, 'выаыва'),
(11, 3, 6, 'чсмчм'),
(12, 3, 6, 'ываываыв'),
(13, 3, 6, 'аыаываыва'),
(14, 3, 6, 'ываываыа'),
(15, 3, 6, 'ываыаыва'),
(16, 3, 6, 'ываываыва'),
(17, 3, 6, 'ываыаыва'),
(18, 3, 6, 'ываываыва'),
(19, 3, 6, 'ываываыва'),
(20, 3, 6, 'ыв'),
(21, 3, 6, 'dasdas'),
(22, 6, 3, 'sdfsdfsfd'),
(23, 1, 6, 'TEEEEEEST'),
(24, 1, 6, 'TEEEEEEST'),
(25, 1, 6, 'TEEEEEEST'),
(26, 1, 3, 'TEST2'),
(27, 1, 3, 'TEST2'),
(28, 1, 3, 'TEST2'),
(29, 1, 3, 'TEST2'),
(30, 1, 3, 'TEST2'),
(31, 1, 3, 'TEST2'),
(32, 1, 6, 'test2'),
(33, 1, 6, 'sdfsdf'),
(34, 1, 3, '123'),
(35, 1, 3, '321'),
(36, 1, 3, '111'),
(37, 1, 3, 'sdfsdf'),
(38, 1, 6, '3333'),
(39, 1, 6, '44454'),
(40, 1, 6, '3232323'),
(41, 1, 6, 'афыафыа'),
(42, 1, 6, 'фывфыв'),
(43, 1, 6, 'фывфвфв'),
(44, 1, 3, 'werwer'),
(45, 1, 6, 'asdsad'),
(46, 1, 6, 'sdfsdfsfd'),
(47, 1, 3, 'asdasdasd'),
(48, 1, 6, '111111111'),
(49, 1, 3, '22222222'),
(50, 1, 3, '33333333333333'),
(51, 1, 3, 'аываыва'),
(52, 1, 3, 'ываыва'),
(53, 1, 3, '555555555555'),
(54, 1, 3, '777777777777777777'),
(55, 1, 3, 'аааааааааааааааа'),
(56, 1, 3, 'qqqqqqqqqqqqqqqqqq'),
(57, 1, 3, 'аываыв'),
(58, 1, 3, 'фывфыв'),
(59, 3, 6, 'аываыва'),
(60, 3, 6, 'ываыва'),
(61, 3, 6, 'вфывфв'),
(62, 1, 6, 'dasdad'),
(63, 1, 6, 'asdadsasd'),
(64, 1, 6, 'asdads'),
(65, 1, 6, 'asdadasd'),
(66, 1, 3, 'asdadad'),
(67, 1, 3, 'adada'),
(68, 1, 6, 'sfdsdf'),
(69, 1, 3, 'dasdasdad'),
(70, 1, 6, 'dadad'),
(72, 1, 6, 'аываыва'),
(73, 1, 6, 'фывфвфв'),
(74, 1, 5, '00000000000000000'),
(75, 1, 6, '1111111111111111111'),
(76, 1, 6, '2222222222222222'),
(77, 1, 6, 'аываыва'),
(78, 1, 3, 'фывфыв'),
(79, 1, 3, '123'),
(80, 1, 3, '321'),
(81, 1, 3, '333'),
(82, 1, 3, '222'),
(83, 1, 6, '111'),
(84, 6, 3, 'aaaaa'),
(85, 1, 6, 'dasd'),
(86, 1, 3, 'asdasd');

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE `groups` (
  `id` int UNSIGNED NOT NULL,
  `group_id` int UNSIGNED DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Дамп данных таблицы `groups`
--

INSERT INTO `groups` (`id`, `group_id`, `user_id`) VALUES
(1, 1, 6),
(2, 1, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `messenges`
--

CREATE TABLE `messenges` (
  `id` int UNSIGNED NOT NULL,
  `to_id` int UNSIGNED DEFAULT NULL,
  `from_id` int UNSIGNED DEFAULT NULL,
  `text` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Дамп данных таблицы `messenges`
--

INSERT INTO `messenges` (`id`, `to_id`, `from_id`, `text`) VALUES
(1, 3, 5, 'привет'),
(2, 3, 5, 'цукцук'),
(3, 3, 6, 'привет'),
(4, 3, 6, 'привет'),
(5, 3, 6, 'qwe'),
(6, 3, 6, 'asdasdsa'),
(7, 6, 3, 'adasd'),
(8, 6, 9, 'dadadas'),
(9, 3, 6, 'куку'),
(10, 6, 3, 'выаыва'),
(11, 3, 6, 'чсмчм'),
(12, 3, 6, 'ываываыв'),
(13, 3, 6, 'аыаываыва'),
(14, 3, 6, 'ываываыа'),
(15, 3, 6, 'ываыаыва'),
(16, 3, 6, 'ываываыва'),
(17, 3, 6, 'ываыаыва'),
(18, 3, 6, 'ываываыва'),
(19, 3, 6, 'ываываыва'),
(20, 3, 6, 'ыв'),
(21, 3, 6, 'dasdas'),
(22, 6, 3, 'sdfsdfsfd'),
(23, 3, 6, 'вфывфыв'),
(24, 3, 6, 'ааааааааааааа'),
(25, 9, 6, 'ываываываы'),
(26, 9, 6, 'фывфывфывфыв'),
(27, 1, 6, '123'),
(28, 2, 6, '321'),
(29, 2, 6, '321'),
(30, 1, 6, '123'),
(31, 2, 6, '321'),
(32, 9, 6, 'fffffffffffffffffffffff'),
(34, 3, 6, '321'),
(35, 3, 6, 'урааааааааааааааа'),
(36, 6, 3, 'adsdad'),
(37, 6, 3, 'adad'),
(38, 3, 6, 'фывфыв'),
(39, 3, 6, 'asdasd'),
(41, 3, 6, 'будет?'),
(45, 6, 3, 'fsfsdf'),
(46, 6, 3, 'asdasd'),
(47, 6, 3, 'asdasd'),
(48, 6, 3, 'asdasda'),
(49, 6, 3, 'asdasd'),
(50, 6, 3, 'asdad'),
(52, 3, 6, '111111111111111'),
(53, 3, 6, 'Привет!'),
(54, 6, 3, 'wdqwd'),
(55, 3, 6, 'asdasd');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `nickname` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `user_hash` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `user_ip` int UNSIGNED DEFAULT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `created` int UNSIGNED DEFAULT NULL,
  `updated` int UNSIGNED DEFAULT NULL,
  `display` tinyint(1) NOT NULL DEFAULT '0',
  `notification` tinyint(1) NOT NULL DEFAULT '0',
  `avatar` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `nickname`, `email`, `password`, `user_hash`, `user_ip`, `role`, `created`, `updated`, `display`, `notification`, `avatar`) VALUES
(3, 'Константин', 'blood', 'bedblood87@gmail.com', '$2y$10$YWa3.oNPBXZgr.XNLXkGOO1cV8VEByq6CwBxrKFkTSedqsPUAQqqi', '2oQFLI30MF', 2886991873, 'VK', 1693839991, 1696511640, 1, 0, '3.jpeg'),
(5, 'Petya', 'Karambol', 'test2@test.test', '$2y$10$qqbVPlxst5qprHBBfHkHyu9x0KTLkDZP2HUBqmDOL8LjJ2C/4rIVy', 'IfLeWkZkhi', 2886991873, 'user', 1694176402, 1696422324, 1, 0, NULL),
(6, 'Никто', 'kuka', 'test@test.test', '$2y$10$TJ667.YyEL1IBgFWx933T.BqFYgfdQk8oGpfcDLfI4QNSM02p7ON6', '7JyYR6eG7b', 2886991873, 'user', 1694177273, 1696514433, 0, 1, '6.gif'),
(9, 'Костя', '', 'bedblood22@yandex.ru', '$2y$10$7VoxCozhgHFih9AKN.XYEutImJBz/tfJ2qqiASWGQ638e1CEAYnqS', 'RPN4unPFOE', 2886991873, 'user', 1694274538, NULL, 0, 0, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `groupmess`
--
ALTER TABLE `groupmess`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_messenges_to` (`group_id`),
  ADD KEY `index_foreignkey_messenges_from` (`from_id`);

--
-- Индексы таблицы `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_groups_group` (`group_id`),
  ADD KEY `index_foreignkey_groups_user` (`user_id`);

--
-- Индексы таблицы `messenges`
--
ALTER TABLE `messenges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_messenges_to` (`to_id`),
  ADD KEY `index_foreignkey_messenges_from` (`from_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `groupmess`
--
ALTER TABLE `groupmess`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT для таблицы `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `messenges`
--
ALTER TABLE `messenges`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Апр 01 2025 г., 06:52
-- Версия сервера: 5.7.24
-- Версия PHP: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `webdatabase`
--

-- --------------------------------------------------------

--
-- Структура таблицы `buildings`
--

CREATE TABLE `buildings` (
  `building_id` int(11) NOT NULL,
  `building_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `buildings`
--

INSERT INTO `buildings` (`building_id`, `building_name`) VALUES
(1, 'Корпус №1'),
(2, 'Корпус №2'),
(3, 'Корпус №3'),
(4, 'Корпус №4'),
(5, 'Корпус №5');

-- --------------------------------------------------------

--
-- Структура таблицы `floors`
--

CREATE TABLE `floors` (
  `floor_id` int(11) NOT NULL,
  `floor_number` int(11) NOT NULL,
  `building_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `floors`
--

INSERT INTO `floors` (`floor_id`, `floor_number`, `building_id`) VALUES
(1, 1, 5),
(2, 2, 5),
(3, 3, 5),
(4, 4, 5);

-- --------------------------------------------------------

--
-- Структура таблицы `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` int(11) NOT NULL,
  `room_type` enum('лекционная','компьютерная','раздевалка','деканат','кафедра') DEFAULT NULL,
  `floor_id` int(11) DEFAULT NULL,
  `building_id` int(11) NOT NULL,
  `info` text,
  `position_x` int(11) DEFAULT NULL,
  `position_y` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `room_type`, `floor_id`, `building_id`, `info`, `position_x`, `position_y`) VALUES
(1, 110, 'лекционная', 1, 5, '1', 1, 1),
(2, 120, 'лекционная', 1, 5, 'дада', 2, 2),
(3, 121, 'лекционная', 1, 5, 'дадада', 3, 3),
(4, 122, 'лекционная', 1, 5, '1', 4, 4),
(5, 127, 'лекционная', 1, 5, '1', 5, 5),
(6, 130, 'лекционная', 1, 5, '1', 6, 6),
(7, 202, 'лекционная', 2, 5, '2', 1, 1),
(9, 2, 'лекционная', 2, 2, '2', 2, 2),
(10, 302, 'компьютерная', 3, 5, '1', 1, 1),
(11, 102, 'деканат', 1, 5, 'Деканат', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$cO78aomfJLgXP9HfKaStcur.OqzApDp73fixTdFrmVWOQ0sge3HtS', 'admin');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`building_id`);

--
-- Индексы таблицы `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`floor_id`);

--
-- Индексы таблицы `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `buildings`
--
ALTER TABLE `buildings`
  MODIFY `building_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `floors`
--
ALTER TABLE `floors`
  MODIFY `floor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

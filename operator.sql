-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Май 30 2025 г., 23:09
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `operator`
--

-- --------------------------------------------------------

--
-- Структура таблицы `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `type` enum('data','sms','minutes') NOT NULL,
  `amount` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `packages`
--

INSERT INTO `packages` (`id`, `type`, `amount`, `price`, `description`) VALUES
(1, 'data', 1, 1200, 'Дополнительный 1 ГБ интернета'),
(2, 'data', 3, 2000, 'Дополнительные 3 ГБ интернета'),
(3, 'data', 15, 3000, 'Дополнительные 15 ГБ интернета'),
(4, 'data', 25, 4500, 'Дополнительные 25 ГБ интернета'),
(5, 'data', 40, 6000, 'Дополнительные 40 ГБ интернета'),
(6, 'sms', 20, 230, 'Пакет 20 SMS'),
(7, 'sms', 100, 1100, 'Пакет 100 SMS'),
(8, 'sms', 300, 2200, 'Пакет 300 SMS'),
(9, 'minutes', 50, 500, 'Пакет 50 минут'),
(10, 'minutes', 100, 900, 'Пакет 100 минут'),
(11, 'minutes', 200, 1600, 'Пакет 200 минут'),
(12, 'minutes', 500, 3000, 'Пакет 500 минут'),
(13, 'minutes', 1000, 5000, 'Пакет 1000 минут');

-- --------------------------------------------------------

--
-- Структура таблицы `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`) VALUES
(8, 1, '1e9a3e9341cfd72f33ba661fb9604acd1d3c8455c0b8554a36d9efa7dabe058a', '2025-05-27 17:39:31'),
(29, 8, 'a929e77e652c16c81b8f0ab3c32eef3c2f596987aae4176f55807d1c86eaff22', '2025-05-28 22:08:52');

-- --------------------------------------------------------

--
-- Структура таблицы `position`
--

CREATE TABLE `position` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `position`
--

INSERT INTO `position` (`id`, `name`) VALUES
(1, 'Администратор'),
(2, 'Менеджер'),
(3, 'Частный клиент'),
(4, 'Бизнес клиент');

-- --------------------------------------------------------

--
-- Структура таблицы `tariff`
--

CREATE TABLE `tariff` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `monthly_cost` int(11) NOT NULL,
  `minutes` int(11) DEFAULT NULL,
  `data_gb` int(11) DEFAULT NULL,
  `sms` int(11) DEFAULT NULL,
  `additional_services` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `tariff`
--

INSERT INTO `tariff` (`id`, `name`, `monthly_cost`, `minutes`, `data_gb`, `sms`, `additional_services`) VALUES
(1, 'Эконом', 550, 200, 10, 0, NULL),
(2, 'Базовый', 650, 400, 25, 0, 'Безлимитный интернет на соцсети и мессенджеры.'),
(3, 'Игровой', 950, 700, 30, 100, 'Безлимитный интернет на соцсети, мессенджеры и игры.'),
(4, 'Премиум', 1500, 2000, 60, 0, 'Безлимитный интернет на соцсети и мессенджеры. Выделенная линия поддержки.'),
(5, 'Базовый', 0, 0, 0, 0, 'Добро пожаловать в А1!'),
(11, 'Малый бизнес', 500, 600, 30, 0, 'Безлимитный интернет на соцсети и мессенджеры.'),
(22, 'Средний бизнес', 600, 1000, 40, 0, 'Безлимитный интернет на соцсети и мессенджеры.'),
(33, 'Крупный бизнес', 700, 2000, 50, 0, 'Безлимитный интернет на соцсети и мессенджеры.'),
(44, 'Премиум бизнес', 1200, 5000, 60, 0, 'Безлимитный интернет на соцсети и мессенджеры.'),
(55, 'Бизнес базовый', 0, 0, 0, 0, NULL),
(555, 'Корпоративный МИНИ', 370, 2500, 150, 250, 'Безлимитный интернет на соцсети и мессенджеры.'),
(666, 'Корпоративный СТАНДАРТ', 450, 5000, 250, 500, 'Безлимитный интернет на соцсети и мессенджеры. Запись разговоров.'),
(777, 'Корпоративный ПРО', 540, 7500, 350, 1000, 'Безлимитный интернет на соцсети и мессенджеры. Запись разговоров.');

-- --------------------------------------------------------

--
-- Структура таблицы `tariff_history`
--

CREATE TABLE `tariff_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `old_tariff_id` int(11) DEFAULT NULL,
  `new_tariff_id` int(11) NOT NULL,
  `change_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `tariff_history`
--

INSERT INTO `tariff_history` (`id`, `user_id`, `old_tariff_id`, `new_tariff_id`, `change_date`) VALUES
(1, 6, 3, 3, '2025-05-17 08:37:54'),
(2, 6, 3, 1, '2025-05-17 19:17:33'),
(4, 6, 2, 2, '2025-05-17 22:33:21'),
(5, 6, 2, 2, '2025-05-18 17:03:03'),
(6, 8, 22, 22, '2025-05-18 18:07:21'),
(7, 8, 22, 11, '2025-05-18 23:28:53'),
(8, 8, 11, 11, '2025-05-19 17:26:38');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tariff_id` int(11) DEFAULT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `balance` int(11) DEFAULT 0,
  `data_balance` int(11) DEFAULT 0,
  `sms_balance` int(11) DEFAULT 0,
  `minutes_balance` int(11) DEFAULT 0,
  `documents_verified` enum('Да','Нет') DEFAULT 'Нет',
  `last_submission_time` datetime DEFAULT NULL,
  `new_full_name` varchar(255) DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `full_name`, `birth_date`, `position_id`, `phone_number`, `email`, `tariff_id`, `login`, `password`, `balance`, `data_balance`, `sms_balance`, `minutes_balance`, `documents_verified`, `last_submission_time`, `new_full_name`, `document_path`) VALUES
(1, 'Кузнецов Артём Владимирович', '2005-05-29', 1, '+79888888888', 'admin@example.com', 555, 'admin', '$2y$10$ztW5vW/kUwxHNY8uO09GweMmUDt8wHYTcho26EzXmeLF2Nq3ShT9G', 2000000, 0, 0, 0, 'Да', NULL, NULL, NULL),
(2, 'Петухов Артём Алексеевич', '2005-08-11', 2, '+79777777777', 'manager@example.com', 555, 'manager', '$2y$10$yK8PKmT5gqjMq43kxSd7JOa8Hx7ywlAPNbvqaTxfPi.otzHh2y5H6', 500, 0, 0, 0, 'Да', NULL, NULL, NULL),
(3, 'Петров Арсений Сергеевич', '2005-01-15', 3, '+79999999999', 'user1@example.com', 2, 'user1', '$2y$10$TPjOXmMcZQ7pGhezUlQBq.TlriCj9ZMpYkddr.ktorRxto599ZFRi', 11000, 0, 0, 0, 'Да', NULL, NULL, NULL),
(4, 'Максимов Артём Валерьевич', '2005-07-14', 3, '+79000000000', 'user2@example.com', 2, 'user2', '$2y$10$y4O/hEmz14tb6r14tUZkxupSA6ebHomObp1qygVQpbeqF3lNgWRV6', 1966999, 0, 0, 0, 'Да', NULL, NULL, NULL),
(5, 'Смирнова Анна Дмитриевна', '1998-03-12', 3, '+79151234567', 'user3@example.com', 2, 'user3', '$2y$10$eAb374M2JNbFKa4Q1RPHS./TSH9xdvxSo1QKt0G84Vik6KZGkf7Ee', 4500, 0, 0, 0, 'Да', NULL, NULL, NULL),
(6, 'Козлов Михаил Сергеевич', '2000-07-25', 3, '+79035554433', 'user4@example.com', 2, 'user4', '$2y$10$MCuiQksMInJdPI02WGdyBuwzdtUDjuHt5XdEL87asRQ6NccQHNVni', 325242, 350, 0, 8000, 'Да', NULL, NULL, NULL),
(7, 'Новикова Екатерина Павловна', '1985-11-30', 4, '+79268765432', 'user5@example.com', 22, 'user5', '$2y$10$8MSsmLTchmoXOZJos1ehReOjtrKTUmOrbXZLLFycoW9hLjVrhziJu', 84500, 3, 300, 1400, 'Да', NULL, NULL, NULL),
(8, 'Волков Денис Игоревич', '1995-05-14', 4, '+79011223344', 'user6@example.com', 11, 'user6', '$2y$10$tMp1SPnfqugq6D81i92sIONV8GW8OL2qK2SguPuOXu/fgdXfTmTMe', 17800, 150, 520, 5650, 'Да', '2025-05-19 17:25:43', NULL, NULL),
(9, 'Белова Ольга Викторовна', '2002-09-08', 4, '+79169876543', 'user7@example.com', 33, 'user7', '$2y$10$znKmMGaA0v3Rvv.xL2.j6OQ5627R5E5zsbRQnA2PhPjyxXQXJxrKa', 1070, 1, 20, 100, 'Да', NULL, NULL, NULL),
(10, 'Соколов Артём Александрович', '1993-12-05', 4, '+79087654321', 'user8@example.com', 44, 'user8', '$2y$10$HDOwAIsAUK9m8PVRbHDB4uiQ0TSFIUYCYE4ea6Wokc6h3WqHP6kHO', 9200, 0, 0, 0, 'Да', NULL, NULL, NULL),
(11, 'Попова Виктория Олеговна', '1988-04-18', 3, '+79130001122', 'user9@example.com', 4, 'user9', '$2y$10$TWsou808j2SJMK/9FJC6dO3XNCEWxxInZLL4n93YLfbCdQcrsIWYm', 21000, 0, 0, 0, 'Да', NULL, NULL, NULL),
(12, 'Лебедев Иван Петрович', '2001-02-28', 3, '+79044556677', 'user10@example.com', 1, 'user10', '$2y$10$QSr.7QCV/oIjIWch3YFkVuQvK1RHilv/x8BE6VrKLNFrKdpZPPkj6', 6700, 0, 0, 0, 'Да', NULL, NULL, NULL),
(13, 'Кузнецова Мария Алексеевна', '1997-08-15', 3, '+79167778899', 'user11@example.com', 2, 'user11', '$2y$10$gemsRYa/TDohIvfin2oEHOFzQLj2gTbI3dOAmiclTYWfxD54ku7ey', 11300, 0, 0, 0, 'Да', NULL, NULL, NULL),
(14, 'Орлов Алексей Денисович', '1990-06-22', 3, '+79055443322', 'user12@example.com', 3, 'user12', '$2y$10$UeIT44DlI6kE.sYEErDoa.E6PsfiQCaEp2PRo9wMstPMOjHhEvePe', 8300, 0, 0, 0, 'Да', NULL, NULL, NULL),
(15, 'Морозова Дарья Игоревна', '2003-10-10', 3, '+79181234567', 'user13@example.com', 4, 'user13', '$2y$10$WEZdJ5Q/Hd/gN6U7nMxuduO0GeKevWm6I4kmI8cy0wrt9TBe.3PzG', 12500, 0, 0, 0, 'Нет', '2025-05-19 14:40:30', NULL, NULL),
(99, 'РОВИИ', '1996-01-01', 4, '+79000000909', 'igorl@example.com', 55, 'igorl', '$2y$10$.k0EUOFVYnkT9BPPNGAvtuQEM/J013AZdfBUOESiyhqY420raHibi', 0, 0, 0, 0, 'Нет', '2025-05-19 14:40:30', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `user_cards`
--

CREATE TABLE `user_cards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bank` varchar(50) NOT NULL,
  `bank_logo` varchar(255) DEFAULT NULL,
  `payment_system` enum('МИР') NOT NULL DEFAULT 'МИР',
  `card_number` varchar(16) NOT NULL,
  `expiry_date` varchar(5) NOT NULL,
  `cvc` varchar(3) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 100.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user_cards`
--

INSERT INTO `user_cards` (`id`, `user_id`, `bank`, `bank_logo`, `payment_system`, `card_number`, `expiry_date`, `cvc`, `balance`) VALUES
(1, 1, 'Сбербанк', NULL, 'МИР', '2202201735182151', '02/29', '550', 10000.00),
(2, 2, 'Тинькофф Банк', NULL, 'МИР', '2200709326011914', '03/28', '822', 10000.00),
(4, 4, 'Альфа-Банк', NULL, 'МИР', '2200152250442338', '11/27', '175', 10000.00),
(5, 3, 'Озон Банк', NULL, 'МИР', '2204321548963275', '05/28', '123', 10000.00),
(6, 5, 'Яндекс Банк', NULL, 'МИР', '2204318765432109', '08/29', '456', 10000.00),
(7, 11, 'Сбербанк', NULL, 'МИР', '2202203673846999', '12/30', '371', 10000.00),
(8, 6, 'МТС Банк', NULL, 'МИР', '2200287654321098', '11/30', '789', 10000.00),
(9, 9, 'Райфайзен Банк', NULL, 'МИР', '2200309876543210', '03/31', '321', 10000.00),
(10, 10, 'Сбербанк', NULL, 'МИР', '2202204567890123', '07/32', '654', 10000.00),
(11, 12, 'Альфа-Банк', NULL, 'МИР', '2200157890123456', '09/33', '987', 10000.00),
(12, 13, 'Тинькофф Банк', NULL, 'МИР', '2200702109876543', '12/34', '159', 10000.00),
(13, 14, 'Озон Банк', NULL, 'МИР', '2204325432109876', '02/35', '357', 10000.00),
(14, 15, 'Яндекс Банк', NULL, 'МИР', '2204318765432109', '04/36', '753', 10000.00),
(15, 99, 'МТС Банк', NULL, 'МИР', '2200289876543210', '06/37', '951', 10000.00),
(16, 7, 'Сбербанк', NULL, 'МИР', '2202208165667313', '12/26', '600', 15000.00),
(19, 8, 'Сбербанк', NULL, 'МИР', '2202208258397224', '01/36', '222', 0.00);

-- --------------------------------------------------------

--
-- Структура таблицы `user_packages`
--

CREATE TABLE `user_packages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `purchase_date` datetime NOT NULL DEFAULT current_timestamp(),
  `expiry_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user_packages`
--

INSERT INTO `user_packages` (`id`, `user_id`, `package_id`, `purchase_date`, `expiry_date`, `is_active`) VALUES
(1, 7, 10, '2025-05-15 01:44:12', '2025-06-13 22:44:12', 1),
(2, 7, 10, '2025-05-15 01:49:00', '2025-06-13 22:49:00', 1),
(3, 7, 8, '2025-05-15 01:50:30', '2025-06-13 22:50:30', 1),
(4, 7, 10, '2025-05-15 01:52:59', '2025-06-13 22:52:59', 1),
(5, 7, 12, '2025-05-15 02:08:45', '2025-06-13 23:08:45', 1),
(6, 7, 11, '2025-05-15 02:11:32', '2025-06-13 23:11:32', 1),
(7, 7, 11, '2025-05-15 02:18:24', '2025-06-13 23:18:24', 1),
(8, 7, 2, '2025-05-15 02:19:33', '2025-06-13 23:19:33', 1),
(9, 6, 10, '2025-05-17 19:17:20', '2025-06-16 16:17:20', 1),
(10, 6, 6, '2025-05-18 12:49:48', '2025-06-17 09:49:48', 1),
(11, 8, 6, '2025-05-18 18:07:48', '2025-06-17 15:07:48', 1),
(12, 8, 8, '2025-05-18 23:04:41', '2025-06-17 20:04:41', 1),
(13, 8, 6, '2025-05-18 23:29:45', '2025-06-17 20:29:45', 1),
(14, 8, 6, '2025-05-19 17:26:51', '2025-06-18 14:26:51', 1),
(15, 8, 10, '2025-05-19 18:00:45', '2025-06-18 15:00:45', 1),
(16, 8, 12, '2025-05-19 18:07:39', '2025-06-18 15:07:39', 1),
(17, 9, 6, '2025-05-19 19:27:25', '2025-06-18 16:27:25', 1),
(18, 9, 1, '2025-05-19 19:49:52', '2025-06-18 16:49:52', 1),
(19, 9, 10, '2025-05-19 19:51:08', '2025-06-18 16:51:08', 1),
(20, 8, 9, '2025-05-25 02:23:00', '2025-06-23 23:23:00', 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tariff`
--
ALTER TABLE `tariff`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tariff_history`
--
ALTER TABLE `tariff_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `old_tariff_id` (`old_tariff_id`),
  ADD KEY `new_tariff_id` (`new_tariff_id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_email` (`email`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `tariff_id` (`tariff_id`),
  ADD KEY `idx_phone_number` (`phone_number`);

--
-- Индексы таблицы `user_cards`
--
ALTER TABLE `user_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `user_packages`
--
ALTER TABLE `user_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT для таблицы `tariff_history`
--
ALTER TABLE `tariff_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT для таблицы `user_cards`
--
ALTER TABLE `user_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `user_packages`
--
ALTER TABLE `user_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `tariff_history`
--
ALTER TABLE `tariff_history`
  ADD CONSTRAINT `tariff_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `tariff_history_ibfk_2` FOREIGN KEY (`old_tariff_id`) REFERENCES `tariff` (`id`),
  ADD CONSTRAINT `tariff_history_ibfk_3` FOREIGN KEY (`new_tariff_id`) REFERENCES `tariff` (`id`);

--
-- Ограничения внешнего ключа таблицы `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `position` (`id`),
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`tariff_id`) REFERENCES `tariff` (`id`);

--
-- Ограничения внешнего ключа таблицы `user_cards`
--
ALTER TABLE `user_cards`
  ADD CONSTRAINT `user_cards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Ограничения внешнего ключа таблицы `user_packages`
--
ALTER TABLE `user_packages`
  ADD CONSTRAINT `user_packages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `user_packages_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

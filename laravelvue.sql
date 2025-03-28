-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Хост: db
-- Время создания: Мар 28 2022 г., 16:50
-- Версия сервера: 10.5.13-MariaDB-1:10.5.13+maria~focal
-- Версия PHP: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `laravelvue`
--

-- --------------------------------------------------------

--
-- Структура таблицы `dashboard`
--

CREATE TABLE `dashboard` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fact_oliq_data1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fact_oliq_data2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fact_ooil_data1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fact_ooil_data2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forecast_oliq_data1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forecast_oliq_data2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forecast_ooil_data1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forecast_ooil_data2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filters_auto_sort` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `dashboard`
--

INSERT INTO `dashboard` (`id`, `company`, `fact_oliq_data1`, `fact_oliq_data2`, `fact_ooil_data1`, `fact_ooil_data2`, `forecast_oliq_data1`, `forecast_oliq_data2`, `forecast_ooil_data1`, `forecast_ooil_data2`, `filters_auto_sort`, `created_at`, `updated_at`) VALUES
(1, 'company1', '10', '20', '30', '40', '12', '22', '15', '25', NULL, NULL, NULL),
(2, 'company2', '11', '21', '31', '41', '13', '23', '20', '30', NULL, NULL, NULL),
(3, 'company1', '12', '22', '32', '42', '14', '24', '25', '35', NULL, NULL, NULL),
(4, 'company2', '13', '23', '33', '43', '15', '25', '30', '40', NULL, NULL, NULL),
(5, 'company1', '14', '24', '34', '44', '16', '26', '35', '45', NULL, NULL, NULL),
(6, 'company2', '15', '25', '35', '45', '17', '27', '40', '50', NULL, NULL, NULL),
(7, 'company1', '16', '26', '36', '46', '18', '28', '45', '55', NULL, NULL, NULL),
(8, 'company2', '17', '27', '37', '47', '19', '29', '50', '60', NULL, NULL, NULL),
(9, 'company1', '18', '28', '38', '48', '20', '30', '55', '65', NULL, NULL, NULL),
(10, 'company2', '19', '29', '39', '49', '21', '31', '60', '70', NULL, NULL, NULL),
(11, 'company1', '20', '30', '40', '50', '22', '32', '65', '75', NULL, NULL, NULL),
(12, 'company2', '21', '31', '41', '51', '23', '33', '70', '80', NULL, NULL, NULL),
(13, 'company1', '22', '32', '42', '52', '24', '34', '75', '85', NULL, NULL, NULL),
(14, 'company2', '23', '33', '43', '53', '25', '35', '80', '90', NULL, NULL, NULL),
(15, 'company1', '24', '34', '44', '54', '26', '36', '85', '95', NULL, NULL, NULL),
(16, 'company2', '25', '35', '45', '55', '27', '37', '90', '100', NULL, NULL, NULL),
(17, 'company1', '26', '36', '46', '56', '28', '38', '95', '105', NULL, NULL, NULL),
(18, 'company2', '27', '37', '47', '57', '29', '39', '100', '110', NULL, NULL, NULL),
(19, 'company1', '28', '38', '48', '58', '30', '40', '105', '115', NULL, NULL, NULL),
(20, 'company2', '29', '39', '49', '59', '31', '41', '110', '120', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2020_03_28_082412_create_dashboard_table', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `dashboard`
--
ALTER TABLE `dashboard`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Индексы таблицы `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `dashboard`
--
ALTER TABLE `dashboard`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

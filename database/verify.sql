-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `verify`
--

-- --------------------------------------------------------

--
-- 表的结构 `mail_content`
--

CREATE TABLE `mail_content` (
  `id` int NOT NULL,
  `thread_id` text COLLATE utf8mb4_general_ci NOT NULL,
  `receive_time` text COLLATE utf8mb4_general_ci NOT NULL,
  `original_recipient` text COLLATE utf8mb4_general_ci COMMENT '原始收信人',
  `correspondence` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `original_text` text COLLATE utf8mb4_general_ci,
  `snippet` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `mail_list`
--

CREATE TABLE `mail_list` (
  `id` int NOT NULL,
  `thread_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `mail_content`
--
ALTER TABLE `mail_content`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `mail_list`
--
ALTER TABLE `mail_list`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `mail_content`
--
ALTER TABLE `mail_content`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `mail_list`
--
ALTER TABLE `mail_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

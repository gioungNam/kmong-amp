-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- 생성 시간: 23-12-13 15:05
-- 서버 버전: 10.4.32-MariaDB
-- PHP 버전: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `project`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `member_value`
--

CREATE TABLE `member_value` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `value` varchar(255) NOT NULL,
  `mapping_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='member eav table';

--
-- 테이블의 덤프 데이터 `member_value`
--

INSERT INTO `member_value` (`id`, `user_id`, `type`, `value`, `mapping_id`, `created_at`) VALUES
(1, 'admin', 'likes', '18', NULL, '2023-12-12 09:44:11'),
(2, 'admin', 'likes', '12', NULL, '2023-12-12 09:45:01'),
(3, 'admin', 'likes', '16', NULL, '2023-12-12 10:02:01'),
(4, 'admin', 'likes', '13', NULL, '2023-12-12 10:03:58'),
(5, 'admin', 'likes', '10', NULL, '2023-12-12 10:04:05'),
(6, 'mangoms', 'likes', '16', NULL, '2023-12-12 10:06:39'),
(7, 'yaburis', 'likes', '17', NULL, '2023-12-12 13:55:35'),
(8, 'yaburis', 'likes', '16', NULL, '2023-12-12 13:55:43'),
(9, 'yaburis', 'likes', '13', NULL, '2023-12-12 13:55:49'),
(10, 'admin', 'likes', '11', NULL, '2023-12-12 14:28:15'),
(11, 'yaburis', 'likes', '18', NULL, '2023-12-13 01:26:51'),
(12, 'mangoms', 'likes', '18', NULL, '2023-12-13 02:43:16'),
(13, 'admin', 'likes', '9', NULL, '2023-12-13 04:34:25'),
(14, 'admin', 'likes', '19', NULL, '2023-12-13 04:46:55'),
(15, 'yaburis', 'likes', '19', NULL, '2023-12-13 04:47:07'),
(16, 'mangoms', 'likes', '19', NULL, '2023-12-13 04:47:48'),
(17, 'mangoms', 'likes', '20', NULL, '2023-12-13 05:51:03'),
(18, 'mangoms', 'likes', '38', NULL, '2023-12-13 08:03:33'),
(19, 'yaburis', 'likes', '38', NULL, '2023-12-13 08:27:59'),
(20, 'mangoms', 'comment', '안녕하세요^^', 'mangoms', '2023-12-13 13:05:53'),
(21, 'mangoms', 'comment', '운영자입니다.', 'admin', '2023-12-13 13:44:53'),
(22, 'mangoms', 'comment', '방명록 테스트2', 'admin', '2023-12-13 13:49:49'),
(23, 'mangoms', 'comment', '방명록 테스트3', 'admin', '2023-12-13 13:50:30'),
(24, 'mangoms', 'comment', '방명록 테스트4', 'admin', '2023-12-13 13:51:01'),
(25, 'mangoms', 'comment', '방명록 테스트5', 'admin', '2023-12-13 13:51:18');

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `member_value`
--
ALTER TABLE `member_value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id_type` (`user_id`,`type`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `member_value`
--
ALTER TABLE `member_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

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
-- 테이블 구조 `board_value`
--

CREATE TABLE `board_value` (
  `id` int(11) NOT NULL,
  `board_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `mapping_id` varchar(255) DEFAULT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='board eav table';

--
-- 테이블의 덤프 데이터 `board_value`
--

INSERT INTO `board_value` (`id`, `board_id`, `type`, `mapping_id`, `value`, `created_at`) VALUES
(1, 18, 'reply', 'yaburis', '댓글입니다.', '2023-12-12 14:23:16'),
(3, 18, 'reply', 'yaburis', '댓글입니다 3', '2023-12-12 14:25:03'),
(4, 16, 'reply', 'yaburis', '댓글', '2023-12-12 14:26:21'),
(5, 18, 'reply', 'admin', '관리자 댓글', '2023-12-12 14:26:42'),
(11, 18, 'reply', 'yaburis', '야부리야부리', '2023-12-13 01:26:47'),
(19, 14, 'reply', 'mangoms', '야부리', '2023-12-13 02:37:08'),
(20, 18, 'reply', 'mangoms', 'dd', '2023-12-13 02:42:47'),
(22, 18, 'reply', 'mangoms', 'dd', '2023-12-13 02:42:50'),
(23, 18, 'reply', 'mangoms', 'dd', '2023-12-13 02:42:52'),
(31, 19, 'reply', 'yaburis', '주목!', '2023-12-13 04:47:12'),
(32, 19, 'reply', 'mangoms', '댓글 댓글\n댓글', '2023-12-13 04:47:38'),
(33, 20, 'inquiry_state', NULL, 'wait', '2023-12-13 05:45:01'),
(34, 20, 'reply', 'admin', '넵 문의주신 사항이 맞습니다.', '2023-12-13 06:00:06'),
(35, 22, 'inquiry_state', NULL, 'wait', '2023-12-13 06:01:48'),
(36, 19, 'reply', 'admin', '감사합니다.', '2023-12-13 06:12:06'),
(38, 14, 'reply', 'mangoms', 'ㅇㅇ', '2023-12-13 06:20:29'),
(39, 14, 'reply', 'mangoms', 'ㅇㅇ', '2023-12-13 06:20:31'),
(40, 32, 'image', NULL, '../uploads/망곰이.jpg', '2023-12-13 07:35:20'),
(41, 33, 'image', NULL, '../uploads/망곰이.jpg', '2023-12-13 07:35:40'),
(42, 34, 'image', NULL, '../uploads/망곰1.png', '2023-12-13 08:01:11'),
(43, 35, 'image', NULL, '../uploads/망곰2.png', '2023-12-13 08:01:31'),
(44, 36, 'image', NULL, '../uploads/망곰3.png', '2023-12-13 08:01:44'),
(45, 37, 'image', NULL, '../uploads/망곰4.png', '2023-12-13 08:01:55'),
(46, 38, 'image', NULL, '../uploads/스크린샷 2023-10-27 124530.png', '2023-12-13 08:02:25'),
(47, 39, 'image', NULL, '../uploads/과목2 오픈채팅.png', '2023-12-13 08:05:09'),
(48, 40, 'image', NULL, '../uploads/부리마왕.png', '2023-12-13 08:06:27'),
(49, 38, 'reply', 'yaburis', '멋있습니다.', '2023-12-13 08:27:58');

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `board_value`
--
ALTER TABLE `board_value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `board_id_index` (`board_id`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `board_value`
--
ALTER TABLE `board_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

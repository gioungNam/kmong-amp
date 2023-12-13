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
-- 테이블 구조 `board`
--

CREATE TABLE `board` (
  `id` int(11) NOT NULL,
  `board_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `likes` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='board table';

--
-- 테이블의 덤프 데이터 `board`
--

INSERT INTO `board` (`id`, `board_type`, `title`, `content`, `user_id`, `created_at`, `likes`, `views`) VALUES
(1, 'free', '제목1', '.', 'buris', '2023-12-11 14:50:46', 0, 0),
(2, 'free', '제목2', 'ㅇ', 'buris', '2023-12-11 14:51:52', 0, 1),
(3, 'free', '제목3', 'ㅇㅇ', 'buris', '2023-12-11 14:52:19', 0, 1),
(4, 'free', '제목3', 'ㅇㅇ', 'buris', '2023-12-11 14:52:19', 0, 0),
(5, 'free', '제목4', 'ㅇㅇ', 'buris', '2023-12-11 14:52:45', 0, 0),
(6, 'free', '제목4', 'ㅇㅇ', 'buris', '2023-12-11 14:52:45', 0, 0),
(8, 'free', '제목7', 'ㅇ', 'buris', '2023-12-11 15:04:32', 0, 1),
(9, 'free', '제목10', 'ㅇㅇ', 'buris', '2023-12-11 15:04:47', 1, 3),
(10, 'free', '제목10이에요', '제목10입니다.', 'buris', '2023-12-11 15:04:47', 1, 10),
(13, 'free', '중복', 'ㅇ', 'buris', '2023-12-11 15:05:35', 2, 5),
(14, 'free', '제목입니다.', '...\r\n...', 'mangoms', '2023-12-11 22:09:30', 0, 11),
(17, 'free', '테스트 게시글2', '테스트', 'mangoms', '2023-12-11 22:50:41', 1, 38),
(19, 'notice', '공지사항입니다.', '모두들 주목', 'admin', '2023-12-13 04:42:06', 3, 7),
(20, 'inquiry', '문의드릴게 있습니다.', '문의 내용\r\n문의 내용', 'mangoms', '2023-12-13 05:45:01', 1, 28),
(21, 'notice', '공지사항2', '공지사항2', 'admin', '2023-12-13 06:01:23', 0, 2),
(22, 'inquiry', '저도 문의사항 있습니다.', '어쩌고 저쩌고\r\n문의 입니다.', 'yaburis', '2023-12-13 06:01:48', 0, 10),
(33, 'img', '제목', '내용', 'mangoms', '2023-12-13 07:35:40', 0, 2),
(34, 'img', '커마게시판2', '내용', 'mangoms', '2023-12-13 08:01:11', 0, 1),
(35, 'img', '커마게시판 제목3', '내용3', 'mangoms', '2023-12-13 08:01:31', 0, 0),
(36, 'img', '커마게시판4', '내용', 'mangoms', '2023-12-13 08:01:44', 0, 2),
(37, 'img', '커마게시판 555', '555', 'mangoms', '2023-12-13 08:01:55', 0, 2),
(38, 'img', '커마게시판입니다.', 'ㅇㅇㅇㅇ', 'mangoms', '2023-12-13 08:02:25', 2, 6);

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `board`
--
ALTER TABLE `board`
  ADD PRIMARY KEY (`id`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `board`
--
ALTER TABLE `board`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

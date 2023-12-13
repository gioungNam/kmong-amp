-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- 생성 시간: 23-12-13 15:04
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
-- 테이블 구조 `member`
--

CREATE TABLE `member` (
  `user_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `member_grade` varchar(50) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1,
  `likes` int(11) NOT NULL DEFAULT 0,
  `profile_path` varchar(255) DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `game_nickname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='member table';

--
-- 테이블의 덤프 데이터 `member`
--

INSERT INTO `member` (`user_id`, `password`, `member_grade`, `nickname`, `level`, `likes`, `profile_path`, `reg_date`, `game_nickname`) VALUES
('admin', '1234', 'admin', '어드민', 999, 0, '../uploads/망곰이.jpg', '2023-12-12 03:30:11', '어드민'),
('buri', '1234', 'basic', '망고미', 99, 0, '', '2023-12-11 14:25:02', '망고미파워'),
('buris', '1234', 'basic', '찌', 99, 0, '../uploads/홀맨 무드등.jpg', '2023-12-11 14:26:29', '찌'),
('guri', '1234', 'basic', '구리', 11, 0, '', '2023-12-11 14:30:26', '구리왕'),
('guris', '1234', 'basic', '구리', 11, 0, '', '2023-12-11 14:30:49', '구리왕'),
('mangoms', '1234', 'basic', '망그러진bear', 99, 0, '../uploads/망곰이.jpg', '2023-12-11 22:02:14', '망곰망곰'),
('yaburi', '1234', 'basic', '야부리', 111, 0, '', '2023-12-11 14:27:12', '야부리왕'),
('yaburis', '1234', 'basic', '야부리', 111, 0, '../uploads/홀맨 무드등.jpg', '2023-12-11 14:30:11', '야부리왕');

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_game_nickname` (`game_nickname`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

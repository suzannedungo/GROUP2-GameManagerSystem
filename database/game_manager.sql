-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Nov 28, 2024 at 11:08 AM
-- Server version: 11.3.2-MariaDB
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `game_manager`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `username` varchar(250) NOT NULL,
  `profile_image` varchar(250) DEFAULT 'default_dp.jpg',
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `tokencode` varchar(250) NOT NULL,
  `status` enum('not_verified','verified') NOT NULL DEFAULT 'not_verified',
  `type` enum('user','admin') DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `name`, `username`, `profile_image`, `email`, `password`, `tokencode`, `status`, `type`, `created_at`) VALUES
(1, 'Kobe Prado Tuazon', 'ebok', 'default_dp.jpg', 'kobeb7952@gmail.com', '6a204bd89f3c8348afd5c77c717a097a', '30a409a46b8d536c60bdd4374c041556', 'verified', 'admin', '2024-11-16 13:41:54'),
(3, 'Kobe Bryant', 'kobe18', 'default_dp.jpg', '2022311035@dhvsu.edu.ph', '6a204bd89f3c8348afd5c77c717a097a', '677b5efefcf2f0bd2a32fc2b4c0e156f', 'verified', 'user', '2024-11-17 19:02:46');

-- --------------------------------------------------------

--
-- Table structure for table `favorite_games`
--

DROP TABLE IF EXISTS `favorite_games`;
CREATE TABLE IF NOT EXISTS `favorite_games` (
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `date_added` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`game_id`),
  KEY `favorite_games_ibfk_2` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

DROP TABLE IF EXISTS `game`;
CREATE TABLE IF NOT EXISTS `game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `game_image` varchar(250) DEFAULT 'default-game-icon.jpg',
  `info` text NOT NULL,
  `download_link` varchar(250) NOT NULL,
  `date_added` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_genre`
--

DROP TABLE IF EXISTS `game_genre`;
CREATE TABLE IF NOT EXISTS `game_genre` (
  `game_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL,
  PRIMARY KEY (`game_id`,`genre_id`),
  KEY `game_genre_ibfk_2` (`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_review`
--

DROP TABLE IF EXISTS `game_review`;
CREATE TABLE IF NOT EXISTS `game_review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `date_added` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `game_review_ibfk_1` (`game_id`),
  KEY `game_review_ibfk_2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_visited`
--

DROP TABLE IF EXISTS `game_visited`;
CREATE TABLE IF NOT EXISTS `game_visited` (
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `visited_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`game_id`),
  KEY `game_visited_ibfk_2` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

DROP TABLE IF EXISTS `genre`;
CREATE TABLE IF NOT EXISTS `genre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `activity` varchar(500) NOT NULL,
  `date_added` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`,`account_id`),
  KEY `logs_ibfk_1` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smtp_user`
--

DROP TABLE IF EXISTS `smtp_user`;
CREATE TABLE IF NOT EXISTS `smtp_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `smtp_user`
--

INSERT INTO `smtp_user` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'VOID', 'noreplyvoid367@gmail.com', 'quyh wvtd dwef hvsv', '2024-11-16 09:18:09');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorite_games`
--
ALTER TABLE `favorite_games`
  ADD CONSTRAINT `favorite_games_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `favorite_games_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `game_genre`
--
ALTER TABLE `game_genre`
  ADD CONSTRAINT `game_genre_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `game_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `game_review`
--
ALTER TABLE `game_review`
  ADD CONSTRAINT `game_review_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `game_review_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `game_visited`
--
ALTER TABLE `game_visited`
  ADD CONSTRAINT `game_visited_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `game_visited_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

DELIMITER $$
--
-- Events
--
DROP EVENT IF EXISTS `delete_unv_acc`$$
CREATE DEFINER=`root`@`localhost` EVENT `delete_unv_acc` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-11-16 13:34:01' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM account
WHERE account.status = 'not_verified' AND account.created_at < NOW() - INTERVAL 3 DAY$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 23, 2024 at 12:18 AM
-- Server version: 8.3.0
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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `default_image` int NOT NULL DEFAULT '0',
  `name` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_joined` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `default_image`, `name`, `email`, `password`, `date_updated`, `date_joined`) VALUES
(1, 0, 'Kobe Prado Tuazon', 'kobeb7952@gmail.com', '6a204bd89f3c8348afd5c77c717a097a', '2024-12-21 20:11:49', '2024-12-18 07:28:54');

-- --------------------------------------------------------

--
-- Table structure for table `favorite_game`
--

DROP TABLE IF EXISTS `favorite_game`;
CREATE TABLE IF NOT EXISTS `favorite_game` (
  `game_id` int NOT NULL,
  `user_id` int NOT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`game_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

DROP TABLE IF EXISTS `game`;
CREATE TABLE IF NOT EXISTS `game` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `developer` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `info` text COLLATE utf8mb4_general_ci NOT NULL,
  `dl_link` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`id`, `name`, `developer`, `info`, `dl_link`, `date_updated`, `date_added`) VALUES
(4, 'Apex Legends', 'Respawn Entertainment', 'Apex Legends is a free-to-play battle royale and hero shooter set in a sci-fi inspired universe. Players compete in squads of three to be the last team standing in fast-paced matches, using strategy, combat, and teamwork to outlast opponents. Each player chooses a unique character, called a Legend, with distinct abilities and skills that provide tactical advantages to their team. The game features dynamic gunplay, advanced movement mechanics, and a unique ping system for seamless communication. With vibrant maps, diverse characters, and fast action, Apex Legends delivers an immersive and competitive multiplayer experience.', 'https://www.ea.com/games/apex-legends', '2024-12-22 18:07:16', '2024-12-21 09:40:39');

-- --------------------------------------------------------

--
-- Table structure for table `game_genre`
--

DROP TABLE IF EXISTS `game_genre`;
CREATE TABLE IF NOT EXISTS `game_genre` (
  `game_id` int NOT NULL,
  `genre_id` int NOT NULL,
  PRIMARY KEY (`game_id`,`genre_id`),
  KEY `genre_id` (`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_genre`
--

INSERT INTO `game_genre` (`game_id`, `genre_id`) VALUES
(4, 12);

-- --------------------------------------------------------

--
-- Table structure for table `game_review`
--

DROP TABLE IF EXISTS `game_review`;
CREATE TABLE IF NOT EXISTS `game_review` (
  `game_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int DEFAULT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `date_updated` datetime DEFAULT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`game_id`,`user_id`),
  KEY `game_review_ibfk_2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_review`
--

INSERT INTO `game_review` (`game_id`, `user_id`, `rating`, `comment`, `date_updated`, `date_added`) VALUES
(4, 8, 5, 'Wow!', NULL, '2024-12-23 05:41:46'),
(4, 9, 1, '', NULL, '2024-12-23 06:25:33');

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

DROP TABLE IF EXISTS `genre`;
CREATE TABLE IF NOT EXISTS `genre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genre`
--

INSERT INTO `genre` (`id`, `name`) VALUES
(13, 'adventure'),
(10, 'horror'),
(14, 'racing'),
(11, 'role play'),
(12, 'shooting');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `activity` text COLLATE utf8mb4_general_ci NOT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smtp_user`
--

DROP TABLE IF EXISTS `smtp_user`;
CREATE TABLE IF NOT EXISTS `smtp_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `smtp_user`
--

INSERT INTO `smtp_user` (`id`, `name`, `email`, `password`, `date_updated`, `date_added`) VALUES
(1, 'VOID', 'noreplyvoid367@gmail.com', 'quyh wvtd dwef hvsv', '2024-12-21 16:22:44', '2024-11-16 09:18:09');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `default_image` int DEFAULT '0',
  `name` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('not_verified','verified','ban') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'not_verified',
  `tokencode` varchar(256) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `default_image`, `name`, `username`, `email`, `password`, `status`, `tokencode`, `date_updated`, `date_added`) VALUES
(8, 1, 'Kobe Prado Tuazon', 'kobe18', 'cocmeme6@gmail.com', 'd74682ee47c3fffd5dcd749f840fcdd4', 'verified', 'b41a7bff7ce6ed4ffdb124b46e4f58b2', '2024-12-22 15:31:32', '2024-12-21 16:36:43'),
(9, 0, 'Kobe Prado Tuazon', 'dhvsu', '2022311035@dhvsu.edu.ph', '6a204bd89f3c8348afd5c77c717a097a', 'verified', 'f1333887c193520f64dd9ca3bfec65c9', '2024-12-23 01:31:30', '2024-12-22 21:47:21');

-- --------------------------------------------------------

--
-- Table structure for table `visited_game`
--

DROP TABLE IF EXISTS `visited_game`;
CREATE TABLE IF NOT EXISTS `visited_game` (
  `game_id` int NOT NULL,
  `user_id` int NOT NULL,
  `date_visited` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`game_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visited_game`
--

INSERT INTO `visited_game` (`game_id`, `user_id`, `date_visited`) VALUES
(4, 8, '2024-12-23 06:13:08'),
(4, 9, '2024-12-23 06:25:35');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorite_game`
--
ALTER TABLE `favorite_game`
  ADD CONSTRAINT `favorite_game_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorite_game_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_genre`
--
ALTER TABLE `game_genre`
  ADD CONSTRAINT `game_genre_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_review`
--
ALTER TABLE `game_review`
  ADD CONSTRAINT `game_review_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_review_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visited_game`
--
ALTER TABLE `visited_game`
  ADD CONSTRAINT `visited_game_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visited_game_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
DROP EVENT IF EXISTS `delete_unv_user`$$
CREATE DEFINER=`root`@`localhost` EVENT `delete_unv_user` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-12-17 18:26:57' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM `user` WHERE `user`.`status` = "not_verified" AND `user`.`date_added` < NOW() - INTERVAL 3 DAY$$

DROP EVENT IF EXISTS `uplift_ban_user`$$
CREATE DEFINER=`root`@`localhost` EVENT `uplift_ban_user` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-12-17 18:31:00' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE `user` SET `user`.`status` = "verified" WHERE `user`.`status` = "ban" AND `user`.`date_updated` < NOW() - INTERVAL 3 DAY$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 17, 2024 at 11:57 AM
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
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `username` varchar(250) DEFAULT NULL,
  `profile_image` varchar(250) DEFAULT 'default_dp.jpg',
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `tokencode` varchar(250) NOT NULL,
  `status` enum('not_verified','verified') NOT NULL DEFAULT 'not_verified',
  `type` enum('user','admin') DEFAULT 'user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `name`, `username`, `profile_image`, `email`, `password`, `tokencode`, `status`, `type`, `created_at`) VALUES
(1, 'Kobe Prado Tuazon', 'ebok', 'default_dp.jpg', 'kobeb7952@gmail.com', '6a204bd89f3c8348afd5c77c717a097a', '30a409a46b8d536c60bdd4374c041556', 'verified', 'admin', '2024-11-16 13:41:54'),
(3, 'Kobe Bryant', 'kobe18', 'default_dp.jpg', '2022311035@dhvsu.edu.ph', '6a204bd89f3c8348afd5c77c717a097a', '677b5efefcf2f0bd2a32fc2b4c0e156f', 'verified', 'user', '2024-11-17 19:02:46');

-- --------------------------------------------------------

--
-- Table structure for table `smtp_user`
--

DROP TABLE IF EXISTS `smtp_user`;
CREATE TABLE IF NOT EXISTS `smtp_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `smtp_user`
--

INSERT INTO `smtp_user` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'VOID', 'noreplyvoid367@gmail.com', 'quyh wvtd dwef hvsv', '2024-11-16 09:18:09');

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

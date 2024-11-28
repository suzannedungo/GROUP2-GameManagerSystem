-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 28, 2024 at 03:26 PM
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
  `name` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `profile_image` varchar(250) COLLATE utf8mb4_general_ci DEFAULT 'default_dp.jpg',
  `email` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `tokencode` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('not_verified','verified') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'not_verified',
  `type` enum('user','admin') COLLATE utf8mb4_general_ci DEFAULT 'user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
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
  `user_id` int NOT NULL,
  `game_id` int NOT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`game_id`),
  KEY `favorite_games_ibfk_2` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

DROP TABLE IF EXISTS `game`;
CREATE TABLE IF NOT EXISTS `game` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `game_image` varchar(250) COLLATE utf8mb4_general_ci DEFAULT 'default-game-icon.jpg',
  `info` text COLLATE utf8mb4_general_ci NOT NULL,
  `download_link` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`id`, `name`, `game_image`, `info`, `download_link`, `date_added`) VALUES
(1, 'WUTHERING WAVES', 'wuwa1.jpg', 'An open-world action RPG with dynamic combat and a rich, post-apocalyptic story.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 20:26:25'),
(2, 'SOLO LEVELING: ARISE', 'Solo-Leveling-ARISE.jpg', 'An action MMORPG featuring thrilling combat, dungeon crawling, and immersive adventures.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 20:26:25'),
(3, 'SWORD ART ONLINE', 'SAO.jpg.webp', 'An RPG where players explore VR worlds, battle monsters, and level up.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 20:26:25'),
(4, 'HONKAI: STAR RAIL', 'Honkai-Star-Rail.jpg', 'Turn-based RPG, exploring sci-fi worlds, battling enemies, and upgrading characters.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 20:26:25'),
(5, 'TOWER OF FANTASY', 'ToF.jpg', 'An open-world RPG, featuring exploration, combat, and character customization in a futuristic setting.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 20:26:25'),
(6, 'DIABLO IMMORTAL', 'diablo-immortal.jpg.jpg', 'An action RPG with real-time combat, dungeon crawling, and character progression.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 20:26:25'),
(7, 'CABAL: INFINITE COMBO', 'cabal.jpg.jpg', 'An action RPG featuring dynamic combat, endless combos, and engaging multiplayer adventures.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 20:26:25'),
(8, 'GRANNY', 'granny.jpg.jpg', 'A horror survival game where players escape a creepy house while avoiding a terrifying pursuer.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 21:05:14'),
(9, 'POPPY PLAYTIME', 'poppy playtime.jpg', 'A horror-puzzle game where players uncover secrets in an abandoned toy factory.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 21:05:14'),
(10, 'FEARS TO FATHOME: HOME ALONE', 'Fears to Fathom.jpg', 'A psychological horror game exploring eerie events and survival instincts.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 21:05:14'),
(11, 'FIVE NIGHTS AT CANDYS', 'five-nights-at-candys.jpg.avif', 'A horror game where players survive nights against animatronic creatures.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 21:05:14'),
(12, 'SLENDER: THE EIGHT PAGES', 'slender.jpg', 'A horror game where players collect pages while evading the Slender Man.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 21:05:14'),
(13, 'LATE NIGHT MOP', 'latenightmop.jpg.png', 'A horror game where players clean a haunted house while facing spooky surprises.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 21:05:14'),
(14, 'FATAL MIDNIGHT', 'fatalmidnight.jpg', 'A horror game where players navigate eerie environments, solve puzzles, and uncover dark secrets.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 21:05:14'),
(15, 'VALORANT', 'valorant.avif', 'A team-based tactical FPS where players combine precise shooting with unique agent abilities.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 22:16:12'),
(16, 'COUNTER STRIKE 2', '4195006-counter-strike-2.jpeg', 'A tactical FPS featuring updated graphics, refined gameplay, and intense team-based combat.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 22:16:12'),
(17, 'APEX LEGENDS', 'apex.jpg', 'A battle royale FPS where players team up, select unique characters, and fight for survival.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 22:16:12'),
(18, 'OVERWATCH 2', 'overwatch2.jpg', 'A team-based FPS with dynamic heroes, strategic gameplay, and intense objective-driven battles.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 22:16:12'),
(19, 'PUBG: BATTLEGROUNDS', 'pubg.jpg', 'A battle royale game where players fight to be the last one standing on a vast map.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 22:16:12'),
(20, 'POINT BLANK', 'pointblank.jpg', 'A fast-paced, tactical first-person shooter with team-based combat and various game modes.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 22:16:12'),
(21, 'THE FINALS', 'thefinals.jpg', 'A tactical FPS with destructible environments, team-based action, and strategic gameplay.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php', '2024-11-28 22:16:12'),
(22, 'NEED FOR SPEED', 'needforspeed.jpg', 'A racing game series featuring high-speed street races, car customization, and intense action.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php#role_play_games', '2024-11-28 22:26:55'),
(23, 'FORZA HORIZON 5', 'forza horizon 5.jpg', 'An open-world racing game with stunning graphics, customization, and exploration.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php#role_play_games', '2024-11-28 22:26:55'),
(24, 'ASSETTO CORSA', 'assetto corsa.jpg', 'A realistic racing simulator known for its precise driving physics, car customization, and immersive tracks.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php#role_play_games', '2024-11-28 22:26:55'),
(25, 'F1 24', 'f1 24.jpg', 'A racing simulation game featuring realistic tracks, cars, and dynamic multiplayer modes.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php#role_play_games', '2024-11-28 22:26:55'),
(26, 'THE CREW', 'thecrew.jpg', 'An open-world racing game focused on exploration, car customization, and multiplayer challenges.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php#role_play_games', '2024-11-28 22:26:55'),
(27, 'MARIO KART 8 DELUXE', 'mario kart 8 deluxe.jpg', 'A fun, fast-paced racing game with iconic characters, creative tracks, and multiplayer modes.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php#role_play_games', '2024-11-28 22:26:55'),
(28, 'ASPHALT 8: AIRBORNE', 'Asphalt 8 Airborne.jpg', 'An arcade racing game featuring high-speed stunts, exotic cars, and vibrant tracks.', 'http://localhost/projects/ITELEC2/Group2-GameManagerSystem/dashboard/user/dashboard.php#role_play_games', '2024-11-28 22:26:55');

-- --------------------------------------------------------

--
-- Table structure for table `game_genre`
--

DROP TABLE IF EXISTS `game_genre`;
CREATE TABLE IF NOT EXISTS `game_genre` (
  `game_id` int NOT NULL,
  `genre_id` int NOT NULL,
  PRIMARY KEY (`game_id`,`genre_id`),
  KEY `game_genre_ibfk_2` (`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_genre`
--

INSERT INTO `game_genre` (`game_id`, `genre_id`) VALUES
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(15, 3),
(16, 3),
(17, 3),
(18, 3),
(19, 3),
(20, 3),
(21, 3),
(22, 4),
(23, 4),
(24, 4),
(25, 4),
(26, 4),
(27, 4),
(28, 4);

-- --------------------------------------------------------

--
-- Table structure for table `game_review`
--

DROP TABLE IF EXISTS `game_review`;
CREATE TABLE IF NOT EXISTS `game_review` (
  `id` int NOT NULL AUTO_INCREMENT,
  `game_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
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
  `user_id` int NOT NULL,
  `game_id` int NOT NULL,
  `visited_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`game_id`),
  KEY `game_visited_ibfk_2` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_visited`
--

INSERT INTO `game_visited` (`user_id`, `game_id`, `visited_at`) VALUES
(3, 3, '2024-11-28 23:25:57'),
(3, 4, '2024-11-28 23:13:56'),
(3, 5, '2024-11-28 23:16:10'),
(3, 11, '2024-11-28 23:16:20'),
(3, 25, '2024-11-28 23:05:27');

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

DROP TABLE IF EXISTS `genre`;
CREATE TABLE IF NOT EXISTS `genre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genre`
--

INSERT INTO `genre` (`id`, `name`) VALUES
(1, 'horror'),
(4, 'racing'),
(2, 'role play'),
(3, 'shooting');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_id` int NOT NULL,
  `activity` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`account_id`),
  KEY `logs_ibfk_1` (`account_id`)
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
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
  ADD CONSTRAINT `favorite_games_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorite_games_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `game_review_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_visited`
--
ALTER TABLE `game_visited`
  ADD CONSTRAINT `game_visited_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_visited_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE;

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

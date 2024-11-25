-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2024 at 01:46 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `game_managersss`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `username` varchar(250) DEFAULT NULL,
  `profile_image` varchar(250) DEFAULT 'default_dp.jpg',
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `tokencode` varchar(250) NOT NULL,
  `status` enum('not_verified','verified') NOT NULL DEFAULT 'not_verified',
  `type` enum('user','admin') DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `favorite_games` (
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `game_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `game_image` varchar(250) DEFAULT 'default-game-icon.jpg',
  `info` text NOT NULL,
  `download_link` varchar(250) NOT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_genre`
--

CREATE TABLE `game_genre` (
  `game_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_review`
--

CREATE TABLE `game_review` (
  `id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_visited`
--

CREATE TABLE `game_visited` (
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `visited_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE `genre` (
  `genre_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `activities_users_admins` varchar(500) NOT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smtp_user`
--

CREATE TABLE `smtp_user` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `smtp_user`
--

INSERT INTO `smtp_user` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'VOID', 'noreplyvoid367@gmail.com', 'quyh wvtd dwef hvsv', '2024-11-16 09:18:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `favorite_games`
--
ALTER TABLE `favorite_games`
  ADD PRIMARY KEY (`user_id`,`game_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`game_id`);

--
-- Indexes for table `game_genre`
--
ALTER TABLE `game_genre`
  ADD PRIMARY KEY (`game_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `game_review`
--
ALTER TABLE `game_review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `game_visited`
--
ALTER TABLE `game_visited`
  ADD PRIMARY KEY (`user_id`,`game_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`genre_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`,`account_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `smtp_user`
--
ALTER TABLE `smtp_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `game_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `game_review`
--
ALTER TABLE `game_review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genre`
--
ALTER TABLE `genre`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smtp_user`
--
ALTER TABLE `smtp_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorite_games`
--
ALTER TABLE `favorite_games`
  ADD CONSTRAINT `favorite_games_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorite_games_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE;

--
-- Constraints for table `game_genre`
--
ALTER TABLE `game_genre`
  ADD CONSTRAINT `game_genre_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`genre_id`) ON DELETE CASCADE;

--
-- Constraints for table `game_review`
--
ALTER TABLE `game_review`
  ADD CONSTRAINT `game_review_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_review_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_visited`
--
ALTER TABLE `game_visited`
  ADD CONSTRAINT `game_visited_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_visited_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

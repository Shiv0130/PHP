-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2025 at 09:05 PM
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
-- Database: `social_network`
CREATE DATABASE social_network 
--

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `friendship_id` int(11) NOT NULL,
  `user_id1` int(11) NOT NULL,
  `user_id2` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE `friend_requests` (
  `request_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `addressee_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `content`, `sent_at`) VALUES
(1, 3, 1, 'hi how are you', '2025-10-28 07:36:29'),
(2, 2, 3, 'hi how are you', '2025-10-28 17:32:35'),
(3, 1, 2, 'hi how are you', '2025-10-30 06:46:21');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `content` text DEFAULT NULL,
  `read_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `user_id`, `content`, `image_path`, `created_at`) VALUES
(1, 1, 'hi', NULL, '2025-10-23 17:28:23'),
(4, 2, 'Hello', NULL, '2025-10-28 17:34:00'),
(5, 1, 'i like aloe vera', 'uploads/post_6903091b7e5284.55973978.jpg', '2025-10-30 06:43:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password_hash`, `profile_picture`, `created_at`) VALUES
(1, 'Saishna Singh', 'saishna355@gmail.com', '$2y$10$CC2N6YlXa7h0eX.AkGbeBu//I.4mi9ZLWgEmXJDGyw6Eo43/Fr4dm', 'uploads/profile_690309598bdae1.85864964.jpg', '2025-10-14 19:41:44'),
(2, 'Rishabh', 'brijlalrishabh@gmail.com', '$2y$10$coEGhTc7P9gm7Hh8VrK3kOBCqmP8OdKCoEhqzQoG0bMYdlPy5/BL6', 'uploads/profile_6900fe6e58b1b5.15245472.jpg', '2025-10-23 19:19:54'),
(3, 'Arthi Singh', 'arthisin@gmail.com', '$2y$10$X5WQmx2ey07/TDPPkG.j..yximiuCqVX8LyK2JPqcDX458tFZOHyS', NULL, '2025-10-28 07:08:06'),
(4, 'simon says', 'iamhungry@gamil.com', '$2y$10$kjiYQypwD6fELtCXxY5UquMftpdnlJN4rEOXLfi.VGU6wVryxiQsu', NULL, '2025-10-30 06:48:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`friendship_id`),
  ADD UNIQUE KEY `uq_friendship` (`user_id1`,`user_id2`),
  ADD KEY `user_id1` (`user_id1`),
  ADD KEY `user_id2` (`user_id2`),
  ADD KEY `user_id1_2` (`user_id1`),
  ADD KEY `user_id2_2` (`user_id2`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD UNIQUE KEY `uq_friend_request` (`requester_id`,`addressee_id`),
  ADD KEY `requester_id` (`requester_id`),
  ADD KEY `addressee_id` (`addressee_id`),
  ADD KEY `status` (`status`),
  ADD KEY `requester_id_2` (`requester_id`),
  ADD KEY `addressee_id_2` (`addressee_id`),
  ADD KEY `status_2` (`status`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `sent_at` (`sent_at`),
  ADD KEY `sender_id_2` (`sender_id`),
  ADD KEY `receiver_id_2` (`receiver_id`),
  ADD KEY `sent_at_2` (`sent_at`),
  ADD KEY `sender_id_3` (`sender_id`),
  ADD KEY `receiver_id_3` (`receiver_id`),
  ADD KEY `sent_at_3` (`sent_at`),
  ADD KEY `sender_id_4` (`sender_id`),
  ADD KEY `receiver_id_4` (`receiver_id`),
  ADD KEY `sent_at_4` (`sent_at`),
  ADD KEY `sender_id_5` (`sender_id`),
  ADD KEY `receiver_id_5` (`receiver_id`),
  ADD KEY `sent_at_5` (`sent_at`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `recipient_id` (`recipient_id`),
  ADD KEY `read_status` (`read_status`),
  ADD KEY `recipient_id_2` (`recipient_id`),
  ADD KEY `read_status_2` (`read_status`),
  ADD KEY `recipient_id_3` (`recipient_id`),
  ADD KEY `read_status_3` (`read_status`),
  ADD KEY `recipient_id_4` (`recipient_id`),
  ADD KEY `read_status_4` (`read_status`),
  ADD KEY `recipient_id_5` (`recipient_id`),
  ADD KEY `read_status_5` (`read_status`),
  ADD KEY `recipient_id_6` (`recipient_id`),
  ADD KEY `read_status_6` (`read_status`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `created_at_2` (`created_at`),
  ADD KEY `created_at_3` (`created_at`),
  ADD KEY `created_at_4` (`created_at`),
  ADD KEY `created_at_5` (`created_at`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `email_2` (`email`),
  ADD KEY `full_name` (`full_name`),
  ADD KEY `email_3` (`email`),
  ADD KEY `full_name_2` (`full_name`),
  ADD KEY `email_4` (`email`),
  ADD KEY `full_name_3` (`full_name`),
  ADD KEY `email_5` (`email`),
  ADD KEY `full_name_4` (`full_name`),
  ADD KEY `email_6` (`email`),
  ADD KEY `full_name_5` (`full_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `friendship_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id1`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`user_id2`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD CONSTRAINT `friend_requests_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friend_requests_ibfk_2` FOREIGN KEY (`addressee_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2025 at 12:50 AM
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
-- Database: `fitzone`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking_actions`
--

CREATE TABLE `booking_actions` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `action_type` enum('view','note','status_change','reply','resolve') NOT NULL,
  `action_value` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_actions`
--

INSERT INTO `booking_actions` (`id`, `session_id`, `staff_id`, `action_type`, `action_value`, `notes`, `created_at`) VALUES
(1, 4, 2, 'resolve', NULL, '', '2025-04-19 17:16:50'),
(2, 4, 2, 'status_change', NULL, '', '2025-04-19 17:16:53'),
(3, 1, 2, 'reply', NULL, 'hellow', '2025-04-19 17:18:54'),
(4, 1, 2, 'status_change', NULL, '', '2025-04-19 17:30:16');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `schedule` varchar(50) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `description`, `image`, `schedule`, `duration`, `trainer_id`, `created_at`, `updated_at`) VALUES
(2, 'Strength Training', 'Build muscle and improve endurance.', 'assets/img/strength_training.png', 'Monday, Wednesday, Friday 6:00 PM', '60 minutes', 1, '2025-04-20 08:27:52', '2025-04-20 08:27:52'),
(3, 'Cardio Sessions', 'Boost your stamina and burn calories fast.', 'assets/img/cardio_fitness.png', 'Tuesday, Thursday 7:00 AM', '45 minutes', 2, '2025-04-20 08:27:52', '2025-04-20 08:27:52'),
(4, 'Yoga & Flexibility', 'Improve posture and relax your body and mind.', 'assets/img/yoga.png', 'Monday, Wednesday 9:00 AM', '75 minutes', 3, '2025-04-20 08:27:52', '2025-04-20 08:27:52'),
(5, 'Pilates', 'Core strength and overall body flexibility.', 'assets/img/pilates.png', 'Tuesday, Thursday 5:30 PM', '60 minutes', 4, '2025-04-20 08:27:52', '2025-04-20 08:27:52'),
(6, 'Zumba', 'Dance fitness for weight loss and fun.', 'assets/img/zumba.png', 'Saturday 10:00 AM', '60 minutes', 5, '2025-04-20 08:27:52', '2025-04-20 08:27:52'),
(7, 'CrossFit', 'High-intensity workout for strength and stamina.', 'assets/img/crossfit.png', 'Monday, Wednesday, Friday 5:00 PM', '90 minutes', 6, '2025-04-20 08:27:52', '2025-04-20 08:27:52'),
(8, 'Weight Loss', 'Targeted training for effective fat loss and toning.', 'assets/img/weight_loss.png', 'Tuesday, Thursday 6:00 PM', '60 minutes', 7, '2025-04-20 08:27:52', '2025-04-20 08:27:52'),
(9, 'Boxing', 'Cardio and strength training through boxing.', 'assets/img/boxing.png', 'Friday 7:00 PM, Saturday 4:00 PM', '90 minutes', 8, '2025-04-20 08:27:52', '2025-04-20 08:27:52');

-- --------------------------------------------------------

--
-- Table structure for table `class_registrations`
--

CREATE TABLE `class_registrations` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_registrations`
--

INSERT INTO `class_registrations` (`id`, `member_id`, `class_name`, `registration_date`) VALUES
(1, 5, 'Cardio Sessions', '2025-04-16 18:02:11'),
(2, 5, 'Yoga & Flexibility', '2025-04-16 18:02:23'),
(3, 5, 'Strength Training', '2025-04-17 07:10:59'),
(4, 5, 'Pilates', '2025-04-17 07:12:52'),
(5, 1, 'Yoga & Flexibility', '2025-04-17 15:15:03'),
(6, 1, 'Strength Training', '2025-04-25 05:41:46'),
(7, 1, 'Cardio Sessions', '2025-04-25 05:42:00'),
(8, 12, 'Strength Training', '2025-04-25 11:13:47');

-- --------------------------------------------------------

--
-- Table structure for table `fitness_classes`
--

CREATE TABLE `fitness_classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `class_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `max_capacity` int(11) NOT NULL,
  `current_enrollment` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` enum('scheduled','cancelled','completed') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('new','replied','resolved','pending') DEFAULT 'new',
  `replies` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `user_id`, `name`, `email`, `subject`, `message`, `file_path`, `created_at`, `status`, `replies`) VALUES
(4, 2, 'Natasha Perera', 'Natasha123@gmail.com', 'One-on-One Personal Training Inquiry', 'Hi, I’m interested in one-on-one training sessions. Could you share details on pricing, trainer availability, and how to schedule a trial session?', NULL, '2025-04-25 06:05:47', 'resolved', 'Hi! Thanks for your interest in one-on-one training. Pricing varies based on session plans, and our certified trainers are available throughout the week. You can book a trial session. Thank You!\napproved.\napproved.\napproved.');

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `plan_name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memberships`
--

INSERT INTO `memberships` (`id`, `member_id`, `plan_name`, `price`, `start_date`, `end_date`, `created_at`) VALUES
(1, 1, 'Basic Plan', 3000.00, '2025-04-16', '2025-05-16', '2025-04-16 07:33:54'),
(2, 1, 'Premium Plan', 5000.00, '2025-04-16', '2025-05-16', '2025-04-16 07:34:46'),
(3, 1, 'Elite Plan', 8000.00, '2025-04-16', '2025-05-16', '2025-04-16 07:34:52'),
(4, 5, 'Basic Plan', 3000.00, '2025-04-16', '2025-05-16', '2025-04-16 07:37:49'),
(6, 1, 'Basic Plan', 3000.00, '2025-04-16', '2025-05-16', '2025-04-16 16:27:11'),
(7, 1, 'Premium Plan', 5000.00, '2025-04-17', '2025-05-17', '2025-04-17 15:13:27'),
(8, 1, 'Basic Plan', 3000.00, '2025-04-25', '2025-05-25', '2025-04-25 05:38:02'),
(9, 12, 'Basic Plan', 3000.00, '2025-04-25', '2025-05-25', '2025-04-25 11:11:48');

-- --------------------------------------------------------

--
-- Table structure for table `member_profiles`
--

CREATE TABLE `member_profiles` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL COMMENT 'in cm',
  `weight` decimal(5,2) DEFAULT NULL COMMENT 'in kg',
  `fitness_goals` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_profiles`
--

INSERT INTO `member_profiles` (`id`, `member_id`, `fullname`, `email`, `phone`, `address`, `gender`, `birthdate`, `height`, `weight`, `fitness_goals`, `medical_conditions`, `updated_at`) VALUES
(1, 5, 'Biso Herath', 'biso@gmail.com', '0708888900', '115/4,Pahalakithulgolla,Ankumbura', 'female', '1959-11-02', 158.00, 57.00, 'weight loss', 'none', '2025-04-19 09:40:35');

-- --------------------------------------------------------

--
-- Table structure for table `staff_profiles`
--

CREATE TABLE `staff_profiles` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `certifications` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_profiles`
--

INSERT INTO `staff_profiles` (`id`, `staff_id`, `fullname`, `email`, `phone`, `address`, `specialization`, `certifications`, `bio`, `emergency_contact`, `updated_at`) VALUES
(1, 2, 'Dilruksha Rajapaksha', 'dilru@gmail.com', '0708888900', '115/4,Pahalakithulgolla,Ankumbura', '', '', '', '', '2025-04-19 17:47:53');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `bio` text NOT NULL,
  `photo` varchar(255) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`id`, `name`, `specialization`, `bio`, `photo`, `joined_at`) VALUES
(1, 'Amila Perera', 'Strength & Conditioning', 'Head Coach with 10+ years of experience in strength training and athletic performance.', 'assets/img/trainer1.png', '2025-04-20 06:43:50'),
(2, 'Sachini Fernando', 'Yoga & Cardio', 'Certified yoga instructor with expertise in Vinyasa and Hatha styles.', 'assets/img/trainer2.png', '2025-04-20 06:43:50'),
(3, 'Kasun Rajapaksha', 'Nutrition', 'Nutrition expert helping clients achieve their fitness goals through proper diet.', 'assets/img/trainer3.png', '2025-04-20 06:43:50'),
(4, 'Dinesh Wickramasinghe', 'Bootcamp & HIIT', 'Specializes in high-intensity interval training and military-style bootcamps.', 'assets/img/trainer4.png', '2025-04-20 06:43:50'),
(5, 'Aruni Jayawardena', 'Pilates', 'Personal trainer focused on core strength and flexibility through Pilates.', 'assets/img/trainer5.png', '2025-04-20 06:43:50'),
(6, 'Haritha Piyumal', 'Group Fitness', 'Enthusiastic group fitness instructor specializing in cardio workouts.', 'assets/img/trainer6.png', '2025-04-20 06:43:50'),
(7, 'Tharindu Wijesuriya', 'Rehabilitation', 'Mobility specialist helping clients recover from injuries.', 'assets/img/trainer7.png', '2025-04-20 06:43:50'),
(8, 'Kavinda Rajapaksha', 'Dance Fitness', 'Energetic dance fitness instructor making workouts fun and engaging.', 'assets/img/trainer8.png', '2025-04-20 06:43:50');

-- --------------------------------------------------------

--
-- Table structure for table `training_sessions`
--

CREATE TABLE `training_sessions` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `session_type` varchar(100) NOT NULL,
  `session_date` date NOT NULL,
  `session_time` time NOT NULL,
  `duration` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `priority` enum('low','medium','high','emergency') DEFAULT 'medium',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_sessions`
--

INSERT INTO `training_sessions` (`id`, `member_id`, `trainer_id`, `session_type`, `session_date`, `session_time`, `duration`, `notes`, `status`, `created_at`, `priority`, `last_updated`) VALUES
(1, 5, 1, 'Strength Training', '2025-07-07', '09:00:00', 60, 'hello', 'scheduled', '2025-04-16 20:55:08', 'medium', '2025-04-19 17:30:16'),
(2, 5, 1, 'Strength Training', '2025-06-05', '10:00:00', 30, 'hello', 'scheduled', '2025-04-16 20:55:35', 'medium', '2025-04-19 16:56:30'),
(3, 5, 3, 'Weight Loss', '2025-07-07', '09:00:00', 60, 'hi', 'scheduled', '2025-04-16 21:08:04', 'medium', '2025-04-19 16:56:30'),
(4, 5, 3, 'Cardio', '2025-09-09', '07:00:00', 60, 'hello', 'resolved', '2025-04-16 21:15:50', 'emergency', '2025-04-19 17:16:53'),
(5, 1, 1, 'Strength Training', '2025-07-07', '10:00:00', 30, 'emergency session', 'scheduled', '2025-04-19 06:19:31', 'medium', '2025-04-19 16:56:30'),
(6, 5, 4, 'Flexibility', '2025-07-07', '11:00:00', 60, 'now', 'scheduled', '2025-04-19 07:44:33', 'medium', '2025-04-19 16:56:30'),
(8, 1, 8, 'Rehabilitation', '2025-05-05', '09:00:00', 60, 'new', 'scheduled', '2025-04-19 08:19:08', 'medium', '2025-04-19 16:56:30'),
(9, 12, 1, 'Strength Training', '2025-05-05', '10:30:00', 60, 'Emergency session for the  competition', 'scheduled', '2025-04-25 11:44:33', 'medium', '2025-04-25 11:44:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('member','trainer','admin') NOT NULL DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `created_at`, `updated_at`, `last_login`, `status`) VALUES
(1, 'Kavinda Warakamura', 'kavinda@gmail.com', '$2y$10$uHVZOIM/4QZQ9kYTugM.5uz1iAxJhlUO48mcaiCiIHvPfx440o4uC', 'member', '2025-04-14 00:01:50', '2025-04-14 00:01:50', NULL, 'active'),
(2, 'Dilruksha Rajapaksha', 'dilru@gmail.com', '$2y$10$xNHxiHPuCdZVokkOLrRD.OGjpYhHGMGdxb0Aw1wEYh6N3jwhO9V6S', 'trainer', '2025-04-14 01:21:49', '2025-04-14 01:21:49', NULL, 'active'),
(3, 'Kavindi Rajapaksha', 'kavindi@gmail.com', '$2y$10$/.rCvvzS78xedHExQTZymu4U3lDXRxTEhq3xjbW0OZL9Ozc9t5Orm', 'admin', '2025-04-14 01:40:37', '2025-04-14 01:40:37', NULL, 'active'),
(4, 'Nishsanka Rajapaksha', 'nish@gmail.com', '$2y$10$75tvVsdorVBZumP1vTXaM.kwWEDsUtsKIe7WOEgnIcpOcL5po1ESq', 'trainer', '2025-04-15 05:01:46', '2025-04-15 05:01:46', NULL, 'active'),
(5, 'Biso Herath', 'biso@gmail.com', '$2y$10$xq37nzQo3bPtQxs1oqKL8eqiVPyD8WlozBY6FQS3W4/jguKODUCrm', 'member', '2025-04-16 07:37:05', '2025-04-16 07:37:05', NULL, 'active'),
(10, 'Isurangi Wickramasinhe', 'isurangani@gmail.com', '$2y$10$XDj3KV5Jpy8y/.lvLOcvWuAaxWOfuDx8lijqIpcxiDdCbnShtKAj.', 'member', '2025-04-25 04:52:05', '2025-04-25 04:52:05', NULL, 'active'),
(11, 'Nimna Rajapaksha', 'nimna321@gmail.com', '$2y$10$n35M6S058WbIoZ458y0bLuIJIgiDgGo9PBHiBU0UivJzAeboWkr6O', 'member', '2025-04-25 10:10:31', '2025-04-25 10:10:31', NULL, 'active'),
(12, 'Wathsala Rajapaksha', 'wathsala221@gmail.com', '$2y$10$QLFm86WWYld4OWkr5I4ZheYhqUCGK8qn/KGDRbffA1CtMVYt90xLy', 'member', '2025-04-25 10:15:05', '2025-04-25 10:15:05', NULL, 'active'),
(13, 'Nuwan Kulathunga', 'nuwan1994@gmail.com', '$2y$10$5svBq/aIE1L9mXHE3xjEOe.CFNJn9uWS6KrP50ptblT16.3d6bTGe', 'trainer', '2025-04-25 11:56:35', '2025-04-25 11:56:35', NULL, 'active'),
(14, 'Nayana Karunarathne', 'nayanaa32@gmail.com', '$2y$10$0b4Ia0LVUQmFEpWK2jpbMeWi4OPNP1WtubXAMo3M5YEURmtFOUOMe', 'member', '2025-04-25 13:56:26', '2025-04-25 13:56:26', NULL, 'active'),
(16, 'Kamal Aruna', 'kamal@gmail.com', '$2y$10$HQCHkt6GaKmCzmk0VP7h3uUwO664BTLcjxxUC4NqtA0G8QGklyeN6', 'member', '2025-04-25 13:57:14', '2025-04-25 13:57:14', NULL, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_actions`
--
ALTER TABLE `booking_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `class_registrations`
--
ALTER TABLE `class_registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `fitness_classes`
--
ALTER TABLE `fitness_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `member_profiles`
--
ALTER TABLE `member_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_id` (`member_id`);

--
-- Indexes for table `staff_profiles`
--
ALTER TABLE `staff_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_sessions`
--
ALTER TABLE `training_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_actions`
--
ALTER TABLE `booking_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `class_registrations`
--
ALTER TABLE `class_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `fitness_classes`
--
ALTER TABLE `fitness_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `member_profiles`
--
ALTER TABLE `member_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff_profiles`
--
ALTER TABLE `staff_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `training_sessions`
--
ALTER TABLE `training_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_actions`
--
ALTER TABLE `booking_actions`
  ADD CONSTRAINT `booking_actions_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `training_sessions` (`id`),
  ADD CONSTRAINT `booking_actions_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`);

--
-- Constraints for table `class_registrations`
--
ALTER TABLE `class_registrations`
  ADD CONSTRAINT `class_registrations_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fitness_classes`
--
ALTER TABLE `fitness_classes`
  ADD CONSTRAINT `fitness_classes_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`);

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `memberships`
--
ALTER TABLE `memberships`
  ADD CONSTRAINT `memberships_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `member_profiles`
--
ALTER TABLE `member_profiles`
  ADD CONSTRAINT `member_profiles_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_profiles`
--
ALTER TABLE `staff_profiles`
  ADD CONSTRAINT `staff_profiles_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

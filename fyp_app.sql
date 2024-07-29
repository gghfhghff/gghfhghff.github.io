-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2024 at 05:15 PM
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
-- Database: `fyp_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `coupon`
--

CREATE TABLE `coupon` (
  `coupon_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `points_required` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupon`
--

INSERT INTO `coupon` (`coupon_id`, `name`, `points_required`, `description`, `image_url`) VALUES
(1, 'NTUC $20 Voucher', 250, 'Get a $20 voucher for NTUC', 'images/ntuc_$20_voucher.jpg'),
(2, 'McDonalds $10 Voucher', 175, 'Get a $10 voucher for McDonalds', 'images/mcdonalds_$10_voucher.jpg'),
(3, 'Singapore Zoo Admission Ticket', 3000, 'One free admission ticket to Singapore Zoo', 'images/singapore_zoo_admission_ticket.jpg'),
(4, 'Liho 1 free drink', 100, 'Get one free drink at Liho', 'images/liho_one_free_drink.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `event_id` int(11) NOT NULL,
  `description` varchar(300) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `organiser_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`event_id`, `description`, `start_date`, `end_date`, `image_url`, `points`, `organiser_id`) VALUES
(11, 'Beach Clean-Up', '2024-06-01', '2024-06-01', 'images/beach_cleanup.jpg', 50, NULL),
(12, 'Visit to Old Folks Home', '2024-06-15', '2024-06-15', 'images/old_folks_home.jpg', 30, NULL),
(13, 'Tree Planting', '2024-07-01', '2024-07-01', 'images/tree_planting.jpg', 40, 5),
(14, 'Community Recycling Drive', '2024-07-15', '2024-07-15', 'images/recycling_drive.jpg', 25, 6),
(15, 'Park Beautification', '2024-08-01', '2024-08-01', 'images/park_beautification.jpg', 60, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `organiser`
--

CREATE TABLE `organiser` (
  `organiser_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `contact` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organiser`
--

INSERT INTO `organiser` (`organiser_id`, `name`, `contact`) VALUES
(5, 'organiser_tree', ''),
(6, 'organiser_recycle', '');

-- --------------------------------------------------------

--
-- Table structure for table `qr_code`
--

CREATE TABLE `qr_code` (
  `qr_id` int(11) NOT NULL,
  `data` varchar(255) NOT NULL,
  `points` int(11) NOT NULL,
  `is_scanned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qr_code`
--

INSERT INTO `qr_code` (`qr_id`, `data`, `points`, `is_scanned`) VALUES
(1, '664b3c4d3f8e3', 30, 0),
(2, '664b3d68189bf', 30, 0),
(3, '664b3e5071ff9', 30, 0),
(4, '664b3e56a7489', 40, 1),
(5, '664cae757daaf', 0, 0),
(6, '664cae80edf00', 0, 0),
(7, '664caf675efea', 0, 0),
(8, '664cafbb165b7', 0, 0),
(9, '664cafbbf20cb', 0, 0),
(10, '664cafbe162df', 0, 0),
(11, '664cafbf0186c', 0, 0),
(12, '664cb02c1c997', 40, 0),
(13, '664cb02d7181a', 40, 0),
(14, '664cb08ed7d50', 25, 0),
(15, '664cb55c686b4', 10000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `points` int(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `is_organiser` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `dob`, `email`, `password`, `points`, `is_admin`, `is_organiser`) VALUES
(1, 'lucius', '2004-03-04', 'lucius_neo@yahoo.com', '$2y$10$g90b.yWC9gGUaj8viUIpjeIGE9CSq/dZU6MgXkaxeQMNjSKQS2NR6', 6290, 0, 0),
(3, 'admin', '2000-03-04', 'admin@admin.com', '$2y$10$7kWC3vzUlSIytKWvXRLjt.59NzEWfmQomjhu5qaSGr/ExNbJTQZXy', 0, 1, 0),
(4, 'lordggh', '2024-05-04', 'lordgghxd@gmail.com', '$2y$10$ER9pnQ6ONdxK6xsOISagkOTcBGF7puSIygWp.lxRZomej9APWDsP6', 0, 0, 0),
(5, 'organiser_tree', '0000-00-00', 'organiser@tree.com', '$2y$10$CtlfeeTT/I5r2bTcV3aVC.fhgO6vEVgWMGWWfiie46Gw1xkyYJWX2', 0, 0, 1),
(6, 'organiser_recycle', '0000-00-00', 'organiser@recycle.com', '$2y$10$.RFfK.G1XVBFDcticnL0qujTtTHPu2SK4aor6GMTNaMzMwdfED7Yi', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_event`
--

CREATE TABLE `user_event` (
  `pe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_qr_code`
--

CREATE TABLE `user_qr_code` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `qr_id` int(11) NOT NULL,
  `scanned_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_qr_code`
--

INSERT INTO `user_qr_code` (`id`, `user_id`, `qr_id`, `scanned_at`) VALUES
(1, 1, 4, '2024-05-21 00:00:51'),
(2, 1, 15, '2024-05-21 22:53:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_reward`
--

CREATE TABLE `user_reward` (
  `user_reward_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `redeemed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_reward`
--

INSERT INTO `user_reward` (`user_reward_id`, `user_id`, `coupon_id`, `unique_id`, `redeemed_at`) VALUES
(1, 1, 1, '664cb9bd21bf2', '2024-05-21 15:11:57'),
(2, 1, 3, '664cb9ef03b61', '2024-05-21 15:12:47'),
(3, 1, 1, '664cba3a5e66b', '2024-05-21 15:14:02'),
(4, 1, 1, '664cba3ea46b8', '2024-05-21 15:14:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coupon`
--
ALTER TABLE `coupon`
  ADD PRIMARY KEY (`coupon_id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `fk_event_organiser` (`organiser_id`);

--
-- Indexes for table `organiser`
--
ALTER TABLE `organiser`
  ADD PRIMARY KEY (`organiser_id`);

--
-- Indexes for table `qr_code`
--
ALTER TABLE `qr_code`
  ADD PRIMARY KEY (`qr_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_event`
--
ALTER TABLE `user_event`
  ADD PRIMARY KEY (`pe_id`),
  ADD KEY `participant_has_event_ibfk_1` (`event_id`),
  ADD KEY `participant_has_event_ibfk_2` (`user_id`);

--
-- Indexes for table `user_qr_code`
--
ALTER TABLE `user_qr_code`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `qr_id` (`qr_id`);

--
-- Indexes for table `user_reward`
--
ALTER TABLE `user_reward`
  ADD PRIMARY KEY (`user_reward_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `coupon_id` (`coupon_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coupon`
--
ALTER TABLE `coupon`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `organiser`
--
ALTER TABLE `organiser`
  MODIFY `organiser_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `qr_code`
--
ALTER TABLE `qr_code`
  MODIFY `qr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_event`
--
ALTER TABLE `user_event`
  MODIFY `pe_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_qr_code`
--
ALTER TABLE `user_qr_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_reward`
--
ALTER TABLE `user_reward`
  MODIFY `user_reward_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_organiser` FOREIGN KEY (`organiser_id`) REFERENCES `organiser` (`organiser_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_event`
--
ALTER TABLE `user_event`
  ADD CONSTRAINT `user_event_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_event_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_qr_code`
--
ALTER TABLE `user_qr_code`
  ADD CONSTRAINT `user_qr_code_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_qr_code_ibfk_2` FOREIGN KEY (`qr_id`) REFERENCES `qr_code` (`qr_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_reward`
--
ALTER TABLE `user_reward`
  ADD CONSTRAINT `user_reward_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_reward_ibfk_2` FOREIGN KEY (`coupon_id`) REFERENCES `coupon` (`coupon_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

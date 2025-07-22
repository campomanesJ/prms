-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2025 at 01:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cenroormoc`
--

-- --------------------------------------------------------

--
-- Table structure for table `cenro_release_info`
--

CREATE TABLE `cenro_release_info` (
  `id` int(11) NOT NULL,
  `routing_number` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `particular` text DEFAULT NULL,
  `date_time_released_from_cenro` datetime DEFAULT NULL,
  `date_time_released_from` datetime DEFAULT NULL,
  `responsible_person` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `sections` text DEFAULT NULL,
  `date_received` datetime DEFAULT NULL,
  `acted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cenro_release_info`
--

INSERT INTO `cenro_release_info` (`id`, `routing_number`, `full_name`, `particular`, `date_time_released_from_cenro`, `date_time_released_from`, `responsible_person`, `remarks`, `sections`, `date_received`, `acted_date`) VALUES
(31, 'ABCD-12', 'hahahha', 'no particular', '2025-02-06 15:15:00', '2025-02-07 15:15:00', 'Secret', 'awawaw', 'MES', '2025-02-13 15:15:00', '2025-02-12 15:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `docs_transmittalform`
--

CREATE TABLE `docs_transmittalform` (
  `id` int(11) NOT NULL,
  `date_of_request` date NOT NULL,
  `div_sec_unit_served` text NOT NULL,
  `request_description` text NOT NULL,
  `date_finished` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `icte_inventory`
--

CREATE TABLE `icte_inventory` (
  `id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `item_no` varchar(50) NOT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `article_item` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `property_no` varchar(100) NOT NULL,
  `unit_value` decimal(10,2) NOT NULL,
  `acquisition_date` date NOT NULL,
  `person_accountable` varchar(255) NOT NULL,
  `office` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `ageing_years` int(11) DEFAULT 0,
  `ageing_months` int(11) DEFAULT 0,
  `ageing_years_months` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `icte_inventory`
--

INSERT INTO `icte_inventory` (`id`, `user`, `item_no`, `serial_number`, `article_item`, `description`, `property_no`, `unit_value`, `acquisition_date`, `person_accountable`, `office`, `remarks`, `ageing_years`, `ageing_months`, `ageing_years_months`) VALUES
(38, '1', '1', '1', '1', '1', '1', 1.00, '2025-02-12', '1', '1', '1', 1, 1, '1');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `item_no` varchar(50) NOT NULL,
  `article_item` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `property_no` varchar(100) NOT NULL,
  `unit_value` decimal(10,2) NOT NULL,
  `acquisition_date` date NOT NULL,
  `person_accountable` varchar(255) NOT NULL,
  `office` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `ageing_years` int(11) DEFAULT 0,
  `ageing_months` int(11) DEFAULT 0,
  `ageing_years_months` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transmittalform`
--

CREATE TABLE `transmittalform` (
  `id` int(11) NOT NULL,
  `date_of_request` date NOT NULL,
  `div_sec_unit_served` varchar(255) NOT NULL,
  `request_description` text NOT NULL,
  `date_finished` date DEFAULT NULL,
  `rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transmittalform`
--

INSERT INTO `transmittalform` (`id`, `date_of_request`, `div_sec_unit_served`, `request_description`, `date_finished`, `rating`) VALUES
(2, '2025-02-14', 'eeeee', 'haahaha', '2025-02-12', 5),
(3, '2025-02-14', 'eeeee', 'hhahahha', '2025-02-20', 5),
(4, '2025-02-13', 'eeeee', 'hehehehhe', '2025-02-19', NULL),
(5, '2025-02-20', 'eeeee', 'hhahahahaahh', '2025-02-20', NULL),
(6, '2025-02-13', 'eeeee', 'fffffff', '2025-02-20', 4),
(7, '2025-02-14', 'wwwwww', 'wwwwwwwwwwwwwwwwwwwwwwwwwww', '2025-02-20', NULL),
(9, '2025-02-21', '12', 'hi', '2025-03-05', NULL),
(10, '2025-02-14', 'ggggg', 'ggg', '2025-02-14', NULL),
(11, '2025-02-13', 'ddddddddddd', 'hahhahaahhah', '2025-02-21', 5),
(12, '2025-02-08', 'wwwwww', 'wwwwwwwwwwww', '2025-02-21', NULL),
(13, '2025-02-26', 'ddddddddddddddddddddddd', 'tattatattata', '2025-02-21', NULL),
(14, '2025-02-13', 'HIIIIII', 'HIIIIIII', '2025-02-18', NULL),
(15, '2025-02-13', 'wwwwww', 'hahhahahaha', '2025-02-19', NULL),
(16, '2025-03-07', 'wwwwww', 'hahahaha', '2025-03-07', 5),
(17, '2025-02-13', '12', 'eeeeeeeeee', '2025-02-28', 5),
(18, '2025-02-22', 'eeeee', 'hhaahhahha', '2025-02-22', 4);

-- --------------------------------------------------------

--
-- Table structure for table `transmit_docs`
--

CREATE TABLE `transmit_docs` (
  `id` int(11) NOT NULL,
  `project_name` text NOT NULL,
  `particular` text NOT NULL,
  `amount_remarks` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transmit_docs`
--

INSERT INTO `transmit_docs` (`id`, `project_name`, `particular`, `amount_remarks`, `date`) VALUES
(2, 'heheh', 'hhheheeh', 'heheheh', '2025-02-12'),
(3, '1', '1', '1', '0000-00-00'),
(4, '1', '11', '1111111', '2025-02-12'),
(5, 'hahahahah', 'hhahahahaa', 'hahahahahahha', '2025-02-04'),
(6, 'Test Project', 'Test Particular', 'Test Remarks', '2025-02-12'),
(7, 'hahhqh', 'ahahahh', 'ahhahah', '2025-02-14'),
(8, 'hahahhah', 'ahhahahah', 'ahhahahahaha', '2025-02-15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `role`, `username`, `email`, `password`, `created_at`) VALUES
(7, 'ADMIN', 'admin', 'admin@gmail.com', '$2y$10$nzYbTH8yF9HRiPvGsfUaHOGsrMpy7fENO02mq/BTnQZsa1Dm1aoJy', '2025-02-17 08:21:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cenro_release_info`
--
ALTER TABLE `cenro_release_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `docs_transmittalform`
--
ALTER TABLE `docs_transmittalform`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `icte_inventory`
--
ALTER TABLE `icte_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transmittalform`
--
ALTER TABLE `transmittalform`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transmit_docs`
--
ALTER TABLE `transmit_docs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cenro_release_info`
--
ALTER TABLE `cenro_release_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `docs_transmittalform`
--
ALTER TABLE `docs_transmittalform`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `icte_inventory`
--
ALTER TABLE `icte_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transmittalform`
--
ALTER TABLE `transmittalform`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `transmit_docs`
--
ALTER TABLE `transmit_docs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

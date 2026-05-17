-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2026 at 05:03 PM
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
-- Database: `vento`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('operations_admin','it_admin','compensation_manager','inventory_admin') NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `application_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `status`, `application_file`, `created_at`, `updated_at`) VALUES
(1, 'Operations', 'Admin', 'operations_admin@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'operations_admin', 'approved', NULL, '2026-05-17 09:36:58', '2026-05-17 09:36:58'),
(2, 'IT', 'Admin', 'it_admin@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'it_admin', 'approved', NULL, '2026-05-17 09:36:58', '2026-05-17 09:36:58'),
(3, 'Compensation', 'Manager', 'compensation_manager@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'compensation_manager', 'approved', NULL, '2026-05-17 09:36:58', '2026-05-17 09:36:58'),
(4, 'Inventory', 'Admin', 'inventory_admin@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'inventory_admin', 'approved', NULL, '2026-05-17 09:36:58', '2026-05-17 09:36:58');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('stock_holder','inventory_clerk','it_security') NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `application_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `status`, `application_file`, `created_at`, `updated_at`) VALUES
(1, 'Stock', 'Holder', 'stock_holder@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'stock_holder', 'approved', NULL, '2026-05-17 09:36:58', '2026-05-17 09:36:58'),
(2, 'Inventory', 'Clerk', 'inventory_clerk@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'inventory_clerk', 'approved', NULL, '2026-05-17 09:36:58', '2026-05-17 09:36:58'),
(3, 'IT', 'Security', 'it_security@gmail.com', '$2y$10$HPrsfJyv/2A0Co6b4cr.DuYg3uHRholweq.4OYUaNDh5avJ7OkDTS', 'it_security', 'approved', NULL, '2026-05-17 09:36:58', '2026-05-17 09:36:58');

-- --------------------------------------------------------

--
-- Table structure for table `hr`
--

CREATE TABLE `hr` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr`
--

INSERT INTO `hr` (`id`, `first_name`, `last_name`, `email`, `password`, `created_at`) VALUES
(1, 'Main', 'HR', 'hr@vento-corp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-05-17 08:44:54');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) GENERATED ALWAYS AS (case when `quantity` = 0 then 'Out of Stock' when `quantity` < 15 then 'Low' else 'Good' end) STORED,
  `last_verification_image` varchar(255) DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `name`, `quantity`, `updated_at`, `last_verification_image`, `updated_by`) VALUES
(1, 'SOFA', 0, '2026-05-17 09:36:58', NULL, NULL),
(2, 'STOOL', 0, '2026-05-17 09:36:58', NULL, NULL),
(3, 'FOLDING CHAIR', 20, '2026-05-17 14:00:40', 'verification_3_1779026440.png', 'Inventory '),
(4, 'ARM CHAIR', 15, '2026-05-17 09:56:30', 'verification_4_1779011790.png', 'Inventory '),
(5, 'RECLINER', 0, '2026-05-17 09:36:58', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rejection_history`
--

CREATE TABLE `rejection_history` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `application_file` varchar(255) DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rejected_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `request_image` varchar(255) NOT NULL,
  `item_requested` varchar(100) NOT NULL,
  `requested_by` varchar(100) NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Rejected','Completed') DEFAULT 'Pending',
  `handled_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `request_image`, `item_requested`, `requested_by`, `requested_at`, `status`, `handled_by`) VALUES
(1, 'request_1779024851_6977.png', 'FOLDING CHAIR', 'Inventory ', '2026-05-17 13:34:11', 'Approved', 'Operations ');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `assigned_to` varchar(100) NOT NULL,
  `status` enum('Assigned','In Progress','Delivered') DEFAULT 'Assigned',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `request_id`, `assigned_to`, `status`, `assigned_at`, `completed_at`) VALUES
(1, 1, 'Stock Holder', 'Delivered', '2026-05-17 13:48:12', '2026-05-17 14:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `hr`
--
ALTER TABLE `hr`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rejection_history`
--
ALTER TABLE `rejection_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hr`
--
ALTER TABLE `hr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rejection_history`
--
ALTER TABLE `rejection_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

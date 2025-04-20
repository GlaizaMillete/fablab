-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2025 at 02:40 PM
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
-- Database: `fablab_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminfablab`
--

CREATE TABLE `adminfablab` (
  `adminID` int(11) NOT NULL,
  `adminUsername` varchar(50) NOT NULL,
  `adminPassword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminfablab`
--

INSERT INTO `adminfablab` (`adminID`, `adminUsername`, `adminPassword`) VALUES
(1, 'admin', '$2y$10$zJpQwu8hEW50DtgK5DPUuuk5Of5PY.WUq.ilImUr/rxYN8Mzzc1hS');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `client_profile` varchar(255) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `equipment` text NOT NULL,
  `billing_date` date NOT NULL,
  `total_invoice` decimal(10,2) NOT NULL,
  `billing_pdf` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`id`, `client_profile`, `client_name`, `equipment`, `billing_date`, `total_invoice`, `billing_pdf`) VALUES
(342, 'STUDENT', 'John Doe', '3D Printer', '2024-01-15', 1500.00, ''),
(343, 'MSME', 'Jane Smith', 'CNC Machine', '2024-01-20', 2500.00, ''),
(344, 'OTHERS', 'Michael Johnson', 'Laser Engraver', '2024-01-25', 3200.00, ''),
(345, 'STUDENT', 'Emily Davis', '3D Printer', '2024-01-28', 1800.00, ''),
(346, 'MSME', 'David Wilson', '3D Printer', '2024-01-30', 2000.00, ''),
(347, 'OTHERS', 'Sarah Brown', 'Laser Engraver', '2024-02-10', 3500.00, ''),
(348, 'STUDENT', 'James Miller', 'CNC Machine', '2024-02-15', 2700.00, ''),
(349, 'MSME', 'Emma Garcia', '3D Printer', '2024-02-20', 2100.00, ''),
(350, 'OTHERS', 'Benjamin Lee', '3D Printer', '2024-02-25', 3300.00, ''),
(351, 'STUDENT', 'Olivia Martinez', 'CNC Machine', '2024-02-28', 2900.00, ''),
(352, 'MSME', 'William Anderson', '3D Printer', '2024-03-10', 2200.00, ''),
(353, 'OTHERS', 'Sophia Thomas', 'Laser Engraver', '2024-03-15', 3100.00, ''),
(354, 'STUDENT', 'Alexander Hernandez', 'CNC Machine', '2024-03-20', 2600.00, ''),
(355, 'MSME', 'Ava Robinson', '3D Printer', '2024-03-25', 2700.00, ''),
(356, 'OTHERS', 'Ethan Clark', '3D Printer', '2024-03-30', 2800.00, ''),
(357, 'STUDENT', 'Daniel Young', 'Laser Engraver', '2025-01-12', 3100.00, ''),
(358, 'MSME', 'Mia Scott', 'CNC Machine', '2025-01-18', 2900.00, ''),
(359, 'OTHERS', 'Jacob Green', '3D Printer', '2025-01-22', 2400.00, ''),
(360, 'STUDENT', 'Charlotte Adams', '3D Printer', '2025-01-27', 2200.00, ''),
(361, 'MSME', 'Logan Baker', 'CNC Machine', '2025-01-31', 2600.00, ''),
(362, 'OTHERS', 'Isabella Gonzalez', '3D Printer', '2025-02-10', 3200.00, ''),
(363, 'STUDENT', 'Lucas Perez', 'Laser Engraver', '2025-02-15', 2800.00, ''),
(364, 'MSME', 'Harper Nelson', '3D Printer', '2025-02-22', 2500.00, ''),
(365, 'OTHERS', 'Michael Carter', 'CNC Machine', '2025-02-27', 3100.00, ''),
(366, 'STUDENT', 'Amelia Mitchell', '3D Printer', '2025-02-28', 2700.00, ''),
(367, 'MSME', 'Henry Ramirez', 'CNC Machine', '2025-03-10', 3300.00, ''),
(368, 'OTHERS', 'Ella Campbell', '3D Printer', '2025-03-15', 2900.00, ''),
(369, 'STUDENT', 'Samuel Rodriguez', '3D Printer', '2025-03-20', 2500.00, ''),
(370, 'MSME', 'Lily Parker', 'Laser Engraver', '2025-03-25', 2700.00, ''),
(371, 'OTHERS', 'Nathan Torres', '3D Printer', '2025-03-30', 2600.00, ''),
(372, 'STUDENT', 'Gab', '3D Printer,CNC Machine', '2025-03-21', 56789.00, ''),
(373, 'Personal', 'Rey', '3D Printer,Laser Cutting Machine', '2025-03-21', 678.83, ''),
(374, 'VIP', 'qwerty', '3D Printer,Laser Cutting Machine,Print and Cut Machine,Vacuum Forming', '2024-06-20', 345.50, ''),
(385, 'STUDENT', 'jade', '3D Printer, Laser Cutting Machine, Print and Cut Machine, CNC MachineB', '2024-03-09', 1.00, 'Class-Schedule.pdf'),
(386, 'STUDENT', 'jade', '3D Printer, 3D Scanner', '2023-01-18', 123456.00, '67ee43883cb70_inside01_BROCHURE.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` enum('Available','In Maintenance','Out of Service') DEFAULT 'Available',
  `last_maintenance_date` date DEFAULT NULL,
  `next_maintenance_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `name`, `type`, `status`, `last_maintenance_date`, `next_maintenance_date`, `notes`) VALUES
(1, '3D Printer', 'Manufacturing', 'Available', NULL, NULL, NULL),
(2, '3D Scanner', 'Scanning', 'Available', NULL, NULL, NULL),
(3, 'Laser Cutting Machine', 'Cutting', 'Available', NULL, NULL, NULL),
(4, 'CNC Machine (Big)', 'Milling', 'Available', NULL, NULL, NULL),
(5, 'Embroidery Machine (One Head)', 'Textile', 'Available', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `feedback_pdf` varchar(255) NOT NULL,
  `feedback_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `client_name`, `feedback_pdf`, `feedback_date`) VALUES
(1, 'John Doe', '', '2025-03-02'),
(2, 'Jane Smith', '', '2025-03-06'),
(3, 'Michael Johnson', 'feedback_report.pdf', '2025-03-10'),
(5, 'David Wilson', 'client_feedback.pdf', '2025-03-20'),
(10, 'Jaded', 'inside01_BROCHURE.pdf', '2025-03-10'),
(11, 'larvie', 'outside_01BROCHURE.pdf', '2025-04-06'),
(12, 'asad', 'student-paper-setup-guide.pdf', '2025-04-14'),
(13, 'aaaaaa', 'Millete_andrea_03 eLMS Activity 1 - ARG.pdf', '2025-04-14');

-- --------------------------------------------------------

--
-- Table structure for table `job_requests`
--

CREATE TABLE `job_requests` (
  `id` int(11) NOT NULL,
  `request_title` varchar(100) NOT NULL,
  `request_date` date NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `client_profile` varchar(50) NOT NULL,
  `client_profile_other` varchar(50) DEFAULT NULL,
  `request_description` text NOT NULL,
  `equipment` text DEFAULT NULL,
  `priority` enum('Low','Medium','High') NOT NULL,
  `completion_date` date NOT NULL,
  `reference_file` varchar(255) DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_requests`
--

INSERT INTO `job_requests` (`id`, `request_title`, `request_date`, `client_name`, `contact_number`, `client_profile`, `client_profile_other`, `request_description`, `equipment`, `priority`, `completion_date`, `reference_file`, `status`, `created_at`, `updated_at`) VALUES
(1, 'asdsadsa', '2025-04-14', 'asa', '09471918324', 'STUDENT', NULL, 'asdsadsadsadsads', '3D Printer, 3D Scanner, Laser Cutting Machine', 'Medium', '2025-04-16', '67fcb14a3c161.pdf', 'Pending', '2025-04-14 06:55:06', '2025-04-14 06:55:06');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `staff_name` varchar(255) NOT NULL,
  `action` text NOT NULL,
  `log_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `staff_name`, `action`, `log_date`) VALUES
(1, 'alice_jones', 'Added billing for client: asa', '2025-04-10 11:32:37'),
(2, 'alice_jones', 'Updated billing for client asadd\'s Client Name: \'asad\' -> \'asadd\'', '2025-04-10 11:50:10'),
(3, 'alice_jones', 'Updated billing for client Glaiza Baba\'s Client Name: \'asadd\' -> \'Glaiza Baba\', Client Profile: \'MSME\' -> \'STUDENT\', Equipment: \'3D Scanner, CNC Machine (Big), CNC Machine (Small)\' -> \'3D Printer, 3D Scanner, Laser Cutting Machine, Print and Cut Machine, CNC Machine (Big), CNC Machine (Small), Vinyl Cutter, Embroidery Machine (One Head), Embroidery Machine (Four Heads), Flatbed Cutter, Vacuum Forming, Water Jet Machine\'', '2025-04-10 14:28:53'),
(4, 'alice_jones', 'Added feedback for client: asad', '2025-04-14 15:59:47'),
(5, 'alice_jones', 'Added feedback for client: aaaaaa', '2025-04-14 16:12:20');

-- --------------------------------------------------------

--
-- Table structure for table `stafffablab`
--

CREATE TABLE `stafffablab` (
  `staffID` int(11) NOT NULL,
  `staffUsername` varchar(50) NOT NULL,
  `staffPassword` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stafffablab`
--

INSERT INTO `stafffablab` (`staffID`, `staffUsername`, `staffPassword`, `status`) VALUES
(2, 'jane_smith', '$2y$10$mugZc6s.zZrF0njk3ZwDPOiYo0wfjQYsJuhNf3ydS/4IpN1aTWfO.', 'Active'),
(3, 'alice_jones', '$2y$10$X/FsHKWBhAGfjafxEg0Z4uDFA0Q0e7/XbhRQLLtgsHdfUgtgcQbky', 'Active'),
(4, 'jadeds', '$2y$10$Ufo.kgLVrFZD1.BA/aUkD.ousOhnAU742A1x3UwX1X9EkQ5m9I/7W', 'Active'),
(5, 'sss', '$2y$10$IwwtjIKCM3cXO9EqmpRtNO12WCMMLFTEqukFMllVkGV6atmaq9YqK', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminfablab`
--
ALTER TABLE `adminfablab`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_requests`
--
ALTER TABLE `job_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stafffablab`
--
ALTER TABLE `stafffablab`
  ADD PRIMARY KEY (`staffID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminfablab`
--
ALTER TABLE `adminfablab`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=388;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `job_requests`
--
ALTER TABLE `job_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stafffablab`
--
ALTER TABLE `stafffablab`
  MODIFY `staffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

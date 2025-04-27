-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2025 at 12:37 AM
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
  `billing_date` date NOT NULL,
  `total_invoice` decimal(10,2) NOT NULL,
  `billing_pdf` varchar(255) NOT NULL,
  `no` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `completion_date` date DEFAULT NULL,
  `prepared_by` varchar(255) NOT NULL,
  `approved_by` varchar(255) NOT NULL,
  `or_no` int(11) NOT NULL,
  `payment_received_by` varchar(255) NOT NULL,
  `receipt_acknowledged_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`id`, `client_profile`, `client_name`, `billing_date`, `total_invoice`, `billing_pdf`, `no`, `address`, `contact_no`, `description`, `completion_date`, `prepared_by`, `approved_by`, `or_no`, `payment_received_by`, `receipt_acknowledged_by`) VALUES
(388, 'STUDENT', 'lapa', '2025-04-28', 250.00, '680e996c2d719_hertalesdiary_3586003323029432749.jpg', 12, 'asdasdsadsa', '09471918324', 'Project Description 3D Print', NULL, 'alice_jones', 'aaaaaaaaaa', 12312321, 'jaded', 'asdasdsad'),
(389, 'MSME', 'larvie', '2025-04-28', 19.00, '680ea06b551ff_INSIDE_BROCHURE02.jpg', 12, 'asdsadsa', '09471918324', 'asdasdsadsadada', NULL, 'alice_jones', 'aaaaaaaaaa', 12121212, 'Cashier', 'asdasdsad');

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
  `personal_name` varchar(255) NOT NULL,
  `request_date` date NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `gender_optional` varchar(255) DEFAULT NULL,
  `age` int(11) NOT NULL,
  `designation` varchar(50) NOT NULL,
  `designation_other` varchar(255) DEFAULT NULL,
  `company` varchar(255) NOT NULL,
  `service_requested` text NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `equipment` text DEFAULT NULL,
  `hand_tools_other` varchar(255) DEFAULT NULL,
  `equipment_other` varchar(255) DEFAULT NULL,
  `consultation_mode` varchar(50) DEFAULT NULL,
  `consultation_schedule` varchar(255) DEFAULT NULL,
  `equipment_schedule` datetime DEFAULT NULL,
  `work_description` text NOT NULL,
  `personnel_name` varchar(255) DEFAULT NULL,
  `personnel_date` date DEFAULT NULL,
  `reference_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_requests`
--

INSERT INTO `job_requests` (`id`, `personal_name`, `request_date`, `client_name`, `address`, `gender`, `gender_optional`, `age`, `designation`, `designation_other`, `company`, `service_requested`, `contact_number`, `equipment`, `hand_tools_other`, `equipment_other`, `consultation_mode`, `consultation_schedule`, `equipment_schedule`, `work_description`, `personnel_name`, `personnel_date`, `reference_file`, `created_at`, `updated_at`) VALUES
(2, '', '2026-04-20', 'Jaded Company', 'aasdasdsa', 'Male', '', 26, 'Student', '', 'N/A', 'Equipment', '09471918324', '3D Printer, Print and Cut Machine', '', '', '', '', '2025-04-29 11:26:00', 'asdasdasdasdas', 'Dionnie', '2025-04-20', '68051242b350c.jpg', '2025-04-20 15:26:58', '2025-04-20 19:24:00'),
(3, '', '2025-01-20', 'Jaded', 'asdasdasd', 'Male', '', 28, 'Student', '', 'N/A', 'Product/Design/Consultation', '09471918324', '', '', '', '', '', '0000-00-00 00:00:00', 'asdsadasd', '', '0000-00-00', NULL, '2025-04-20 15:29:19', '2025-04-20 19:24:13'),
(4, '', '2025-10-21', 'lala', 'asdasd', 'Female', '', 22, 'Others', 'Speedster', 'N/A', 'Training/Tour/Orientation', '9471918323', '', '', '', '', '', '2025-04-22 16:26:00', 'asadasd', 'Dionnie', '2025-04-21', '68052f2195bc4-inside01_BROCHURE.pdf', '2025-04-20 17:30:09', '2025-04-20 19:24:27'),
(5, 'hermione', '2024-04-21', 'Lethal Company', 'asdsadsa', 'Prefer not to say', '', 35, 'Teacher', '', 'N/A', 'Equipment', '96564', 'Hand Tools', 'Hand Tool', '', '', '', '0000-00-00 00:00:00', 'asdasdasdasdaas', 'Dionnie', '2025-04-21', '680531d4c8d30-outside_BROCHURE.jpg', '2025-04-20 17:41:40', '2025-04-20 19:24:42'),
(6, 'Tralala', '2025-04-21', 'Tralavero Tralala', 'adasdsa', 'Prefer not to say', 'Bisexual', 25, 'MSME/Entrepreneur', '', 'N/A', 'Equipment', '1165', 'Print and Cut Machine, CNC Machine (Big), Other', '', 'nice', '', '', '2025-04-22 18:30:00', 'asdadad', 'Dionnie', '2025-04-21', '68055955b5cd6-df41b9b2-d857-4db3-8626-5b197633d501.jpg', '2025-04-20 20:30:13', '2025-04-20 20:30:13');

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
(5, 'alice_jones', 'Added feedback for client: aaaaaa', '2025-04-14 16:12:20'),
(6, 'alice_jones', 'Added client profile and service request for client: Jaded Company', '2025-04-20 23:26:58'),
(7, 'alice_jones', 'Added client profile and service request for client: Jaded', '2025-04-20 23:29:19'),
(8, 'alice_jones', 'Added client profile and service request for client: lala', '2025-04-21 01:30:09'),
(9, 'alice_jones', 'Added client profile and service request for client: Lethal Company', '2025-04-21 01:41:40'),
(10, 'alice_jones', 'Added client profile and service request for client: Tralavero Tralala', '2025-04-21 04:30:13'),
(11, 'alice_jones', 'Added billing for client: lapa', '2025-04-28 04:54:04'),
(12, 'alice_jones', 'Added billing for client: larvie', '2025-04-28 05:23:55');

-- --------------------------------------------------------

--
-- Table structure for table `service_details`
--

CREATE TABLE `service_details` (
  `id` int(11) NOT NULL,
  `billing_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `rate` varchar(50) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_details`
--

INSERT INTO `service_details` (`id`, `billing_id`, `service_name`, `unit`, `rate`, `total_cost`) VALUES
(1, 389, 'Jade', '1', '1', 10.00),
(2, 389, 'Raposa', '2', '2', 9.00);

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
-- Indexes for table `service_details`
--
ALTER TABLE `service_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `billing_id` (`billing_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=390;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `service_details`
--
ALTER TABLE `service_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stafffablab`
--
ALTER TABLE `stafffablab`
  MODIFY `staffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `service_details`
--
ALTER TABLE `service_details`
  ADD CONSTRAINT `service_details_ibfk_1` FOREIGN KEY (`billing_id`) REFERENCES `billing` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

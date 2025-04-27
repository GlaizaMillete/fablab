-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 10:32 PM
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
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `client_profile` varchar(255) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `equipment` text NOT NULL,
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

INSERT INTO `billing` (`id`, `client_profile`, `client_name`, `equipment`, `billing_date`, `total_invoice`, `billing_pdf`, `no`, `address`, `contact_no`, `description`, `completion_date`, `prepared_by`, `approved_by`, `or_no`, `payment_received_by`, `receipt_acknowledged_by`) VALUES
(342, 'STUDENT', 'John Doe', '3D Printer', '2024-01-15', 1500.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(343, 'MSME', 'Jane Smith', 'CNC Machine', '2024-01-20', 2500.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(344, 'OTHERS', 'Michael Johnson', 'Laser Engraver', '2024-01-25', 3200.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(345, 'STUDENT', 'Emily Davis', '3D Printer', '2024-01-28', 1800.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(346, 'MSME', 'David Wilson', '3D Printer', '2024-01-30', 2000.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(347, 'OTHERS', 'Sarah Brown', 'Laser Engraver', '2024-02-10', 3500.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(348, 'STUDENT', 'James Miller', 'CNC Machine', '2024-02-15', 2700.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(349, 'MSME', 'Emma Garcia', '3D Printer', '2024-02-20', 2100.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(350, 'OTHERS', 'Benjamin Lee', '3D Printer', '2024-02-25', 3300.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(351, 'STUDENT', 'Olivia Martinez', 'CNC Machine', '2024-02-28', 2900.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(352, 'MSME', 'William Anderson', '3D Printer', '2024-03-10', 2200.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(353, 'OTHERS', 'Sophia Thomas', 'Laser Engraver', '2024-03-15', 3100.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(354, 'STUDENT', 'Alexander Hernandez', 'CNC Machine', '2024-03-20', 2600.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(355, 'MSME', 'Ava Robinson', '3D Printer', '2024-03-25', 2700.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(356, 'OTHERS', 'Ethan Clark', '3D Printer', '2024-03-30', 2800.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(357, 'STUDENT', 'Daniel Young', 'Laser Engraver', '2025-01-12', 3100.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(358, 'MSME', 'Mia Scott', 'CNC Machine', '2025-01-18', 2900.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(359, 'OTHERS', 'Jacob Green', '3D Printer', '2025-01-22', 2400.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(360, 'STUDENT', 'Charlotte Adams', '3D Printer', '2025-01-27', 2200.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(361, 'MSME', 'Logan Baker', 'CNC Machine', '2025-01-31', 2600.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(362, 'OTHERS', 'Isabella Gonzalez', '3D Printer', '2025-02-10', 3200.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(363, 'STUDENT', 'Lucas Perez', 'Laser Engraver', '2025-02-15', 2800.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(364, 'MSME', 'Harper Nelson', '3D Printer', '2025-02-22', 2500.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(365, 'OTHERS', 'Michael Carter', 'CNC Machine', '2025-02-27', 3100.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(366, 'STUDENT', 'Amelia Mitchell', '3D Printer', '2025-02-28', 2700.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(367, 'MSME', 'Henry Ramirez', 'CNC Machine', '2025-03-10', 3300.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(368, 'OTHERS', 'Ella Campbell', '3D Printer', '2025-03-15', 2900.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(369, 'STUDENT', 'Samuel Rodriguez', '3D Printer', '2025-03-20', 2500.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(370, 'MSME', 'Lily Parker', 'Laser Engraver', '2025-03-25', 2700.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(371, 'OTHERS', 'Nathan Torres', '3D Printer', '2025-03-30', 2600.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(372, 'STUDENT', 'Gab', '3D Printer,CNC Machine', '2025-03-21', 56789.00, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(373, 'Personal', 'Rey', '3D Printer,Laser Cutting Machine', '2025-03-21', 678.83, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(374, 'VIP', 'qwerty', '3D Printer,Laser Cutting Machine,Print and Cut Machine,Vacuum Forming', '2024-06-20', 345.50, '', 0, '', '', '', NULL, '', '', 0, '', ''),
(385, 'STUDENT', 'jade', '3D Printer, Laser Cutting Machine, Print and Cut Machine, CNC MachineB', '2024-03-09', 1.00, 'Class-Schedule.pdf', 0, '', '', '', NULL, '', '', 0, '', ''),
(386, 'STUDENT', 'jade', '3D Printer, 3D Scanner', '2023-01-18', 123456.00, '67ee43883cb70_inside01_BROCHURE.pdf', 0, '', '', '', NULL, '', '', 0, '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=388;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

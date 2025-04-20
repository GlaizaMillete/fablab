-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2025 at 07:57 PM
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
(2, '', '2025-04-20', 'Jaded Company', 'aasdasdsa', 'Male', '', 26, 'Student', '', 'N/A', 'Equipment', '09471918324', '3D Printer, Print and Cut Machine', '', '', '', '', '2025-04-29 11:26:00', 'asdasdasdasdas', 'Dionnie', '2025-04-20', '68051242b350c.jpg', '2025-04-20 15:26:58', '2025-04-20 15:26:58'),
(3, '', '2025-04-20', 'Jaded', 'asdasdasd', 'Male', '', 28, 'Student', '', 'N/A', 'Product/Design/Consultation', '09471918324', '', '', '', '', '', '0000-00-00 00:00:00', 'asdsadasd', '', '0000-00-00', NULL, '2025-04-20 15:29:19', '2025-04-20 15:29:19'),
(4, '', '2025-04-21', 'lala', 'asdasd', 'Female', '', 22, 'Others', 'Speedster', 'N/A', 'Training/Tour/Orientation', '9471918323', '', '', '', '', '', '2025-04-22 16:26:00', 'asadasd', 'Dionnie', '2025-04-21', '68052f2195bc4-inside01_BROCHURE.pdf', '2025-04-20 17:30:09', '2025-04-20 17:30:09'),
(5, 'hermione', '2025-04-21', 'Lethal Company', 'asdsadsa', 'Prefer not to say', '', 35, 'Teacher', '', 'N/A', 'Equipment', '96564', 'Hand Tools', 'Hand Tool', '', '', '', '0000-00-00 00:00:00', 'asdasdasdasdaas', 'Dionnie', '2025-04-21', '680531d4c8d30-outside_BROCHURE.jpg', '2025-04-20 17:41:40', '2025-04-20 17:41:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `job_requests`
--
ALTER TABLE `job_requests`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `job_requests`
--
ALTER TABLE `job_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

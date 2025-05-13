-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 10:09 AM
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
  `no` int(11) NOT NULL,
  `client_profile` varchar(255) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `billing_date` date NOT NULL,
  `total_invoice` decimal(10,2) NOT NULL,
  `billing_pdf` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `completion_date` date DEFAULT NULL,
  `prepared_by` varchar(255) NOT NULL,
  `prepared_date` date DEFAULT NULL,
  `approved_by` varchar(255) NOT NULL,
  `or_no` int(11) NOT NULL,
  `or_favor` varchar(255) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_received_by` varchar(255) NOT NULL,
  `receipt_acknowledged_by` varchar(255) NOT NULL,
  `receipt_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`no`, `client_profile`, `client_name`, `billing_date`, `total_invoice`, `billing_pdf`, `address`, `contact_no`, `description`, `completion_date`, `prepared_by`, `prepared_date`, `approved_by`, `or_no`, `or_favor`, `payment_date`, `payment_received_by`, `receipt_acknowledged_by`, `receipt_date`) VALUES
(388, 'STUDENT', 'Marsh', '2025-04-28', 250.00, '680e996c2d719_hertalesdiary_3586003323029432749.jpg', 'asdasdsadsa', '09471918324', 'Project Description 3D Print', NULL, 'alice_jones', NULL, 'aaaaaaaaaa', 12312321, '', NULL, 'jaded', 'asdasdsad', NULL),
(389, 'MSME', 'larvie', '2025-04-28', 19.00, '680ea06b551ff_INSIDE_BROCHURE02.jpg', 'asdsadsa', '09471918324', 'asdasdsadsadada', NULL, 'alice_jones', NULL, 'aaaaaaaaaa', 12121212, '', NULL, 'Cashier', 'asdasdsad', NULL),
(390, 'aaaaaaaaaaa', 'baba', '2025-04-28', 69.00, '680eb7cef17dd_lakers_3576633226252579652.jpg', 'asdasdasda', '9471918323', 'hey you', '2025-04-28', 'alice_jones', NULL, 'Sir Jun', 12321321, '', NULL, 'Cashier', 'baba', NULL),
(391, 'MSME', 'glaiza incorporated', '2025-05-05', 145.00, '68185a8022fd8_GROUP-1_-CAPSTONE_Manuscript2.pdf', 'glaiza', '12321231', 'good project', '2025-05-05', 'alice_jones', NULL, 'Sir jun', 123123213, '', NULL, 'cashier', 'asdsdsads', NULL),
(392, 'STUDENT', '1231321', '2025-05-05', 145.00, '6818664e48066_Picture1.jpg', 'asdsadasdsa', '1232132133213', '123213', '2025-05-05', 'alice_jones', NULL, 'asdsadsa', 1212321321, '', NULL, 'cashier', 'asdsdsads', NULL),
(393, 'Laurel', 'polly', '2025-05-13', 549.99, '6822fc49b121e_IMG_20250415_160343.jpg', 'polly', '9471918323', 'polly', '2025-05-13', 'alice_jones', '2025-05-13', 'Sir Jun', 2147483647, 'asdasdasd', '2025-05-13', '0', 'polly', '2025-05-13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=394;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

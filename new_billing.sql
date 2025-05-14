-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 08:44 AM
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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

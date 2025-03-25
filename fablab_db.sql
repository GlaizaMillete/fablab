-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2025 at 06:52 AM
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
-- Database: `fablab_db`
--

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
(3, 'alice_jones', 'mypassword789', 'Active'),
(4, 'jadeds', '$2y$10$Ufo.kgLVrFZD1.BA/aUkD.ousOhnAU742A1x3UwX1X9EkQ5m9I/7W', 'Active'),
(5, 'sss', '$2y$10$IwwtjIKCM3cXO9EqmpRtNO12WCMMLFTEqukFMllVkGV6atmaq9YqK', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `stafffablab`
--
ALTER TABLE `stafffablab`
  ADD PRIMARY KEY (`staffID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `stafffablab`
--
ALTER TABLE `stafffablab`
  MODIFY `staffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

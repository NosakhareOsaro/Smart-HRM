-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 15, 2023 at 10:24 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_smart_hrm`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `attendance_status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `date`, `attendance_status`) VALUES
(34, 7, '2023-07-01', 2),
(35, 8, '2023-07-01', 1),
(36, 6, '2023-07-01', 1),
(37, 6, '2023-07-02', 1),
(38, 6, '2023-07-03', 1),
(39, 6, '2023-07-04', 2),
(40, 6, '2023-07-05', 2),
(41, 6, '2023-07-06', 2),
(42, 6, '2023-07-07', 2),
(43, 6, '2023-07-10', 2),
(44, 7, '2023-07-03', 2),
(45, 7, '2023-07-04', 2),
(46, 7, '2023-07-17', 1),
(47, 7, '2023-07-18', 2),
(48, 7, '2023-07-14', 1),
(49, 7, '2023-07-19', 2),
(50, 8, '2023-07-03', 2),
(51, 8, '2023-07-04', 1),
(52, 8, '2023-07-13', 2),
(53, 8, '2023-07-06', 2),
(54, 8, '2023-07-14', 1),
(55, 9, '2023-07-21', 2),
(56, 9, '2023-07-29', 1),
(57, 9, '2023-07-19', 2),
(58, 9, '2023-07-27', 2),
(59, 9, '2023-07-17', 2),
(60, 10, '2023-07-10', 2),
(61, 10, '2023-07-27', 1),
(62, 10, '2023-07-11', 2),
(63, 10, '2023-07-19', 2),
(64, 10, '2023-07-17', 2),
(65, 15, '2023-07-25', 2),
(66, 15, '2023-07-27', 2),
(67, 15, '2023-07-26', 2),
(68, 16, '2023-07-03', 1),
(69, 16, '2023-07-04', 2),
(70, 16, '2023-07-05', 2),
(71, 16, '2023-07-06', 2),
(72, 16, '2023-07-07', 2),
(73, 16, '2023-07-11', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('admin','employee') NOT NULL,
  `employee_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `phone`, `role`, `employee_id`, `created_at`, `updated_at`) VALUES
(6, 'admin', 'test', 'test123@gmail.com', '$2y$10$Hpi2fovoWLpK0SK3QcwdpeCrdjt4DY2H3JH3DEzcSMdCQut00K/mu', '+2347033319996', 'admin', 555555, '2023-07-14 20:41:10', NULL),
(7, 'frankie', 'slack', 'slack@gmail.com', '$2y$10$Fl3pApD/jkJx.ZBAlytos.1biB/j4/WPig06rPlYpaKsXysEOj4rK', '+2347033319996', 'employee', 123345, '2023-07-14 20:57:00', NULL),
(8, 'james', 'IBJ', 'james@gmail.com', '$2y$10$GeCeJEXazVERDQg.R/QMu.A/6Euko0as2cSLLAMp.dq5yzkwa9GB6', '+2347033319990', 'employee', 123345, '2023-07-14 21:31:51', NULL),
(9, 'mariam', 'jenny', 'mariam@gmail.com', '$2y$10$NMl872SVW.VZCgN7qVTGQeclGFqN2DnjtjBMM5j44PiYYZQ.FJeIS', '+2347033319990', 'admin', 1111111, '2023-07-14 21:44:37', NULL),
(10, 'favour', 'ehid', 'favour@gmail.com', '$2y$10$9y6TDLXHYEEarsQaS2rt0OqSrxzFOxtAYM3n.2TySSg41a8nnPt4G', '+2347133319995', 'employee', 111111134, '2023-07-14 21:47:08', NULL),
(11, 'lyod', 'framma', 'lyod@gmail.com', '$2y$10$R0PJXUi9fjIwX38fJJqcy.gRFMsYokV.tcRbGfnhLTu2Kyxg/nnZ2', '07033319995', 'employee', 1111222, '2023-07-14 22:16:59', NULL),
(12, 'ekenne', 'malam', 'ekenee@gmail.com', '$2y$10$5gOmqK6PQUCdF./Rqj90hO5ijLouHOah4dlamM2s/BamJGVYgabm.', '+2347033319990', 'employee', 222222, '2023-07-14 22:42:44', NULL),
(13, 'Jagz', 'Jessy', 'Jazgs@gmail.com', '$2y$10$wICvrvpfjGzeBcJrvRR7iOLEV6rdo/XE0gcQnuQe6ctjEecXyGQKW', '+2347033319990', 'employee', 12345, '2023-07-14 22:54:48', NULL),
(14, 'okon', 'marvel', 'okon@gmail.com', '$2y$10$Py40XKZC4QtbLZ/dOnLs3evDknjg5mttI2TvyPZQs5uUQKuhDvqbm', '+2347033319998', 'employee', 555555, '2023-07-15 07:55:54', NULL),
(15, 'simon', 'daniel', 'simon@gmail.com', '$2y$10$SQfZ5RJbA9g.cx9RgiOgZ.To6HjqYOkVbeSXyoA3yIzWEAZQ/dnqe', '+2347033319991', 'employee', 1111111, '2023-07-15 08:05:57', NULL),
(16, 'michael', 'smith', 'micheal@gmail.com', '$2y$10$8dt/3gg6pRrxoaNdQJ8WqO./PrNAvYr/La.PsXuk9ckDvt3GSDlha', '+2347033319990', 'employee', 44447, '2023-07-15 09:01:49', NULL),
(17, 'iweaat', 'Osaro', 'osaronosakhrrt@gmail.com', '$2y$10$lyvWC/JrMwDPjNTxq/otjumpsPztnzuPK6BUHBNOxlv9INIfxKSUW', '+2347033319993', 'employee', 1111222, '2023-07-15 09:11:24', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

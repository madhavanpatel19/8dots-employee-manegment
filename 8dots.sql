-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2025 at 08:37 AM
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
-- Database: `8dots`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_us`
--

CREATE TABLE `about_us` (
  `about_id` int(10) NOT NULL,
  `about_heading` text NOT NULL,
  `about_short_desc` text NOT NULL,
  `about_desc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `about_us`
--

INSERT INTO `about_us` (`about_id`, `about_heading`, `about_short_desc`, `about_desc`) VALUES
(1, 'About Us - Our Story', '\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters,\r\n', 'Rhone was the collective vision of a small group of weekday warriors. For years, we were frustrated by the lack of activewear designed for men and wanted something better. With that in mind, we set out to design premium apparel that is made for motion and engineered to endure.\r\n\r\nAdvanced materials and state of the art technology are combined with heritage craftsmanship to create a new standard in activewear. Every product tells a story of premium performance, reminding its wearer to push themselves physically without having to sacrifice comfort and style.\r\n\r\nBeyond our product offering, Rhone is founded on principles of progress and integrity. Just as we aim to become better as a company, we invite men everywhere to raise the bar and join us as we move Forever Forward.');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(10) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_pass` varchar(255) NOT NULL,
  `admin_image` text NOT NULL,
  `admin_contact` varchar(255) NOT NULL,
  `admin_country` text NOT NULL,
  `admin_job` varchar(255) NOT NULL,
  `admin_about` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_name`, `admin_email`, `admin_pass`, `admin_image`, `admin_contact`, `admin_country`, `admin_job`, `admin_about`) VALUES
(1, 'admin', 'admin@gmail.com', '123', 'the-batman-2022-robert-pattinson-bruce-wayne-batman-logo-dark-hd-wallpaper-preview.jpg', '987654321', 'india', 'CEO', ' hello '),
(2, 'Test Name', 'admin@ave.com', '123', 'admin.jpg', '077885221', 'Morocco', 'Front-End Developer', ' Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical '),
(3, 'Madhavan', 'madhavan@gmail.com', '123', 'madhavan.jpg', '987654321', 'india', 'Front-End Developer', ' hello');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `status` enum('present','absent','leave') DEFAULT 'present',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `emp_id`, `attendance_date`, `check_in_time`, `status`, `remarks`, `created_at`) VALUES
(3, 7, '2025-11-01', NULL, 'absent', '', '2025-11-24 07:10:32'),
(6, 8, '2025-01-01', NULL, 'present', '', '2025-11-24 07:35:08'),
(9, 8, '2025-11-24', NULL, 'leave', '', '2025-11-24 09:14:27'),
(10, 7, '2025-11-24', NULL, 'leave', '', '2025-11-24 09:14:27'),
(13, 8, '2025-11-23', NULL, 'present', '', '2025-11-24 09:15:16'),
(14, 7, '2025-11-23', NULL, 'absent', '', '2025-11-24 09:15:16'),
(25, 8, '2025-11-17', NULL, 'leave', '', '2025-11-24 10:41:36'),
(26, 7, '2025-11-17', NULL, 'absent', '', '2025-11-24 10:41:36'),
(27, 9, '2025-11-24', NULL, 'present', '', '2025-11-24 11:46:59'),
(36, 8, '2025-11-25', '10:12:00', 'present', '', '2025-11-25 12:10:50'),
(37, 11, '2025-11-25', '10:11:00', 'present', '', '2025-11-25 12:10:50'),
(38, 7, '2025-11-25', '11:10:00', 'present', 'Late check-in (after 10:15 AM)', '2025-11-25 12:10:50'),
(39, 10, '2025-11-25', '10:10:00', 'leave', 'personal leave', '2025-11-25 12:10:50'),
(40, 9, '2025-11-25', '10:10:00', 'absent', '', '2025-11-25 12:10:50'),
(41, 8, '2025-11-26', '10:10:00', 'present', '', '2025-11-26 05:40:58'),
(42, 11, '2025-11-26', '10:10:00', 'present', '', '2025-11-26 05:40:58'),
(43, 7, '2025-11-26', '10:18:00', 'present', 'hospital emergence | Late check-in (after 10:15 AM)', '2025-11-26 05:40:58'),
(44, 10, '2025-11-26', '10:10:00', 'present', 'hospital emergence', '2025-11-26 05:40:58'),
(45, 9, '2025-11-26', '10:11:00', 'present', '', '2025-11-26 05:40:58'),
(46, 8, '2025-11-27', '10:20:00', 'present', 'Late check-in (after 10:15 AM)', '2025-11-27 05:28:29'),
(47, 11, '2025-11-27', '10:10:00', 'present', '', '2025-11-27 05:28:29'),
(48, 7, '2025-11-27', '10:18:00', 'present', 'Late check-in (after 10:15 AM)', '2025-11-27 05:28:29'),
(49, 10, '2025-11-27', '10:10:00', 'present', '', '2025-11-27 05:28:29'),
(50, 9, '2025-11-27', '10:10:00', 'present', '', '2025-11-27 05:28:29'),
(51, 8, '2025-11-28', '10:10:00', 'present', '', '2025-11-28 05:06:41'),
(52, 11, '2025-11-28', '10:09:00', 'present', '', '2025-11-28 05:06:41'),
(53, 7, '2025-11-28', '10:30:00', 'present', 'Late check-in (after 10:15 AM)', '2025-11-28 05:06:41'),
(54, 10, '2025-11-28', '10:20:00', 'present', 'Late check-in (after 10:15 AM)', '2025-11-28 05:06:42'),
(55, 9, '2025-11-28', NULL, 'leave', 'hospital emergence', '2025-11-28 05:06:42'),
(56, 12, '2025-11-28', '10:19:00', 'present', 'Late check-in (after 10:15 AM)', '2025-11-28 11:13:18'),
(57, 12, '2025-11-27', '10:19:00', 'present', 'Late check-in (after 10:15 AM)', '2025-11-28 11:13:28'),
(58, 12, '2025-11-26', '10:16:00', 'present', 'Late check-in (after 10:15 AM)', '2025-11-28 11:13:39'),
(59, 12, '2025-11-25', '10:20:00', 'present', 'Late check-in (after 10:15 AM)', '2025-11-28 11:15:02'),
(60, 8, '2025-12-01', '10:10:00', 'present', '', '2025-12-01 06:21:56'),
(61, 11, '2025-12-01', '10:12:00', 'present', '1', '2025-12-01 06:21:56'),
(62, 7, '2025-12-01', '01:11:00', 'present', 'Late check-in (after 10:15 AM)', '2025-12-01 06:21:56'),
(63, 10, '2025-12-01', NULL, 'leave', 'personal leave', '2025-12-01 06:21:56'),
(64, 9, '2025-12-01', '10:12:00', 'present', '', '2025-12-01 06:21:56'),
(65, 12, '2025-12-01', '11:01:00', 'present', 'Late check-in (after 10:15 AM)', '2025-12-01 06:21:56'),
(66, 8, '2025-12-02', '10:10:00', 'present', '', '2025-12-02 10:56:56'),
(67, 11, '2025-12-02', '10:17:00', 'present', 'Late check-in (after 10:15 AM)', '2025-12-02 10:56:56'),
(68, 7, '2025-12-02', '10:19:00', 'present', 'Late check-in (after 10:15 AM)', '2025-12-02 10:56:56'),
(69, 10, '2025-12-02', '10:20:00', 'present', 'Late check-in (after 10:15 AM)', '2025-12-02 10:56:56'),
(70, 9, '2025-12-02', '10:19:00', 'present', 'Late check-in (after 10:15 AM)', '2025-12-02 10:56:56'),
(71, 12, '2025-12-02', '10:01:00', 'present', '', '2025-12-02 10:56:56'),
(72, 8, '2025-12-03', '10:10:00', 'present', '', '2025-12-03 06:10:05'),
(73, 11, '2025-12-03', '10:16:00', 'present', 'Late check-in (after 10:15 AM)', '2025-12-03 06:10:05'),
(74, 7, '2025-12-03', '10:19:00', 'present', 'Late check-in (after 10:15 AM)', '2025-12-03 06:10:05'),
(75, 10, '2025-12-03', '09:59:00', 'present', '', '2025-12-03 06:10:05'),
(76, 9, '2025-12-03', NULL, 'leave', 'personal leave', '2025-12-03 06:10:05'),
(77, 12, '2025-12-03', NULL, 'absent', '', '2025-12-03 06:10:05');

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_documents`
--

INSERT INTO `employee_documents` (`id`, `emp_id`, `file_name`, `uploaded_at`) VALUES
(12, 11, '1764746467_images (2).jpg', '2025-12-03 07:21:07');

-- --------------------------------------------------------

--
-- Table structure for table `emp_list`
--

CREATE TABLE `emp_list` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone_number` int(11) NOT NULL,
  `address` varchar(100) NOT NULL,
  `email` varchar(21) NOT NULL,
  `blood_group` varchar(11) NOT NULL,
  `gender` varchar(11) NOT NULL,
  `join_date` date NOT NULL,
  `salary` int(11) NOT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `hra` decimal(10,2) DEFAULT NULL,
  `allowance` decimal(10,2) DEFAULT NULL,
  `deductions` decimal(10,2) DEFAULT NULL,
  `documents` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_list`
--

INSERT INTO `emp_list` (`id`, `name`, `phone_number`, `address`, `email`, `blood_group`, `gender`, `join_date`, `salary`, `basic_salary`, `hra`, `allowance`, `deductions`, `documents`) VALUES
(7, 'raj', 987654321, 'botad', 'rajveer@gmail.com', 'A+', 'Male', '2027-12-31', 10012, 10000.00, 12.00, 0.00, 0.00, NULL),
(8, 'madhavan', 987654321, '503, Drive In Rd', 'madhi@gmail.com', 'B+', 'Male', '2025-11-07', 15000, 15000.00, 0.00, 0.00, 0.00, NULL),
(9, 'snehal', 1234566779, '415, kook', 'kpkpkp@yopmail.com', 'O+', 'Male', '0000-00-00', 87000, 80000.00, 7000.00, 0.00, 0.00, NULL),
(10, 'rajveer ', 987654321, '503, Drive In Rd', 'rajveer@gmail.com', 'B-', 'Male', '2028-01-22', 15000, 15000.00, 0.00, 0.00, 0.00, NULL),
(11, 'meet', 2147483647, '503, Drive In Rd', 'madhi@gmail.com', 'A+', 'Male', '2025-02-25', 15000, 15000.00, 0.00, 0.00, 0.00, NULL),
(12, 'verr', 2147483647, '503, Drive In Rd', 'veer@gmail.com', 'A+', 'Male', '2017-12-31', 15000, 15000.00, 0.00, 0.00, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `emp_performance`
--

CREATE TABLE `emp_performance` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `perf_year` int(11) NOT NULL,
  `perf_month` int(11) NOT NULL,
  `absent` tinyint(3) UNSIGNED DEFAULT 0,
  `late` tinyint(3) UNSIGNED DEFAULT 0,
  `task_sheet` tinyint(3) UNSIGNED DEFAULT 0,
  `performance_score` tinyint(3) UNSIGNED DEFAULT 0,
  `dressing_behaviour` tinyint(3) UNSIGNED DEFAULT 0,
  `rnd` tinyint(3) UNSIGNED DEFAULT 0,
  `total` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_performance`
--

INSERT INTO `emp_performance` (`id`, `emp_id`, `perf_year`, `perf_month`, `absent`, `late`, `task_sheet`, `performance_score`, `dressing_behaviour`, `rnd`, `total`, `updated_at`) VALUES
(1, 7, 2025, 5, 15, 10, 2, 35, 9, 1, 72, '2025-11-28 06:55:41'),
(2, 8, 2025, 5, 1, 1, 1, 2, 1, 1, 7, '2025-11-28 06:56:36'),
(5, 7, 2025, 11, 10, 5, 1, 12, 9, 12, 49, '2025-11-28 10:50:50'),
(7, 8, 2025, 11, 20, 10, 9, 1, 10, 15, 65, '2025-11-28 07:26:04'),
(8, 9, 2025, 11, 10, 0, 0, 0, 0, 0, 10, '2025-11-28 07:07:05'),
(9, 10, 2025, 11, 10, 10, 10, 35, 10, 3, 78, '2025-12-01 09:46:23'),
(10, 11, 2025, 11, 10, 10, 0, 35, 10, 13, 78, '2025-11-28 07:20:21'),
(24, 12, 2025, 11, 0, 0, 9, 23, 9, 12, 53, '2025-11-28 11:12:30'),
(26, 7, 2025, 12, 10, 10, 8, 25, 9, 13, 75, '2025-12-01 06:46:36'),
(27, 8, 2025, 12, 10, 10, 9, 19, 9, 12, 69, '2025-12-01 06:46:54'),
(28, 9, 2025, 12, 10, 10, 10, 32, 9, 13, 84, '2025-12-01 06:47:13'),
(29, 10, 2025, 12, 10, 10, 3, 3, 3, 3, 32, '2025-12-01 06:47:23'),
(30, 11, 2025, 12, 10, 10, 1, 1, 1, 1, 24, '2025-12-01 06:47:40'),
(31, 12, 2025, 12, 10, 10, 9, 23, 9, 14, 75, '2025-12-01 06:48:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emp_date` (`emp_id`,`attendance_date`);

--
-- Indexes for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `emp_list`
--
ALTER TABLE `emp_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emp_performance`
--
ALTER TABLE `emp_performance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emp_month` (`emp_id`,`perf_year`,`perf_month`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `emp_list`
--
ALTER TABLE `emp_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `emp_performance`
--
ALTER TABLE `emp_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `employee_documents_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emp_performance`
--
ALTER TABLE `emp_performance`
  ADD CONSTRAINT `emp_performance_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

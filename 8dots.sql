-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2026 at 01:26 PM
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
(1, 'About Us - Our Story', '\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters,\r\n', 'Rhone was the collective vision of a small group of weekday warriors. For years, we were frustrated by the lack of activewear designed for men and wanted something better. With that in mind, we set out to design premium apparel that is made for motion and engineered to endure.\r\n\r\nAdvanced materials and state of the art technology are combined with heritage craftsmanship to create a new standard in activewear. Every product tells a story of premium performance, reminding its wearer to push themselves physically without having to sacrifice comfort and style.\r\n\r\nBeyond our product offering, Rhone is founded on principles of progress and integrity. Just as we aim to become better as a company, we invite men everywhere to raise the bar and join us as we move Forever Forward.'),
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
  `admin_about` text NOT NULL,
  `is_super_admin` tinyint(1) NOT NULL DEFAULT 0,
  `permissions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_name`, `admin_email`, `admin_pass`, `admin_image`, `admin_contact`, `admin_country`, `admin_job`, `admin_about`, `is_super_admin`, `permissions`) VALUES
(1, 'admin', 'admin@gmail.com', '123', 'the-batman-2022-robert-pattinson-bruce-wayne-batman-logo-dark-hd-wallpaper-preview.jpg', '987654321', 'india', 'CEO', ' hello ', 1, NULL),
(2, 'Test Name', 'admin@ave.com', '123', 'admin.jpg', '077885221', 'Morocco', 'Front-End Developer', ' Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical ', 0, 'employee_insert,employee_update,employee_delete,employee_view');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `publish_date` datetime DEFAULT current_timestamp(),
  `end_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `publish_date`, `end_date`, `is_active`, `created_at`) VALUES
(23, 'jh', 'gsehtf', '2026-06-16 15:35:00', NULL, 0, '2026-06-16 10:02:47'),
(24, 'jalsa karo', 'moje moj', '2026-06-16 15:53:00', '2026-06-16 16:02:00', 0, '2026-06-16 10:21:57'),
(25, 'use slack', 'bvgyfu', '2026-06-16 16:20:00', NULL, 0, '2026-06-16 10:51:17'),
(26, 'use slack', 'cd', '2026-06-17 09:39:44', NULL, 0, '2026-06-17 04:09:44'),
(27, 'ee', 'rvfva', '2026-06-17 09:39:49', NULL, 0, '2026-06-17 04:09:49'),
(28, 'vvae', 'sdv', '2026-06-17 09:39:55', NULL, 0, '2026-06-17 04:09:55');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_read`
--

CREATE TABLE `announcement_read` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement_read`
--

INSERT INTO `announcement_read` (`id`, `announcement_id`, `emp_id`) VALUES
(3, 12, 14);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `status` enum('present','absent','leave') DEFAULT 'present',
  `remarks` varchar(255) DEFAULT NULL,
  `work_photos` text DEFAULT NULL,
  `performance` int(11) DEFAULT NULL,
  `total_duration_secs` int(11) DEFAULT 0,
  `last_resume_time` datetime DEFAULT NULL,
  `is_working` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `emp_id`, `attendance_date`, `check_in_time`, `check_out_time`, `status`, `remarks`, `work_photos`, `performance`, `total_duration_secs`, `last_resume_time`, `is_working`, `created_at`) VALUES
(102, 14, '2026-04-22', '10:58:00', NULL, 'present', 'dce ververvr', NULL, NULL, 17001, '2026-04-22 15:42:48', 1, '2026-04-22 05:28:13'),
(103, 11, '2026-04-22', '15:45:41', NULL, 'present', NULL, NULL, NULL, 4893, '2026-04-22 17:09:10', 0, '2026-04-22 10:15:41'),
(104, 14, '2026-04-27', '10:19:00', '12:55:00', 'present', 'demo', NULL, NULL, 8637, '2026-04-27 12:49:53', 0, '2026-04-27 04:49:39'),
(105, 14, '2026-05-15', NULL, NULL, 'leave', 'Leave: demo', NULL, NULL, 0, NULL, 0, '2026-05-14 05:12:55'),
(106, 14, '2026-05-16', NULL, NULL, 'leave', 'Leave: demo', NULL, NULL, 0, NULL, 0, '2026-05-14 05:12:55'),
(107, 14, '2026-05-17', NULL, NULL, 'leave', 'Leave: demo', NULL, NULL, 0, NULL, 0, '2026-05-14 05:12:55'),
(108, 14, '2026-05-18', NULL, NULL, 'leave', 'Leave: demo', NULL, NULL, 0, NULL, 0, '2026-05-14 05:12:55'),
(109, 14, '2026-05-19', NULL, NULL, 'leave', 'Leave: demo', NULL, NULL, 0, NULL, 0, '2026-05-14 05:12:55'),
(110, 14, '2026-05-29', '15:08:00', '18:41:00', 'present', 'demo', '', NULL, 10486, '2026-05-29 16:31:29', 0, '2026-05-29 09:38:32'),
(111, 14, '2026-06-01', NULL, NULL, 'leave', 'Leave: demo', NULL, NULL, 0, NULL, 0, '2026-05-29 09:50:31'),
(112, 14, '2026-06-02', '12:54:00', '15:10:00', 'present', 'demo', '', NULL, 8203, '2026-06-02 12:54:31', 0, '2026-06-02 07:24:31'),
(113, 14, '2026-06-04', '09:46:30', NULL, 'present', NULL, NULL, NULL, 2181, '2026-06-04 09:46:30', 0, '2026-06-04 04:16:30'),
(114, 14, '2026-06-19', '10:20:00', '10:22:00', 'present', '3e', '[\"work_photos\\/14_2026-06-19_1781844752_0.png\",\"work_photos\\/14_2026-06-19_1781844752_1.png\",\"work_photos\\/14_2026-06-19_1781844752_2.png\"]', NULL, 135, '2026-06-19 10:20:48', 0, '2026-06-19 04:50:48'),
(115, 14, '2026-06-22', '11:05:00', '11:05:00', 'present', 'dew', '', NULL, 19, '2026-06-22 11:05:19', 0, '2026-06-22 05:35:19');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `cat_title` text NOT NULL,
  `cat_top` text NOT NULL,
  `cat_image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `industry` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `image`, `name`, `mobile`, `email`, `country`, `company_name`, `website`, `created_at`, `status`, `industry`) VALUES
(12, '1781675352_4210.jpg', 'Patel Madhavan', '987654321', 'madhavanpatel19@gmail.com', 'India', '', '', '2026-04-21 07:41:00', 'Active', NULL),
(13, '1781675265_5859.jpeg', 'rajveer', '2304549592', 'madhavanpatel19@gmail.com', 'India', '8dots', 'https://8dots.in', '2026-05-04 07:34:34', 'Active', NULL),
(18, '1781674467_8193.jpeg', 'Patel Madhavan', '1234567890', 'madhavanpatel19@gmail.com', 'uk', '8DOTS', 'https://8dots.in', '2026-06-17 05:34:27', 'Active', NULL),
(20, '1781675373_6517.jpeg', 'dayro', '1234567890', 'madhavanpatel19@gmail.com', 'India', '8dots', 'https://8dots.in', '2026-06-17 05:49:33', 'Active', ''),
(21, '1781679124_8962.jpeg', 'dayro112', '1234567890', 'madhavanpatel19@gmail.com', 'India', '8dots', 'https://8dots.in', '2026-06-17 06:52:04', 'Active', '8dots, cedlete123');

-- --------------------------------------------------------

--
-- Table structure for table `client_industries`
--

CREATE TABLE `client_industries` (
  `id` int(11) NOT NULL,
  `industry_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_industries`
--

INSERT INTO `client_industries` (`id`, `industry_name`, `created_at`) VALUES
(2, '8dots', '2026-06-17 06:54:41'),
(4, 'cedlete123', '2026-06-17 07:02:01');

-- --------------------------------------------------------

--
-- Table structure for table `client_projects`
--

CREATE TABLE `client_projects` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `project_date` date DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT NULL,
  `currency` varchar(20) DEFAULT 'INR',
  `status` varchar(50) DEFAULT 'Active',
  `source` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deadline` date DEFAULT NULL,
  `project_desc` text DEFAULT NULL,
  `project_image` varchar(255) DEFAULT NULL,
  `assigned_employees` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_projects`
--

INSERT INTO `client_projects` (`id`, `client_id`, `project_name`, `project_date`, `budget`, `currency`, `status`, `source`, `created_at`, `deadline`, `project_desc`, `project_image`, `assigned_employees`) VALUES
(20, 13, 'crroco123', '2026-05-14', 12000.00, 'GBP', 'Active', NULL, '2026-05-04 07:35:16', NULL, NULL, NULL, NULL),
(22, 13, 'demo', '2026-06-10', 12121212.00, 'INR', 'Pending', NULL, '2026-06-10 07:19:28', '2026-06-11', '123', '', '20'),
(23, 12, 'crroco123', '2026-06-05', 1203000.00, 'INR', 'Pending', NULL, '2026-06-10 09:39:49', '2026-06-12', 'der', '', '11,20,14,19'),
(24, 12, 'harikrushana agro chemicals', '2026-06-01', 4000000.00, 'INR', 'Pending', NULL, '2026-06-11 05:27:00', '2026-06-30', 'harikurshna agro cemicals web site', '', '11,20,14,19'),
(25, 13, 'helmet lock', '2026-12-31', 0.00, 'INR', 'Active', '', '2026-06-11 05:34:03', '2026-12-31', 'dqf', '', '11,20,14'),
(26, 12, 'test 1', '2026-12-31', 50000.00, 'INR', 'Active', '', '2026-06-11 10:40:01', '2026-12-31', 'ffimf', '1781759380_9500.png', '14,19'),
(27, 13, 'packaging Box', '2026-06-12', 10000.00, 'USD', 'Active', NULL, '2026-06-11 12:03:06', '2026-06-17', 'fqadaacaa', '1781179386_6571.jpg', '11,20,14,19'),
(28, 13, 'dmeo', '2026-12-31', 99999.00, 'INR', 'Active', NULL, '2026-06-11 12:26:49', '2026-12-31', 'fejfeo', '1781180809_3626.png', '20,14,19'),
(29, 13, '23sdkfhkshd', '2026-12-31', 24590024.00, 'INR', 'Active', NULL, '2026-06-11 12:38:03', '2026-12-31', '\r\nvfv', '1781181483_3088.jfif', '11,20,14,19'),
(31, 13, 'crroco123', '2026-12-31', 603.00, 'INR', 'Active', NULL, '2026-06-11 13:01:55', '2026-12-31', 'ewev', '', '11,20,14,19'),
(32, 13, 'crroco123567', '2026-12-31', 12300012.00, 'INR', 'Pending', 'ahmedabad', '2026-06-11 14:03:11', '2026-12-31', 'fjefweh', '1781759363_5622.png', '11,20,19'),
(33, 13, 'crroco123', '2026-12-31', 0.00, 'INR', 'Completed', 'ahmedabad, BNI, botad, Civil, Electrical', '2026-06-12 04:49:25', '2026-12-31', 'fdfr', '', '11,20,14,19'),
(34, 13, 'Demo Project', '2026-06-13', 5000.00, 'USD', 'Pending', '8dots, CD Innov', '2026-06-12 11:56:20', '2026-07-02', 'MAIN project', '1781265380_6704.png', '11,20,14,19'),
(37, 21, 'crroco', '2026-06-19', 343.00, 'INR', 'Active', '8dots', '2026-06-18 05:10:59', '2026-06-19', '4r434ger', '1781759459_1486.jpg', '20');

-- --------------------------------------------------------

--
-- Table structure for table `client_project_remarks`
--

CREATE TABLE `client_project_remarks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_project_remarks`
--

INSERT INTO `client_project_remarks` (`id`, `project_id`, `remark`, `created_at`) VALUES
(70, 20, 'demo ', '2026-05-04 07:35:16'),
(71, 20, 'System: Project details updated (Name: crroco123, Budget: 12000, Status: Active)', '2026-05-28 09:45:26'),
(72, 20, 'System: Project status updated to Completed', '2026-05-29 09:38:08'),
(75, 20, 'System: Project status updated to Active', '2026-06-01 07:19:35'),
(76, 20, 'System: Project status updated to Completed', '2026-06-02 04:45:43'),
(77, 20, 'System: Project status updated to Active', '2026-06-02 04:45:44'),
(78, 20, 'System: Project status updated to Pending', '2026-06-02 04:45:46'),
(80, 20, 'System: Project status updated to Active', '2026-06-02 09:47:29'),
(83, 24, 'System: Project status updated to Pending', '2026-06-11 05:32:31'),
(84, 25, 'System: Project status updated to Pending', '2026-06-11 05:56:56'),
(85, 25, 'System: Project status updated to Active', '2026-06-11 07:12:12'),
(86, 33, 'System: Project status updated to Pending', '2026-06-12 04:50:14'),
(87, 33, 'System: Project status updated to Completed', '2026-06-12 04:50:25'),
(88, 32, 'System: Project status updated to Pending', '2026-06-12 11:54:13'),
(89, 26, 'System: Project status updated to Completed', '2026-06-17 04:44:55'),
(90, 26, 'System: Project status updated to Active', '2026-06-17 04:46:09'),
(91, 25, 'System: Project status updated to Pending', '2026-06-22 06:26:46'),
(92, 25, 'System: Project status updated to Active', '2026-06-22 06:26:47');

-- --------------------------------------------------------

--
-- Table structure for table `company_links`
--

CREATE TABLE `company_links` (
  `id` int(11) NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `link_url` text NOT NULL,
  `category` varchar(255) DEFAULT 'General',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_pinned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_links`
--

INSERT INTO `company_links` (`id`, `link_name`, `link_url`, `category`, `created_at`, `is_pinned`) VALUES
(7, 'figma', 'https://web.whatsapp.com/', 'General', '2026-05-14 10:12:38', 0),
(8, 'figma', 'https://web.whatsapp.com/', '123', '2026-05-14 10:12:57', 0),
(14, 'Test Link', 'http://localhost/8DOTS/admin_area/index.php', 'abc', '2026-06-15 10:29:37', 0),
(18, 'vkfvnk', 'https://chatgpt.com/c/6a2fd810-ce98-83ee-ae23-c8c72a491147', 'Section', '2026-06-15 11:08:08', 0),
(21, 'efjkw', 'uploads/company_links/1781523948_Tax Invoice – crroco123.pdf', 'qw', '2026-06-15 11:45:48', 0),
(22, 'cd', 'uploads/company_links/1781523957_Vinay Prabhu Invoice_DMC 78.pdf', 'qw', '2026-06-15 11:45:58', 0),
(23, '124', 'file:///C:/Users/Madhavan/AppData/Local/Packages/5319275A.WhatsAppDesktop_cv1g1gvanyjgm/LocalState/sessions/262DC2B5AD011BAB12793922FAC5FE9B99DE30E1/transfers/2026-24/Tax%20Invoice%20%E2%80%93%20crroco123.pdf', '123', '2026-06-15 12:00:27', 1),
(24, 'demo 22412313', 'uploads/company_links/1781588291_WhatsApp Image 2026-06-14 at 2.39.45 PM.jpeg', '123', '2026-06-16 05:38:11', 0),
(25, 'cd', 'uploads/company_links/1781591184_WhatsApp Image 2026-06-14 at 2.39.45 PM.jpeg', '123', '2026-06-16 06:26:24', 0),
(26, 'ddd', 'uploads/company_links/1781591191_WhatsApp Image 2026-06-14 at 2.39.45 PM.jpeg', '123', '2026-06-16 06:26:31', 0),
(27, '124', 'uploads/company_links/1781591201_WhatsApp Image 2026-06-14 at 2.39.45 PM.jpeg', '123', '2026-06-16 06:26:41', 0),
(28, 'wqcwe', 'uploads/company_links/1781591217_WhatsApp Image 2026-06-14 at 2.39.45 PM.jpeg', '123', '2026-06-16 06:26:57', 0);

-- --------------------------------------------------------

--
-- Table structure for table `customer_feedback`
--

CREATE TABLE `customer_feedback` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `service_month` varchar(100) DEFAULT NULL,
  `service_quality` varchar(100) DEFAULT NULL,
  `service_on_time` varchar(10) DEFAULT NULL,
  `professionalism` varchar(100) DEFAULT NULL,
  `overall_satisfaction` varchar(100) DEFAULT NULL,
  `liked` text DEFAULT NULL,
  `improvement` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `recommend` varchar(10) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_feedback`
--

INSERT INTO `customer_feedback` (`id`, `customer_name`, `contact_number`, `email`, `service_month`, `service_quality`, `service_on_time`, `professionalism`, `overall_satisfaction`, `liked`, `improvement`, `comments`, `rating`, `recommend`, `is_read`, `created_at`) VALUES
(1, 'Patel Madhavan', 'dfsv', 'madhavanpatel19@gmail.com', '2026-03', 'Excellent', 'Yes', 'Very Satisfied', 'Very Satisfied', 'fh fy', 'jhm', 'mu7', 4, 'Yes', 1, '2026-04-09 05:42:17'),
(2, 'Patel Madhavan', '0987654321', 'madhavanpatel19@gmail.com', '2026-03', 'Good', 'Yes', 'Satisfied', 'Very Satisfied', 'hu', 'ny5', 'menrl', 2, 'Yes', 1, '2026-04-09 05:45:45');

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
(14, 19, '1776404391_extra_3890.pdf', '2026-04-17 05:39:51'),
(15, 11, '1781864082_Banner.jpg', '2026-06-19 10:14:42'),
(16, 14, '1781864510_ChatGPT Image Jun 18, 2026, 01_10_58 PM.png', '2026-06-19 10:21:50');

-- --------------------------------------------------------

--
-- Table structure for table `emp_list`
--

CREATE TABLE `emp_list` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone_number` int(11) NOT NULL,
  `address` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `blood_group` varchar(11) NOT NULL,
  `gender` varchar(11) NOT NULL,
  `join_date` date NOT NULL,
  `salary` int(11) NOT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `hra` decimal(10,2) DEFAULT NULL,
  `allowance` decimal(10,2) DEFAULT NULL,
  `deductions` decimal(10,2) DEFAULT NULL,
  `documents` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `work_experience` varchar(255) DEFAULT NULL,
  `marital_status` varchar(50) DEFAULT NULL,
  `num_dependents` int(11) DEFAULT 0,
  `emergency_name` varchar(100) DEFAULT NULL,
  `emergency_relationship` varchar(100) DEFAULT NULL,
  `emergency_address` text DEFAULT NULL,
  `emergency_phone` varchar(15) DEFAULT NULL,
  `education_json` text DEFAULT NULL,
  `employment_json` text DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `bank_branch` varchar(255) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `account_type_ifsc` varchar(100) DEFAULT NULL,
  `employee_image` varchar(255) DEFAULT NULL,
  `offer_latter` varchar(255) DEFAULT NULL,
  `NDA` varchar(255) DEFAULT NULL,
  `Aadhar_card` varchar(255) DEFAULT NULL,
  `Pan_card` varchar(255) DEFAULT NULL,
  `Passportsize_photo` varchar(255) DEFAULT NULL,
  `old_company_slary_slip` varchar(255) DEFAULT NULL,
  `otp` varchar(10) DEFAULT NULL,
  `otp_expire` datetime DEFAULT NULL,
  `last_birthday_wish_year` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT 'Not Assigned',
  `designation` varchar(100) DEFAULT 'Not Assigned',
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_list`
--

INSERT INTO `emp_list` (`id`, `name`, `phone_number`, `address`, `email`, `password`, `blood_group`, `gender`, `join_date`, `salary`, `basic_salary`, `hra`, `allowance`, `deductions`, `documents`, `age`, `dob`, `work_experience`, `marital_status`, `num_dependents`, `emergency_name`, `emergency_relationship`, `emergency_address`, `emergency_phone`, `education_json`, `employment_json`, `account_name`, `bank_branch`, `account_number`, `account_type_ifsc`, `employee_image`, `offer_latter`, `NDA`, `Aadhar_card`, `Pan_card`, `Passportsize_photo`, `old_company_slary_slip`, `otp`, `otp_expire`, `last_birthday_wish_year`, `department`, `designation`, `status`) VALUES
(11, 'meet', 2147483647, '503, Drive In Rd', 'madhi@gmail.com', '123', 'A+', 'Male', '2025-02-25', 15000, 15000.00, 0.00, 0.00, 0.00, NULL, 0, '2026-06-03', '', '', 0, '', '', '', '', '[]', '[]', '', '', '', '', '1781865363_1509.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Not Assigned', 'Not Assigned', 'Active'),
(14, 'Patel Madhavan123', 2147483647, 'Gota', 'madhavanpatel19@gmail.com', '123', 'AB+', 'Male', '2026-04-15', 12000, 12000.00, 0.00, 0.00, 0.00, NULL, 0, '2026-04-15', '', '', 0, 'Patel Madhavan', 'fataer', 'Gota', '1231232112', '[{\"degree\":\"123\",\"univ\":\"123\",\"year\":\"123\",\"grade\":\"123\",\"city\":\"12312\"},{\"degree\":\"3\",\"univ\":\"2312\",\"year\":\"123\",\"grade\":\"123\",\"city\":\"23\"}]', '[{\"company\":\"2\",\"pos\":\"12\",\"year\":\"12\",\"reason\":\"12\"}]', '123', 'btad', '87654321', 'SBIN2345', '1781865520_8101.png', '', '', '', '', '', '', '480469', '2026-06-04 10:34:21', 2026, 'Not Assigned', 'Not Assigned', 'Active'),
(19, 'rajveer', 2147483647, 'Gota', 'madhavanpatel19@gmail.com', '123', 'A+', 'Male', '2026-04-16', 15000, 15000.00, 0.00, 0.00, 0.00, NULL, 25, '2000-04-18', '', 'Single', 0, '', '', '', '', '[]', '[]', '', '', '', '', '1776404391_2429.jpg', '', '', '', '', '', '', '480469', '2026-06-04 10:34:21', NULL, 'Not Assigned', 'Not Assigned', 'Active'),
(20, 'panth ', 2147483647, 'Gota', 'madhavanpatel19@gmail.com', '123', 'O+', 'Male', '2026-04-16', 10000, 10000.00, 0.00, 0.00, 0.00, NULL, 5, '2020-12-31', '', '', 0, '', '', '', '', '[]', '[]', '', '', '', '', '1776404776_3937.jpeg', '', '', '', '', '', '', '480469', '2026-06-04 10:34:21', NULL, 'Not Assigned', 'Not Assigned', 'Active');

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
(10, 11, 2025, 11, 10, 10, 0, 35, 10, 13, 78, '2025-11-28 07:20:21'),
(30, 11, 2025, 12, 10, 10, 1, 1, 1, 1, 24, '2025-12-01 06:47:40'),
(38, 14, 2026, 6, 10, 10, 1, 0, 2, 12, 35, '2026-06-19 11:23:55');

-- --------------------------------------------------------

--
-- Table structure for table `emp_salary_history`
--

CREATE TABLE `emp_salary_history` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `month` varchar(10) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `hra` decimal(10,2) NOT NULL,
  `pf` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL,
  `allowance` decimal(10,2) NOT NULL,
  `deductions` decimal(10,2) NOT NULL,
  `gross_pay` decimal(10,2) NOT NULL,
  `total_deductions` decimal(10,2) NOT NULL,
  `net_pay` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `experience_letters`
--

CREATE TABLE `experience_letters` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `relieve_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `experience_letters`
--

INSERT INTO `experience_letters` (`id`, `name`, `email`, `number`, `designation`, `join_date`, `relieve_date`, `created_at`) VALUES
(2, 'Madhavan', 'madhavanpatel19@gmail.com', '9876543210', 'hr ', '2026-05-15', '2026-05-19', '2026-05-20 05:26:06'),
(3, 'Patel Madhavan', 'madhavanpatel19@gmail.com', '987654321234342', 'demi', '2026-07-03', '2026-06-16', '2026-06-18 10:50:33');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(11) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `budget` varchar(50) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'INR',
  `remark` text DEFAULT NULL,
  `lead_source` varchar(255) DEFAULT NULL,
  `status` enum('active','future','expired') DEFAULT 'active',
  `followup_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `client_name`, `phone`, `email`, `company_name`, `project_name`, `description`, `budget`, `currency`, `remark`, `lead_source`, `status`, `followup_date`, `created_at`) VALUES
(1, 'madhavan ', '0987654321`', 'madhavanpatel19@gmail.com', '8dots', NULL, 'hfjrfjernvfkn nrkfernk  3rke kk34  k3rnrekfa;vknrkaek k', '123000', 'INR', 'demo', 'Civil', 'expired', '2026-06-16', '2026-04-27 09:49:27'),
(2, 'madhavan ', '+91 9876543210', 'madhavanpatel19@gmail.com', '8dots123', NULL, 'ecommerce ', '121222', 'INR', 'demo', 'BNI', 'future', '2026-04-27', '2026-04-27 09:59:15'),
(6, 'Patel Madhavan', '9198765412', 'madhavanpatel19@gmail.com', '8dots', 'crroco123', 'avc', '123000', 'INR', 'AFD', 'ahmedabad, BNI, botad, Civil', 'active', '2026-06-15', '2026-05-04 05:31:35'),
(7, 'kamal', '7778882276', 'kokok@yopmail.com', '8DOTS', '23sdkfhkshd', 'this is demi', '234', 'INR', 'sdfhsdf', 'BNI', 'expired', '2026-06-15', '2026-05-04 05:52:55');

-- --------------------------------------------------------

--
-- Table structure for table `lead_followups`
--

CREATE TABLE `lead_followups` (
  `id` int(11) NOT NULL,
  `lead_id` int(11) NOT NULL,
  `followup_date` date NOT NULL,
  `followup_method` enum('Phone','Email','WhatsApp','Meeting','Other') DEFAULT 'Phone',
  `followup_type` varchar(100) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lead_followups`
--

INSERT INTO `lead_followups` (`id`, `lead_id`, `followup_date`, `followup_method`, `followup_type`, `remark`, `created_at`) VALUES
(1, 1, '2026-04-27', 'Email', 'Urgent', 'udeaa', '2026-04-27 09:50:30'),
(2, 1, '2026-04-27', 'Phone', 'General Remark', 'wgver', '2026-04-27 09:57:54'),
(5, 6, '2026-05-04', 'Phone', 'After 2 Days', 'acfc', '2026-05-04 05:39:38'),
(6, 6, '2026-05-04', 'WhatsApp', 'Urgent', 'abcd', '2026-05-04 05:42:06'),
(7, 6, '2026-05-04', 'Phone', 'General Remark', 'qwds', '2026-05-04 05:42:18'),
(8, 6, '2026-05-04', 'Meeting', '1 Month Before', '234dc', '2026-05-04 05:44:35'),
(9, 6, '2026-05-04', 'Email', 'Urgent', 'sdav', '2026-05-04 05:48:55'),
(10, 7, '2026-05-04', 'Email', 'General Remark', 'kjhfjkshdfksdfh', '2026-05-04 05:53:40'),
(11, 1, '2026-06-15', 'Phone', 'After 2 Days', 'adawdadadadawd', '2026-06-15 05:49:27'),
(12, 7, '2026-06-15', 'Phone', 'Next Week', 'adadadadada', '2026-06-15 05:49:51'),
(13, 6, '2026-06-15', 'Phone', 'After 2 Days', 'uvho', '2026-06-15 06:31:21'),
(14, 7, '2026-06-15', 'Phone', 'Urgent', 'dfawe', '2026-06-15 06:36:31'),
(15, 6, '2026-06-15', 'Phone', '1 Month Before', 'efvf', '2026-06-15 06:38:25'),
(17, 6, '2026-06-15', 'Phone', 'After 2 Days', 'fmlv', '2026-06-15 06:40:25');

-- --------------------------------------------------------

--
-- Table structure for table `lead_sources`
--

CREATE TABLE `lead_sources` (
  `id` int(11) NOT NULL,
  `source_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lead_sources`
--

INSERT INTO `lead_sources` (`id`, `source_name`, `created_at`) VALUES
(1, 'Mechanical', '2026-04-27 10:18:58'),
(3, 'Turnkey', '2026-04-27 10:18:58'),
(4, 'Electrical', '2026-04-27 10:18:58'),
(5, 'Civil', '2026-04-27 10:18:58'),
(6, 'ahmedabad', '2026-04-27 10:19:16'),
(7, 'botad', '2026-04-27 10:26:56'),
(8, 'iot', '2026-04-27 12:45:08'),
(9, 'family ', '2026-05-04 05:31:01'),
(10, '8dots', '2026-06-12 04:53:57'),
(12, 'smitbhai', '2026-06-12 04:54:34'),
(13, 'madhavan ', '2026-06-12 04:56:29'),
(14, 'sec', '2026-06-12 04:58:59');

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

CREATE TABLE `leave_applications` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `leave_from` date NOT NULL,
  `leave_to` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_applications`
--

INSERT INTO `leave_applications` (`id`, `emp_id`, `leave_type_id`, `leave_from`, `leave_to`, `reason`, `status`, `created_at`) VALUES
(3, 14, 1, '2026-05-15', '2026-05-19', 'demo', 'approved', '2026-05-14 05:11:48'),
(4, 14, 1, '2026-06-01', '2026-06-01', 'demo', 'approved', '2026-05-29 09:49:04');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL,
  `leave_name` varchar(255) NOT NULL,
  `num_of_leave` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `leave_name`, `num_of_leave`, `created_at`) VALUES
(1, 'sick leave', 12, '2026-05-14 05:09:03');

-- --------------------------------------------------------

--
-- Table structure for table `nda_forms`
--

CREATE TABLE `nda_forms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `start_date` date NOT NULL,
  `notice_period` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nda_forms`
--

INSERT INTO `nda_forms` (`id`, `name`, `number`, `email`, `position`, `salary`, `start_date`, `notice_period`, `created_at`) VALUES
(2, 'Patel Madhavan', '9876543212', 'madhavanpatel19@gmail.com', 'web devlpor', NULL, '2026-06-25', NULL, '2026-06-18 11:34:04');

-- --------------------------------------------------------

--
-- Table structure for table `offer_letters`
--

CREATE TABLE `offer_letters` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `number` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `notice_period` varchar(100) DEFAULT NULL,
  `salary` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offer_letters`
--

INSERT INTO `offer_letters` (`id`, `name`, `number`, `email`, `position`, `start_date`, `notice_period`, `salary`) VALUES
(6, 'Patel Madhavan', 987654321, 'madhavanpatel19@gmail.com', 'hr', '2026-04-21', '30days', 10000.00),
(7, 'Patel Madhavan', 2147483647, 'madhavanpatel19@gmail.com', 'web devlpor', '2026-06-18', '45 day', 14996.00);

-- --------------------------------------------------------

--
-- Table structure for table `project_budget_phases`
--

CREATE TABLE `project_budget_phases` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `phase_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `expected_date` date DEFAULT NULL,
  `cost` decimal(15,2) DEFAULT 0.00,
  `received_amount` decimal(15,2) DEFAULT 0.00,
  `received_date` date DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_budget_phases`
--

INSERT INTO `project_budget_phases` (`id`, `project_id`, `phase_name`, `description`, `expected_date`, `cost`, `received_amount`, `received_date`, `remark`, `created_at`) VALUES
(2, 21, 'Project Execution', 'Initial budget allocation', NULL, 12300012.00, 0.00, NULL, '', '2026-06-10 06:47:32'),
(3, 22, 'demo', 'Initial budget allocation', NULL, 12121212.00, 10000.00, '2026-06-10', '', '2026-06-10 07:21:08'),
(8, 24, 'Project Execution', 'Initial budget allocation', NULL, 2000000.00, 0.00, NULL, '', '2026-06-11 08:56:55'),
(9, 24, 'dwqd', 'efwf', NULL, 2000000.00, 12330.00, '2026-12-31', 'vd', '2026-06-11 08:56:55'),
(10, 27, 'Project Execution', 'Initial budget allocation', NULL, 5000.00, 0.00, NULL, '', '2026-06-11 12:04:59'),
(11, 27, 'Phase 1', 'pay via remitly', NULL, 5000.00, 1000.00, '2026-06-12', 'Net Banking', '2026-06-11 12:04:59'),
(13, 29, 'phasa 1', 'dmeo', NULL, 12300012.00, 10000.00, '2026-06-11', 'Debit Card', '2026-06-11 12:46:31'),
(14, 29, 'phase2', 'ewfewe', NULL, 12290012.00, 10000.00, '2026-06-11', 'Cash', '2026-06-11 12:46:31'),
(20, 31, 'Phase 1', 'demi', NULL, 101.00, 90.00, '2026-06-11', 'PhonePe', '2026-06-11 13:06:26'),
(21, 31, 'Phase 2', 'demi', NULL, 200.00, 0.00, NULL, '', '2026-06-11 13:06:26'),
(22, 31, 'Phase 3', 'demi', NULL, 101.00, 0.00, NULL, '', '2026-06-11 13:06:26'),
(23, 31, 'Phase 4', 'demide', NULL, 100.00, 90.00, '2026-06-11', 'Cash', '2026-06-11 13:06:26'),
(24, 31, 'Phase 5', '123', NULL, 101.00, 0.00, NULL, '', '2026-06-11 13:06:26'),
(27, 33, 'Phase 1', '123', NULL, 0.00, 1200.00, '2026-12-31', 'PhonePe', '2026-06-12 04:50:00'),
(34, 35, 'Phase 1', 'demi', '2026-12-31', 12.00, 0.00, NULL, NULL, '2026-06-12 12:10:17'),
(35, 36, 'Phase 1', '', NULL, 12.00, 0.00, NULL, NULL, '2026-06-12 12:12:46'),
(40, 34, 'Phase 1', 'system', NULL, 2000.00, 0.00, NULL, NULL, '2026-06-12 13:03:43'),
(41, 34, 'Phase 2', 'engineering', NULL, 2000.00, 0.00, NULL, NULL, '2026-06-12 13:03:43'),
(42, 34, 'Phase 3', 'data', NULL, 1000.00, 0.00, NULL, NULL, '2026-06-12 13:03:43'),
(44, 25, 'Phase 1', '', NULL, 0.00, 0.00, NULL, NULL, '2026-06-17 09:49:16'),
(45, 32, 'Phase 1', '', '2026-12-31', 100.00, 0.00, NULL, NULL, '2026-06-18 05:09:23'),
(46, 26, 'Phase 1', '', NULL, 0.00, 0.00, NULL, NULL, '2026-06-18 05:09:40'),
(49, 37, 'Phase 1', 'demi', NULL, 343.00, 12131.00, NULL, '', '2026-06-22 06:58:57');

-- --------------------------------------------------------

--
-- Table structure for table `project_documents`
--

CREATE TABLE `project_documents` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_documents`
--

INSERT INTO `project_documents` (`id`, `project_id`, `document_name`, `file_path`, `created_at`) VALUES
(1, 20, 'demo', 'project_docs/project_20_1778752820_6a059d34cbf1f.jpeg', '2026-05-14 10:00:20'),
(2, 21, 'demo', '1781067498_5242.jpg', '2026-06-10 04:58:18'),
(3, 34, 'Project Proposal', '1781265380_9598.png', '2026-06-12 11:56:20'),
(5, 37, 'demo', '1782111356_6156.jpg', '2026-06-22 06:55:56');

-- --------------------------------------------------------

--
-- Table structure for table `project_links`
--

CREATE TABLE `project_links` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `link_url` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_links`
--

INSERT INTO `project_links` (`id`, `project_id`, `link_name`, `link_url`, `created_at`) VALUES
(1, 21, 'demo', 'http://localhost/8DOTS/admin_area/index.php?add_project', '2026-06-10 04:58:18'),
(8, 34, 'website', 'file:///C:/Users/LENOVO/Desktop/index-expandable.html', '2026-06-12 13:03:43'),
(9, 34, 'efjkw', 'file:///C:/Users/Madhavan/AppData/Local/Packages/5319275A.WhatsAppDesktop_cv1g1gvanyjgm/LocalState/sessions/262DC2B5AD011BAB12793922FAC5FE9B99DE30E1/transfers/2026-24/Tax%20Invoice%20%E2%80%93%20crroco123.pdf', '2026-06-12 13:03:43'),
(10, 34, 'cd', 'https://chatgpt.com/c/6a2beba9-2fb8-83e8-9338-02d1777c1ff9', '2026-06-12 13:03:43'),
(11, 36, 'nkjn', 'https://cadletedesigns.com/', '2026-06-12 13:09:28'),
(13, 37, 'website', 'http://localhost/8DOTS/admin_area/index.php?add_project', '2026-06-22 06:55:56');

-- --------------------------------------------------------

--
-- Table structure for table `project_team_todos`
--

CREATE TABLE `project_team_todos` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `due_date` date DEFAULT NULL,
  `priority` varchar(50) DEFAULT 'Medium',
  `status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_team_todos`
--

INSERT INTO `project_team_todos` (`id`, `project_id`, `emp_id`, `task_name`, `due_date`, `priority`, `status`, `created_at`) VALUES
(19, 36, 20, 'demo', '2026-06-20', 'High', 1, '2026-06-17 12:50:57'),
(20, 34, 11, 'demo', NULL, 'Medium', 1, '2026-06-17 12:52:08'),
(21, 34, 11, 'demo1', NULL, 'Medium', 1, '2026-06-17 12:52:19'),
(22, 34, 11, 'sdcme', '2026-06-18', 'Medium', 1, '2026-06-17 12:52:34'),
(23, 28, 11, 'edew', '2026-06-18', 'Medium', 1, '2026-06-18 04:47:15'),
(24, 0, 14, 'weg', '2026-06-18', 'Low', 1, '2026-06-18 06:52:47'),
(25, 0, 14, 'wevsd', '2026-06-18', 'Medium', 1, '2026-06-18 06:52:52'),
(26, 0, 14, 'fvd', '2026-06-19', 'Medium', 1, '2026-06-18 06:53:09'),
(27, 0, 19, 'dew', '2026-06-26', 'Low', 1, '2026-06-18 06:55:57'),
(28, 0, 19, 'efw', '2026-06-18', 'High', 1, '2026-06-18 06:57:11'),
(29, 0, 11, 'de', '2026-06-22', 'High', 1, '2026-06-22 06:59:31');

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
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcement_read`
--
ALTER TABLE `announcement_read`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ann_read_emp` (`emp_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emp_date` (`emp_id`,`attendance_date`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_industries`
--
ALTER TABLE `client_industries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_projects`
--
ALTER TABLE `client_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `client_project_remarks`
--
ALTER TABLE `client_project_remarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `company_links`
--
ALTER TABLE `company_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_feedback`
--
ALTER TABLE `customer_feedback`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `emp_salary_history`
--
ALTER TABLE `emp_salary_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emp_month` (`emp_id`,`month`);

--
-- Indexes for table `experience_letters`
--
ALTER TABLE `experience_letters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lead_followups`
--
ALTER TABLE `lead_followups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_id` (`lead_id`);

--
-- Indexes for table `lead_sources`
--
ALTER TABLE `lead_sources`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `source_name` (`source_name`);

--
-- Indexes for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_leave_emp` (`emp_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nda_forms`
--
ALTER TABLE `nda_forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offer_letters`
--
ALTER TABLE `offer_letters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_budget_phases`
--
ALTER TABLE `project_budget_phases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_links`
--
ALTER TABLE `project_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_team_todos`
--
ALTER TABLE `project_team_todos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `announcement_read`
--
ALTER TABLE `announcement_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `client_industries`
--
ALTER TABLE `client_industries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `client_projects`
--
ALTER TABLE `client_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `client_project_remarks`
--
ALTER TABLE `client_project_remarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `company_links`
--
ALTER TABLE `company_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `customer_feedback`
--
ALTER TABLE `customer_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `emp_list`
--
ALTER TABLE `emp_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `emp_performance`
--
ALTER TABLE `emp_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `emp_salary_history`
--
ALTER TABLE `emp_salary_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `experience_letters`
--
ALTER TABLE `experience_letters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lead_followups`
--
ALTER TABLE `lead_followups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `lead_sources`
--
ALTER TABLE `lead_sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nda_forms`
--
ALTER TABLE `nda_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `offer_letters`
--
ALTER TABLE `offer_letters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `project_budget_phases`
--
ALTER TABLE `project_budget_phases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `project_documents`
--
ALTER TABLE `project_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `project_links`
--
ALTER TABLE `project_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `project_team_todos`
--
ALTER TABLE `project_team_todos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcement_read`
--
ALTER TABLE `announcement_read`
  ADD CONSTRAINT `fk_ann_read_emp` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_projects`
--
ALTER TABLE `client_projects`
  ADD CONSTRAINT `client_projects_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_project_remarks`
--
ALTER TABLE `client_project_remarks`
  ADD CONSTRAINT `client_project_remarks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `client_projects` (`id`) ON DELETE CASCADE;

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

--
-- Constraints for table `emp_salary_history`
--
ALTER TABLE `emp_salary_history`
  ADD CONSTRAINT `fk_salary_emp` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lead_followups`
--
ALTER TABLE `lead_followups`
  ADD CONSTRAINT `lead_followups_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD CONSTRAINT `fk_leave_emp` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

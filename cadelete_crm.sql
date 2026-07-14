-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2026 at 12:39 PM
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
-- Database: `cadelete_crm`
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
(1, 'admin', 'admin@gmail.com', '123', 'IMG-20251208-WA0027.jpg', '987654321', 'india', 'CEO', ' hello ', 1, NULL),
(6, 'hr', 'hr@123gmail.com', '123', 'ChatGPT Image May 29, 2026, 12_03_24 PM.png', '0987654321', 'India', 'manager', '', 0, 'dashboard_view,employee_view,employee_insert,employee_update,employee_delete,attendance_view,attendance_insert,leave_view,leave_insert,leave_approve,worksheet_view,salary_view,salary_insert,salary_update,salary_delete,budget_view,budget_insert,budget_update,budget_delete,announcement_view,project_view,project_insert,project_update,project_delete,project_assign_task,project_assigned_only,project_source_view,project_source_insert,project_source_delete,todo_view,todo_insert,todo_update,todo_delete,lead_view,lead_insert,lead_update,lead_delete,client_view,client_insert,client_update,client_delete,company_link_view,company_link_insert,company_link_update,company_link_delete,offer_letter_view,offer_letter_insert,nda_view,nda_insert,experience_letter_view,experience_letter_insert');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `publish_date`, `end_date`, `is_active`, `created_at`, `deleted_at`) VALUES
(32, 'jh', 'demo', '2026-06-30 15:33:00', NULL, 0, '2026-06-30 10:04:06', '2026-07-08 18:04:22');

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
(6, 32, 23);

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
  `status` enum('present','absent','late') DEFAULT 'present',
  `remarks` varchar(255) DEFAULT NULL,
  `work_photos` text DEFAULT NULL,
  `performance` int(11) DEFAULT NULL,
  `total_duration_secs` int(11) DEFAULT 0,
  `last_resume_time` datetime DEFAULT NULL,
  `is_working` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `emp_id`, `attendance_date`, `check_in_time`, `check_out_time`, `status`, `remarks`, `work_photos`, `performance`, `total_duration_secs`, `last_resume_time`, `is_working`, `created_at`, `ip_address`, `location`) VALUES
(121, 23, '2026-06-30', '09:22:47', '19:00:00', 'present', NULL, NULL, NULL, 533, '2026-06-30 17:02:40', 1, '2026-06-30 05:52:47', NULL, NULL),
(123, 23, '2026-07-01', '08:13:00', NULL, 'present', 'fix bugs', '', NULL, 17963, '2026-07-01 15:15:00', 1, '2026-07-01 04:43:08', NULL, NULL),
(124, 23, '2026-07-02', '10:11:00', '17:24:00', 'present', 'test', '[\"work_photos\\/23_2026-07-02_1782993302_0.png\",\"work_photos\\/23_2026-07-02_1782993302_1.jpg\"]', NULL, 25930, '2026-07-02 13:16:00', 0, '2026-07-01 07:26:04', '::1', 'Local Network'),
(125, 27, '2026-07-06', '10:00:00', '17:24:00', 'present', '', NULL, NULL, 0, NULL, 0, '2026-07-06 05:56:09', NULL, NULL),
(126, 28, '2026-07-06', '10:00:00', '17:24:00', 'present', '', NULL, NULL, 0, NULL, 0, '2026-07-06 05:56:09', NULL, NULL),
(127, 23, '2026-07-06', '10:00:00', '17:24:00', 'present', '', NULL, NULL, 0, NULL, 0, '2026-07-06 05:56:09', NULL, NULL),
(128, 25, '2026-07-06', '10:00:00', '17:24:00', 'present', '', NULL, NULL, 0, NULL, 0, '2026-07-06 05:56:09', NULL, NULL),
(129, 23, '2026-07-09', NULL, NULL, 'absent', 'Leave: efe', NULL, NULL, 0, NULL, 0, '2026-07-07 04:28:27', NULL, NULL),
(130, 23, '2026-07-10', '10:18:35', NULL, 'present', 'Leave: efe', NULL, NULL, 0, '2026-07-10 10:18:35', 1, '2026-07-07 04:28:27', '::1', 'Local Network'),
(131, 23, '2026-07-11', NULL, NULL, 'absent', 'Leave: efe', NULL, NULL, 0, NULL, 0, '2026-07-07 04:28:27', NULL, NULL),
(132, 23, '2026-07-12', NULL, NULL, 'absent', 'Leave: efe', NULL, NULL, 0, NULL, 0, '2026-07-07 04:28:27', NULL, NULL),
(133, 23, '2026-07-13', NULL, NULL, 'late', '', NULL, NULL, 0, NULL, 0, '2026-07-07 04:28:27', NULL, NULL),
(134, 23, '2026-07-14', NULL, NULL, 'absent', '', NULL, NULL, 0, NULL, 0, '2026-07-07 04:28:27', NULL, NULL),
(135, 23, '2026-07-15', NULL, NULL, 'absent', 'Leave: efe', NULL, NULL, 0, NULL, 0, '2026-07-07 04:28:27', NULL, NULL),
(136, 23, '2026-07-07', '14:22:00', '15:05:00', 'present', 'ew', '[\"work_photos\\/23_2026-07-07_1783416956_0.jpg\"]', NULL, 2580, '2026-07-07 14:22:55', 0, '2026-07-07 08:52:55', '::1', 'Local Network'),
(137, 23, '2026-07-08', '10:10:12', NULL, 'present', NULL, NULL, NULL, 18709, '2026-07-08 15:26:10', 1, '2026-07-08 04:40:12', '::1', 'Local Network'),
(138, 27, '2026-07-13', '10:00:00', NULL, 'absent', '', NULL, NULL, 0, NULL, 0, '2026-07-13 11:31:04', NULL, NULL),
(139, 28, '2026-07-13', '10:00:00', NULL, 'late', '', NULL, NULL, 0, NULL, 0, '2026-07-13 11:31:04', NULL, NULL),
(140, 25, '2026-07-13', '10:00:00', NULL, 'absent', '', NULL, NULL, 0, NULL, 0, '2026-07-13 11:31:04', NULL, NULL),
(141, 27, '2026-07-14', '10:00:00', '18:00:00', 'present', '', NULL, NULL, 28800, NULL, 0, '2026-07-14 09:51:56', NULL, NULL),
(142, 28, '2026-07-14', '10:16:00', NULL, 'late', 'Late check-in', NULL, NULL, 0, NULL, 0, '2026-07-14 09:51:56', NULL, NULL),
(143, 25, '2026-07-14', '10:19:00', NULL, 'late', 'Late check-in', NULL, NULL, 0, NULL, 0, '2026-07-14 09:51:56', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` int(11) NOT NULL,
  `att_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `action` varchar(20) NOT NULL,
  `action_time` datetime NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`id`, `att_id`, `emp_id`, `action`, `action_time`, `ip_address`, `location`, `created_at`) VALUES
(1, 123, 23, 'pause', '2026-07-01 11:42:28', '::1', 'Local Network', '2026-07-01 06:12:28'),
(2, 123, 23, 'resume', '2026-07-01 11:42:53', '::1', 'Local Network', '2026-07-01 06:12:53'),
(3, 123, 23, 'pause', '2026-07-01 11:43:16', '::1', 'Local Network', '2026-07-01 06:13:16'),
(4, 123, 23, 'resume', '2026-07-01 11:43:19', '::1', 'Local Network', '2026-07-01 06:13:19'),
(5, 123, 23, 'check_out', '2026-07-01 15:13:00', '::1', 'Local Network', '2026-07-01 09:43:58'),
(6, 123, 23, 'resume', '2026-07-01 15:15:00', '::1', 'Local Network', '2026-07-01 09:45:00'),
(7, 124, 23, 'check_in', '2026-07-02 10:11:35', '::1', 'Local Network', '2026-07-02 04:41:35'),
(8, 124, 23, 'pause', '2026-07-02 13:15:45', '::1', 'Local Network', '2026-07-02 07:45:45'),
(9, 124, 23, 'resume', '2026-07-02 13:16:00', '::1', 'Local Network', '2026-07-02 07:46:00'),
(10, 124, 23, 'check_out', '2026-07-02 17:24:00', '::1', 'Local Network', '2026-07-02 11:55:02'),
(11, 136, 23, 'check_in', '2026-07-07 14:22:55', '::1', 'Local Network', '2026-07-07 08:52:55'),
(12, 136, 23, 'check_out', '2026-07-07 15:05:00', '::1', 'Local Network', '2026-07-07 09:35:56'),
(13, 137, 23, 'check_in', '2026-07-08 10:10:12', '::1', 'Local Network', '2026-07-08 04:40:12'),
(14, 137, 23, 'pause', '2026-07-08 15:22:01', '::1', 'Local Network', '2026-07-08 09:52:01'),
(15, 137, 23, 'resume', '2026-07-08 15:26:10', '::1', 'Local Network', '2026-07-08 09:56:10'),
(16, 130, 23, 'check_in', '2026-07-10 10:18:35', '::1', 'Local Network', '2026-07-10 04:48:35');

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
  `industry` varchar(100) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `image`, `name`, `mobile`, `email`, `country`, `company_name`, `website`, `created_at`, `status`, `industry`, `deleted_at`) VALUES
(45, '1782794422_4067.png', 'Patel Madhavan', '1234353222', 'madhavanpatel19@gmail.com', 'India', '8dots123', 'https://www.figma.com/design/41kQ1s4X3LY1HcwtY4c3hQ/CADLETE-CRM?node-id=68-3&t=qmv7Uj5y2jjlng3R-0', '2026-06-30 04:40:22', 'Active', '8dots', NULL),
(46, '1782794563_2264.jpeg', 'rajveer', '9876543211', 'madhavanpatel19@gmail.com', 'India', '8dots', 'https://8dots.in', '2026-06-30 04:42:43', 'Active', '8dots, 8dots1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `client_industries`
--

CREATE TABLE `client_industries` (
  `id` int(11) NOT NULL,
  `industry_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_industries`
--

INSERT INTO `client_industries` (`id`, `industry_name`, `created_at`, `deleted_at`) VALUES
(5, '8dots', '2026-06-24 10:39:22', NULL),
(8, '8dots1', '2026-06-24 10:42:02', NULL);

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
  `assigned_employees` text DEFAULT NULL,
  `assigned_users` text DEFAULT NULL,
  `assigned_admins` text DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_projects`
--

INSERT INTO `client_projects` (`id`, `client_id`, `project_name`, `project_date`, `budget`, `currency`, `status`, `source`, `created_at`, `deadline`, `project_desc`, `project_image`, `assigned_employees`, `assigned_users`, `assigned_admins`, `deleted_at`) VALUES
(38, 45, 'crroco123', '2026-06-30', 10000.00, 'INR', 'Active', '', '2026-06-30 04:44:17', '2026-06-30', '23423', '1782794657_2646.jpg', '27,28,23,25', '', '1,4', NULL),
(39, 45, 'crroco123123', '2026-06-25', 120000.00, 'INR', 'Completed', 'family ', '2026-06-30 05:34:42', '2026-06-24', '322', '1782797682_9838.jpeg', '27,28', '', '1', NULL),
(40, 45, 'crroco', '2026-07-14', 12300012.00, 'INR', 'Active', 'bro', '2026-07-06 11:31:29', '2026-07-07', 'efer', '1783337489_8233.jpg', '23,25', '', '6', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `client_project_remarks`
--

CREATE TABLE `client_project_remarks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_project_remarks`
--

INSERT INTO `client_project_remarks` (`id`, `project_id`, `remark`, `created_at`, `deleted_at`) VALUES
(96, 38, 'ewef', '2026-06-30 05:33:21', NULL),
(97, 39, 'System: Project status updated to Completed', '2026-07-08 09:55:48', NULL);

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
  `is_pinned` tinyint(1) DEFAULT 0,
  `uploaded_by_type` enum('admin','employee') DEFAULT 'admin',
  `uploaded_by_id` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_links`
--

INSERT INTO `company_links` (`id`, `link_name`, `link_url`, `category`, `created_at`, `is_pinned`, `uploaded_by_type`, `uploaded_by_id`, `deleted_at`) VALUES
(31, 'cd', 'https://chatgpt.com/c/6a2fd810-ce98-83ee-ae23-c8c72a491147', 'demo', '2026-07-02 06:08:09', 1, 'admin', NULL, NULL),
(32, 'figma12', 'uploads/company_links/1782977363_Gemini_Generated_Image_9toiw09toiw09toi.png', 'dem', '2026-07-02 07:29:23', 1, 'admin', NULL, NULL),
(34, 'demo', 'http://localhost/8DOTS/admin_area/index.php?company_links', 'demo', '2026-07-07 09:21:18', 1, 'employee', 23, NULL),
(35, 'cd', 'uploads/company_links/1783416087_takeaway-menu.pdf', 'demo', '2026-07-07 09:21:27', 1, 'employee', 23, NULL),
(36, 'efjkw', 'uploads/company_links/1783416094_britanni spice takeaway menu.pdf', 'demo', '2026-07-07 09:21:34', 0, 'employee', 23, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_links_assignments`
--

CREATE TABLE `company_links_assignments` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_links_assignments`
--

INSERT INTO `company_links_assignments` (`id`, `category`, `emp_id`, `created_at`) VALUES
(12, 'demo', 23, '2026-07-02 07:09:21'),
(13, 'dem', 27, '2026-07-02 07:40:16'),
(14, 'dem', 28, '2026-07-02 07:40:16'),
(15, 'dem', 25, '2026-07-02 07:40:16');

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

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_list`
--

INSERT INTO `emp_list` (`id`, `name`, `phone_number`, `address`, `email`, `password`, `blood_group`, `gender`, `join_date`, `salary`, `basic_salary`, `hra`, `allowance`, `deductions`, `documents`, `age`, `dob`, `work_experience`, `marital_status`, `num_dependents`, `emergency_name`, `emergency_relationship`, `emergency_address`, `emergency_phone`, `education_json`, `employment_json`, `account_name`, `bank_branch`, `account_number`, `account_type_ifsc`, `employee_image`, `offer_latter`, `NDA`, `Aadhar_card`, `Pan_card`, `Passportsize_photo`, `old_company_slary_slip`, `otp`, `otp_expire`, `last_birthday_wish_year`, `department`, `designation`, `status`, `deleted_at`) VALUES
(23, 'Patel Madhavan1', 1234566779, 'Gota', 'madhavanpatel19@gmail.com', '123', 'A+', 'Male', '2026-06-29', 20000, 20000.00, 0.00, 0.00, 0.00, NULL, 26, '2000-06-09', '', 'Single', 0, '', '', '', '', '[]', '[]', '', '', '', '', '1782882504_4083.jpeg', '', '', '', '', '', '', '', NULL, NULL, 'Not Assigned', 'Not Assigned', 'Active', NULL),
(25, 'ram', 2147483647, 'Gota', 'madhavanpatel19@gmail.com', '1234', 'A+', 'Male', '2026-06-29', 0, 15000.00, 0.00, 0.00, 0.00, NULL, 0, '2009-06-29', '', 'Single', 0, '', '', '', '', '[]', '[]', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, 'Not Assigned', 'Not Assigned', 'Active', NULL),
(27, 'Patel Madhavan', 2147483647, 'Gota', 'madhavanpatel19@gmail.com', '12345', 'A+', 'Male', '2026-06-29', 120001, 120001.00, 0.00, 0.00, 0.00, NULL, 0, '2026-06-30', '', 'Single', 0, '', '', '', '', '[]', '[]', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, 'Not Assigned', 'Not Assigned', 'Active', NULL),
(28, 'Patel Madhavan', 2147483647, 'Gota', 'madhavanpatel19@gmail.com', '123456', 'B+', 'Male', '2026-06-29', 15000, 15000.00, 0.00, 0.00, 0.00, NULL, 15, '2010-06-30', '', 'Single', 0, '', '', '', '', '[]', '[]', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, 'Not Assigned', 'Not Assigned', 'Inactive', NULL);

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

-- --------------------------------------------------------

--
-- Table structure for table `emp_personal_categories`
--

CREATE TABLE `emp_personal_categories` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_personal_categories`
--

INSERT INTO `emp_personal_categories` (`id`, `emp_id`, `category_name`, `created_at`) VALUES
(1, 23, 'madhavan', '2026-07-08 06:25:16');

-- --------------------------------------------------------

--
-- Table structure for table `emp_personal_documents`
--

CREATE TABLE `emp_personal_documents` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `doc_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emp_personal_resources`
--

CREATE TABLE `emp_personal_resources` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `resource_type` varchar(50) DEFAULT 'link',
  `link_name` varchar(255) NOT NULL,
  `link_url` text DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_personal_resources`
--

INSERT INTO `emp_personal_resources` (`id`, `emp_id`, `category`, `resource_type`, `link_name`, `link_url`, `is_pinned`, `created_at`) VALUES
(1, 23, 'madhavan', 'link', 'figma', 'https://web.whatsapp.com/', 1, '2026-07-08 06:25:23');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `experience_letters`
--

INSERT INTO `experience_letters` (`id`, `name`, `email`, `number`, `designation`, `join_date`, `relieve_date`, `created_at`, `deleted_at`) VALUES
(4, 'Patel Madhavan', 'madhavanpatel19@gmail.com', '98765432', 'hr', '2026-06-23', '2026-06-29', '2026-06-30 06:53:14', NULL);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `client_name`, `phone`, `email`, `company_name`, `project_name`, `description`, `budget`, `currency`, `remark`, `lead_source`, `status`, `followup_date`, `created_at`, `deleted_at`) VALUES
(8, 'Patel Madhavan', '0987654321', 'madhavanpatel19@gmail.com', '8dots DEMO', 'crroco', '', '123000', 'INR', '', 'qwewe', 'active', '2026-06-30', '2026-06-30 06:08:57', NULL);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lead_followups`
--

INSERT INTO `lead_followups` (`id`, `lead_id`, `followup_date`, `followup_method`, `followup_type`, `remark`, `created_at`, `deleted_at`) VALUES
(18, 8, '2026-06-30', 'Phone', 'General Remark', '', '2026-06-30 06:26:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_sources`
--

CREATE TABLE `lead_sources` (
  `id` int(11) NOT NULL,
  `source_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lead_sources`
--

INSERT INTO `lead_sources` (`id`, `source_name`, `created_at`, `deleted_at`) VALUES
(30, 'family ', '2026-06-30 05:24:35', NULL),
(32, 'qwewe', '2026-06-30 06:03:21', NULL),
(34, 'bro', '2026-06-30 10:48:32', NULL),
(35, 'hr', '2026-07-01 05:25:43', NULL);

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
(7, 23, 4, '2026-07-01', '2026-07-02', 'demo', 'approved', '2026-06-30 12:16:55'),
(8, 23, 4, '2026-07-09', '2026-07-15', 'efe', 'approved', '2026-07-06 06:20:57');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL,
  `leave_name` varchar(255) NOT NULL,
  `num_of_leave` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `leave_name`, `num_of_leave`, `created_at`, `deleted_at`) VALUES
(4, 'demo', 10, '2026-06-30 12:16:29', NULL),
(5, 'den1', 10, '2026-06-30 12:20:16', NULL),
(6, 'demo', 12, '2026-06-30 12:20:24', NULL),
(7, 'abc', 23, '2026-06-30 12:20:32', NULL),
(8, 'dfks', 12, '2026-06-30 12:20:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nda_forms`
--

INSERT INTO `nda_forms` (`id`, `name`, `number`, `email`, `position`, `salary`, `start_date`, `notice_period`, `created_at`, `deleted_at`) VALUES
(3, 'Patel Madhavan', '9876543213', 'madhavanpatel19@gmail.com', 'hr', NULL, '2026-07-13', NULL, '2026-06-30 06:52:42', NULL);

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
  `salary` decimal(10,2) NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offer_letters`
--

INSERT INTO `offer_letters` (`id`, `name`, `number`, `email`, `position`, `start_date`, `notice_period`, `salary`, `deleted_at`) VALUES
(8, 'Patel Madhavan', 2147483647, 'madhavanpatel19@gmail.com', 'web devloper', '2026-07-02', '90 days', 12000.00, NULL);

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
(56, 38, 'Phase 1', 'teset1', '2026-06-30', 5000.00, 0.00, NULL, NULL, '2026-07-02 09:56:58'),
(57, 38, 'Phase 2', 'test2 ', '2026-07-04', 5000.00, 0.00, NULL, NULL, '2026-07-02 09:56:58'),
(58, 39, 'Phase 1', '', NULL, 0.00, 0.00, NULL, NULL, '2026-07-02 09:57:29'),
(59, 40, 'Phase 1', '', NULL, 0.00, 0.00, NULL, NULL, '2026-07-06 11:31:29');

-- --------------------------------------------------------

--
-- Table structure for table `project_documents`
--

CREATE TABLE `project_documents` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_links`
--

CREATE TABLE `project_links` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `link_url` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_links`
--

INSERT INTO `project_links` (`id`, `project_id`, `link_name`, `link_url`, `created_at`, `deleted_at`) VALUES
(22, 38, 'website', 'http://localhost/8DOTS/admin_area/index.php?add_project', '2026-07-02 09:56:58', NULL),
(23, 38, 'ddd', 'https://web.whatsapp.com/', '2026-07-02 09:56:58', NULL),
(24, 38, 'efjkw', 'http://localhost/8DOTS/admin_area/index.php', '2026-07-02 09:56:58', NULL);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_team_todos`
--

INSERT INTO `project_team_todos` (`id`, `project_id`, `emp_id`, `task_name`, `due_date`, `priority`, `status`, `created_at`, `deleted_at`) VALUES
(40, 0, 22, 'efjefke', '2026-06-24', 'Medium', 1, '2026-06-24 11:27:10', NULL),
(41, 0, 22, 'dfjoef', '2026-06-24', 'Medium', 1, '2026-06-24 11:27:15', NULL),
(42, 0, 22, 'wdkdkv', '2026-06-24', 'Medium', 1, '2026-06-24 11:28:38', NULL),
(43, 0, 22, 'dkvfdvvkf', '2026-06-24', 'Medium', 1, '2026-06-24 11:28:42', NULL),
(44, 0, 22, 'fkvaekver', '2026-06-24', 'Medium', 1, '2026-06-24 11:28:59', NULL),
(45, 0, 23, 'wew', '2026-06-30', 'Medium', 1, '2026-06-30 04:23:11', NULL),
(46, 0, 23, 'faga', '2026-07-01', 'High', 1, '2026-07-01 11:25:37', NULL),
(47, 38, 23, 'ewfawf', NULL, 'Medium', 1, '2026-07-01 11:45:06', NULL),
(48, 38, 23, 'afafrrde', NULL, 'Medium', 1, '2026-07-01 11:45:10', NULL),
(49, 0, 23, 'wefef', '2026-07-02', 'Low', 1, '2026-07-02 11:30:30', NULL),
(50, 0, 23, 'ewfeae', '2026-07-02', 'Medium', 1, '2026-07-02 11:30:34', NULL),
(51, 0, 23, 'efadvdfvd', '2026-07-02', 'High', 1, '2026-07-02 11:30:38', NULL),
(52, 0, 23, 'frefer', '2026-07-02', 'Low', 1, '2026-07-02 11:30:44', NULL),
(53, 0, 23, 'demo1', '2026-12-31', 'High', 1, '2026-07-07 09:25:27', NULL),
(54, 0, 27, 'ew', '2026-07-08', 'Low', 1, '2026-07-08 10:03:22', NULL),
(55, 0, 25, '4ee', '2026-07-08', 'Medium', 1, '2026-07-08 10:05:42', NULL),
(56, 0, 25, 'qefer', '2026-07-08', 'Medium', 1, '2026-07-08 10:05:44', NULL);

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
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `att_id` (`att_id`);

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
-- Indexes for table `company_links_assignments`
--
ALTER TABLE `company_links_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cat_emp` (`category`,`emp_id`);

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
-- Indexes for table `emp_personal_categories`
--
ALTER TABLE `emp_personal_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emp_personal_documents`
--
ALTER TABLE `emp_personal_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emp_personal_resources`
--
ALTER TABLE `emp_personal_resources`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
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
  MODIFY `admin_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `announcement_read`
--
ALTER TABLE `announcement_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `client_industries`
--
ALTER TABLE `client_industries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `client_projects`
--
ALTER TABLE `client_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `client_project_remarks`
--
ALTER TABLE `client_project_remarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `company_links`
--
ALTER TABLE `company_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `company_links_assignments`
--
ALTER TABLE `company_links_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `emp_performance`
--
ALTER TABLE `emp_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `emp_personal_categories`
--
ALTER TABLE `emp_personal_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `emp_personal_documents`
--
ALTER TABLE `emp_personal_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_personal_resources`
--
ALTER TABLE `emp_personal_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `emp_salary_history`
--
ALTER TABLE `emp_salary_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `experience_letters`
--
ALTER TABLE `experience_letters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lead_followups`
--
ALTER TABLE `lead_followups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `lead_sources`
--
ALTER TABLE `lead_sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nda_forms`
--
ALTER TABLE `nda_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `offer_letters`
--
ALTER TABLE `offer_letters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `project_budget_phases`
--
ALTER TABLE `project_budget_phases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `project_documents`
--
ALTER TABLE `project_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `project_links`
--
ALTER TABLE `project_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `project_team_todos`
--
ALTER TABLE `project_team_todos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

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

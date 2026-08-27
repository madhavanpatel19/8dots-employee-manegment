-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2026 at 07:13 AM
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
-- Database: `cadlete_crm`
--

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
  `permissions` text DEFAULT NULL,
  `department` varchar(255) DEFAULT 'Management'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_name`, `admin_email`, `admin_pass`, `admin_image`, `admin_contact`, `admin_country`, `admin_job`, `admin_about`, `is_super_admin`, `permissions`, `department`) VALUES
(1, 'admin', 'admin@gmail.com', '123', 'bro-takes-photos-O6khX6-XozY-unsplash.jpg', '987654321', 'india', 'CEO', ' hello ', 1, NULL, 'Management');

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

-- --------------------------------------------------------

--
-- Table structure for table `announcement_read`
--

CREATE TABLE `announcement_read` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `status` varchar(20) DEFAULT 'present',
  `remarks` text DEFAULT NULL,
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
(1, 1, '2026-08-26', '09:45:00', '18:47:00', 'present', '', NULL, NULL, 32520, NULL, 0, '2026-08-25 10:00:12', NULL, NULL),
(2, 1, '2026-08-27', '09:51:44', NULL, 'present', 'Leave: fesival leavs', NULL, NULL, 0, '2026-08-27 09:51:44', 1, '2026-08-25 10:00:12', '::1', 'Local Network'),
(3, 1, '2026-08-19', NULL, NULL, 'leave', 'Leave: medical', NULL, NULL, 0, NULL, 0, '2026-08-25 10:11:06', NULL, NULL);

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
(1, 2, 1, 'check_in', '2026-08-27 09:51:44', '::1', 'Local Network', '2026-08-27 04:21:44');

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
(1, '1787637856_4722.png', 'kamal bhai parmar', '0987654323', '8dots.in@gmail.com', 'India', '8DOTS', 'https://8dots.in/', '2026-08-25 06:04:16', 'Active', 'BNI', NULL);

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
(1, 'BNI', '2026-08-25 06:04:14', NULL);

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
(1, 1, '8dots CRM', '2026-08-25', 10000.00, 'INR', 'Completed', 'BNI', '2026-08-25 06:11:48', '2026-08-31', 'crm', '1787638308_6544.png', '1', '', '1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `client_project_remarks`
--

CREATE TABLE `client_project_remarks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `posted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_project_remarks`
--

INSERT INTO `client_project_remarks` (`id`, `project_id`, `remark`, `created_at`, `deleted_at`, `posted_by`) VALUES
(1, 1, 'System: Project status updated to Pending', '2026-08-25 08:59:26', NULL, 'System'),
(2, 1, 'System: Project status updated to Active', '2026-08-25 08:59:39', NULL, 'System'),
(3, 1, 'HELLO', '2026-08-25 09:01:18', NULL, 'admin'),
(4, 1, 'System: All 21 SOP checklist items completed. Project status automatically changed to Completed.', '2026-08-26 05:02:22', NULL, 'System');

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

--
-- Dumping data for table `employee_documents`
--

INSERT INTO `employee_documents` (`id`, `emp_id`, `file_name`, `uploaded_at`, `deleted_at`) VALUES
(1, 1, '1787638158_extra_1379.pdf', '2026-08-25 06:09:18', NULL);

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
  `company_email` varchar(255) DEFAULT NULL,
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
  `deleted_at` datetime DEFAULT NULL,
  `allowed_leaves` int(11) DEFAULT NULL,
  `extra_leaves` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_list`
--

INSERT INTO `emp_list` (`id`, `name`, `phone_number`, `address`, `email`, `company_email`, `password`, `blood_group`, `gender`, `join_date`, `salary`, `basic_salary`, `hra`, `allowance`, `deductions`, `documents`, `age`, `dob`, `work_experience`, `marital_status`, `num_dependents`, `emergency_name`, `emergency_relationship`, `emergency_address`, `emergency_phone`, `education_json`, `employment_json`, `account_name`, `bank_branch`, `account_number`, `account_type_ifsc`, `employee_image`, `offer_latter`, `NDA`, `Aadhar_card`, `Pan_card`, `Passportsize_photo`, `old_company_slary_slip`, `otp`, `otp_expire`, `last_birthday_wish_year`, `department`, `designation`, `status`, `deleted_at`, `allowed_leaves`, `extra_leaves`) VALUES
(1, 'Patel Madhavan', 987654321, 'Ahemedabad', 'madhavanpatel19@gmail.com', 'madhavanpatel1919@gmail.com', '123', 'A+', 'Male', '2026-08-25', 10000, 10000.00, 0.00, 0.00, 0.00, NULL, 20, '2006-01-19', '', 'Single', 0, 'rajveer', 'brother', 'bopal', '0987654323', '[{\"degree\":\"b.tech\",\"univ\":\"sou\",\"year\":\"2024\",\"grade\":\"A+\",\"city\":\"Ahmedabad \"}]', '[{\"company\":\"freshers\",\"pos\":\"intern\",\"year\":\"2yr\",\"reason\":\"\"}]', 'bob', '0987654323', 'ACC389950', 'IFSC78560', '1787638158_1566.jpeg', '1787638157_4853.pdf', '1787638157_6234.pdf', '1787638157_5664.pdf', '1787638157_2485.pdf', '1787638157_8984.pdf', '1787638158_3356.pdf', NULL, NULL, NULL, 'devloper', 'intern', 'Active', NULL, 45, 1);

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
  `assigned_employees` text DEFAULT NULL,
  `assigned_admins` text DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `client_name`, `phone`, `email`, `company_name`, `project_name`, `description`, `budget`, `currency`, `remark`, `lead_source`, `status`, `assigned_employees`, `assigned_admins`, `followup_date`, `created_at`, `deleted_at`) VALUES
(1, 'kamalbhai ', '9875643213', '8dots.in@gmail.com', '8dots', '8dots CRM', '', '10000', 'INR', '', 'BNI', 'future', '1', '1', '2026-08-25', '2026-08-25 07:05:07', NULL);

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
(1, 'Mechanical', '2026-08-25 04:22:59', '2026-08-25 11:40:06'),
(2, 'BNI', '2026-08-25 04:22:59', NULL),
(3, 'Turnkey', '2026-08-25 04:22:59', '2026-08-25 11:40:01'),
(4, 'Electrical', '2026-08-25 04:22:59', '2026-08-25 11:39:58'),
(5, 'Civil', '2026-08-25 04:22:59', '2026-08-25 11:39:54'),
(6, 'demo', '2026-08-25 06:38:37', '2026-08-25 12:08:41');

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
(1, 1, 0, '2026-08-26', '2026-08-27', 'fesival leavs', 'approved', '2026-08-25 09:48:18'),
(2, 1, 2, '2026-08-19', '2026-08-19', 'medical', 'approved', '2026-08-25 10:11:06'),
(3, 1, 0, '2026-08-27', '2026-08-31', 'demo', 'rejected', '2026-08-26 12:17:50'),
(4, 1, 1, '2026-09-01', '2026-09-07', 'demo', 'rejected', '2026-08-26 12:24:15');

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
(1, 'sick leave', 12, '2026-08-25 09:19:28', NULL),
(2, 'demo', 30, '2026-08-25 09:29:46', NULL);

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
(1, 1, 'Phase 1', 'token', '2026-08-25', 2500.00, 0.00, NULL, NULL, '2026-08-25 06:11:48'),
(2, 1, 'Phase 2', 'devloping', '2026-08-31', 5000.00, 0.00, NULL, NULL, '2026-08-25 06:11:48'),
(3, 1, 'Phase 3', 'final', '2026-09-30', 2499.90, 0.00, NULL, NULL, '2026-08-25 06:11:48');

-- --------------------------------------------------------

--
-- Table structure for table `project_documents`
--

CREATE TABLE `project_documents` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `is_proposal` tinyint(1) NOT NULL DEFAULT 0,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_documents`
--

INSERT INTO `project_documents` (`id`, `project_id`, `document_name`, `is_proposal`, `file_path`, `created_at`, `deleted_at`) VALUES
(1, 1, 'demo', 0, '1787638308_5944.pdf', '2026-08-25 06:11:48', NULL),
(2, 1, 'Project Proposal', 1, '1787638308_proposal_6304.pdf', '2026-08-25 06:11:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_expenses`
--

CREATE TABLE `project_expenses` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `qty` int(11) DEFAULT 1,
  `cost` decimal(15,2) DEFAULT 0.00,
  `total_cost` decimal(15,2) DEFAULT 0.00,
  `expense_date` date DEFAULT NULL,
  `ordered_from` varchar(255) DEFAULT NULL,
  `ordered_from_url` varchar(500) DEFAULT NULL,
  `paid_by` varchar(255) DEFAULT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `invoice_file` varchar(255) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
(1, 1, 'reference link', 'https://example.com/proj-resource/45/131', '2026-08-25 06:11:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_phase_payments`
--

CREATE TABLE `project_phase_payments` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `phase_name` varchar(255) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT 0.00,
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_sop_checklist`
--

CREATE TABLE `project_sop_checklist` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `sop_item_id` int(11) NOT NULL,
  `is_checked` tinyint(1) DEFAULT 0,
  `checked_by` varchar(255) DEFAULT NULL,
  `checked_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_sop_checklist`
--

INSERT INTO `project_sop_checklist` (`id`, `project_id`, `sop_item_id`, `is_checked`, `checked_by`, `checked_at`) VALUES
(1, 1, 16, 0, NULL, NULL),
(2, 1, 15, 0, NULL, NULL),
(3, 1, 17, 0, NULL, NULL),
(4, 1, 18, 0, NULL, NULL),
(5, 1, 19, 0, NULL, NULL),
(6, 1, 20, 0, NULL, NULL),
(8, 1, 9, 1, 'admin', '2026-08-26 14:30:43'),
(9, 1, 10, 0, NULL, NULL),
(10, 1, 11, 0, NULL, NULL),
(11, 1, 12, 0, NULL, NULL),
(12, 1, 7, 0, NULL, NULL),
(13, 1, 5, 0, NULL, NULL),
(14, 1, 6, 0, NULL, NULL),
(15, 1, 8, 0, NULL, NULL),
(16, 1, 13, 0, NULL, NULL),
(17, 1, 14, 0, NULL, NULL),
(18, 1, 4, 0, NULL, NULL),
(19, 1, 3, 0, NULL, NULL),
(20, 1, 2, 0, NULL, NULL),
(21, 1, 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_sop_items`
--

CREATE TABLE `project_sop_items` (
  `id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `item_text` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_sop_items`
--

INSERT INTO `project_sop_items` (`id`, `category`, `item_text`, `sort_order`, `created_at`) VALUES
(1, 'SETUP', 'Job Card created in Job Card Tracker', 1, '2026-08-26 04:48:07'),
(2, 'SETUP', 'Drive Main Project Folder created', 2, '2026-08-26 04:48:07'),
(3, 'SETUP', 'Social Media folder created (3d Cad Images) - PORTFOLIO SECTION', 3, '2026-08-26 04:48:07'),
(4, 'SETUP', 'Canva Whiteboard created for design tracking', 4, '2026-08-26 04:48:07'),
(5, 'EXECUTION', 'Daily work photos uploaded to CRM as work log', 5, '2026-08-26 04:48:07'),
(6, 'EXECUTION', 'Design changes tracked on Canva Whiteboard (dated)', 6, '2026-08-26 04:48:07'),
(7, 'EXECUTION', 'Phase-wise site images saved in portfolio folder', 7, '2026-08-26 04:48:07'),
(8, 'EXECUTION', 'Changelog updated after every revision', 8, '2026-08-26 04:48:07'),
(9, 'COMPLETION', 'All final files saved on Drive with Date', 9, '2026-08-26 04:48:07'),
(10, 'COMPLETION', 'Photorealistic renders created and saved', 10, '2026-08-26 04:48:07'),
(11, 'COMPLETION', 'BOM with vendors created', 11, '2026-08-26 04:48:07'),
(12, 'COMPLETION', 'Vendors added to Vendor Sheet', 12, '2026-08-26 04:48:07'),
(13, 'COMPLETION', 'Job Card updated with all final links (Fusion Link + Drive + Canva)', 13, '2026-08-26 04:48:07'),
(14, 'COMPLETION', 'Project Folder downloaded locally on Master Computer', 14, '2026-08-26 04:48:07'),
(15, 'MARKETING', 'Add Content of Portfolio on Sheet', 15, '2026-08-26 04:48:07'),
(16, 'MARKETING', 'Add Content of Case Study on Sheet', 16, '2026-08-26 04:48:07'),
(17, 'MARKETING', 'Portfolio images organised in Drive by phase', 17, '2026-08-26 04:48:07'),
(18, 'MARKETING', 'Figma - Social Media content created (Insta, Case Study, etc.)', 18, '2026-08-26 04:48:07'),
(19, 'MARKETING', 'Portfolio website updated FR + Case Study', 19, '2026-08-26 04:48:07'),
(20, 'MARKETING', 'Social Media Posts published on all Social Media platforms', 20, '2026-08-26 04:48:07');

-- --------------------------------------------------------

--
-- Table structure for table `project_team_todos`
--

CREATE TABLE `project_team_todos` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `priority` varchar(50) DEFAULT 'Medium',
  `status` tinyint(1) DEFAULT 0,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_team_todos`
--

INSERT INTO `project_team_todos` (`id`, `project_id`, `emp_id`, `task_name`, `description`, `due_date`, `priority`, `status`, `completed_at`, `created_at`, `deleted_at`) VALUES
(1, 0, 1, 'demo', NULL, '2026-01-12', 'Medium', 0, NULL, '2026-08-25 11:41:10', NULL),
(2, 1, 1, 'demo', '', NULL, 'Medium', 0, NULL, '2026-08-25 11:41:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_todo_attachments`
--

CREATE TABLE `project_todo_attachments` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` varchar(50) DEFAULT NULL,
  `uploaded_by_admin` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_todo_comments`
--

CREATE TABLE `project_todo_comments` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `attachment_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_notifications`
--

CREATE TABLE `system_notifications` (
  `id` int(11) NOT NULL,
  `recipient_type` varchar(20) NOT NULL,
  `recipient_id` int(11) DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_notifications`
--

INSERT INTO `system_notifications` (`id`, `recipient_type`, `recipient_id`, `title`, `message`, `url`, `type`, `is_read`, `created_at`) VALUES
(1, 'employee', 1, 'Assigned to Project: 8dots CRM', 'You have been assigned to project \'8dots CRM\'.', 'index.php?team_todo&project_id=1', 'project_assigned', 1, '2026-08-25 06:11:48'),
(2, 'employee', 1, 'Leave Request Approved', 'Your leave request (26 Aug 2026 to 27 Aug 2026) has been approved.', 'index.php?leave_application', 'success', 1, '2026-08-25 10:00:12'),
(3, 'employee', 1, 'Leave Request Approved', 'Your leave request (26 Aug 2026 to 27 Aug 2026) has been approved.', 'index.php?leave_application', 'success', 1, '2026-08-25 10:05:08'),
(4, 'employee', 1, 'Leave Request Approved', 'Your leave request (26 Aug 2026 to 27 Aug 2026) has been approved.', 'index.php?leave_application', 'success', 1, '2026-08-25 10:05:10'),
(5, 'employee', 1, 'New Leave Record Added', 'Admin added a leave record (19 Aug 2026 to 19 Aug 2026) for you.', 'index.php?leave_application', 'info', 1, '2026-08-25 10:11:06'),
(6, 'admin', 1, 'New Task in Project', 'Patel Madhavan added task \'demo\' in Project.', 'index.php?team_todo&project_id=0&open_task_id=0&emp_id=1', 'task_assigned', 1, '2026-08-25 11:41:10'),
(7, 'admin', 1, 'New Task in 8dots CRM', 'Patel Madhavan added task \'demo\' in 8dots CRM.', 'index.php?team_todo&project_id=1&open_task_id=0&emp_id=1', 'task_assigned', 1, '2026-08-25 11:41:34'),
(8, 'admin', 1, '✅ Project SOP 100% Complete!', 'All 21 SOP checklist items for project \"8dots CRM\" have been completed by admin. Status auto-changed to Completed.', 'index.php?projects', 'success', 1, '2026-08-26 05:02:22'),
(9, 'admin', 1, 'New Leave Request: Patel Madhavan', 'Patel Madhavan applied for Extra Leaves (27 Aug 2026 to 31 Aug 2026). Reason: demo', 'index.php?view_leave_requests', 'warning', 1, '2026-08-26 12:17:50'),
(10, 'employee', 0, 'Leave Request Rejected', 'Your leave request (01 Jan 1970 to 01 Jan 1970) has been rejected.', 'index.php?leave_application', 'danger', 0, '2026-08-26 12:21:46'),
(11, 'admin', 1, 'New Leave Request: Patel Madhavan', 'Patel Madhavan applied for sick leave (01 Sep 2026 to 07 Sep 2026, 7 days). Reason: demo', 'index.php?view_leave_requests', 'warning', 1, '2026-08-26 12:24:15'),
(12, 'employee', 1, 'Leave Request Rejected', 'Your leave request (01 Sep 2026 to 07 Sep 2026) has been rejected.', 'index.php?leave_application', 'danger', 1, '2026-08-26 12:24:45'),
(13, 'employee', 1, 'Leave Request Rejected', 'Your leave request (01 Sep 2026 to 07 Sep 2026) has been rejected.', 'index.php?leave_application', 'danger', 1, '2026-08-26 12:25:15'),
(14, 'employee', 1, 'Leave Request Rejected', 'Your leave request (01 Sep 2026 to 07 Sep 2026) has been rejected.', 'index.php?leave_application', 'danger', 1, '2026-08-26 12:25:15'),
(15, 'employee', 1, 'Lead Assignment: kamalbhai ', 'You have been assigned to lead \'kamalbhai \'.', 'index.php?view_lead=1', 'lead_assigned', 0, '2026-08-27 04:39:40'),
(16, 'employee', 1, 'Lead Assignment: kamalbhai ', 'You have been assigned to lead \'kamalbhai \'.', 'index.php?view_lead=1', 'lead_assigned', 0, '2026-08-27 04:43:55');

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
-- Indexes for table `project_expenses`
--
ALTER TABLE `project_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_links`
--
ALTER TABLE `project_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_phase_payments`
--
ALTER TABLE `project_phase_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_sop_checklist`
--
ALTER TABLE `project_sop_checklist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_project_sop` (`project_id`,`sop_item_id`);

--
-- Indexes for table `project_sop_items`
--
ALTER TABLE `project_sop_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_team_todos`
--
ALTER TABLE `project_team_todos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_todo_attachments`
--
ALTER TABLE `project_todo_attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_todo_comments`
--
ALTER TABLE `project_todo_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `system_notifications`
--
ALTER TABLE `system_notifications`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcement_read`
--
ALTER TABLE `announcement_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `client_industries`
--
ALTER TABLE `client_industries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `client_projects`
--
ALTER TABLE `client_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `client_project_remarks`
--
ALTER TABLE `client_project_remarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `company_links`
--
ALTER TABLE `company_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_links_assignments`
--
ALTER TABLE `company_links_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_feedback`
--
ALTER TABLE `customer_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `emp_list`
--
ALTER TABLE `emp_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `emp_performance`
--
ALTER TABLE `emp_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_personal_categories`
--
ALTER TABLE `emp_personal_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_personal_documents`
--
ALTER TABLE `emp_personal_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_personal_resources`
--
ALTER TABLE `emp_personal_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_salary_history`
--
ALTER TABLE `emp_salary_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experience_letters`
--
ALTER TABLE `experience_letters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lead_followups`
--
ALTER TABLE `lead_followups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_sources`
--
ALTER TABLE `lead_sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offer_letters`
--
ALTER TABLE `offer_letters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_budget_phases`
--
ALTER TABLE `project_budget_phases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project_documents`
--
ALTER TABLE `project_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_expenses`
--
ALTER TABLE `project_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_links`
--
ALTER TABLE `project_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_phase_payments`
--
ALTER TABLE `project_phase_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_sop_checklist`
--
ALTER TABLE `project_sop_checklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `project_sop_items`
--
ALTER TABLE `project_sop_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `project_team_todos`
--
ALTER TABLE `project_team_todos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_todo_attachments`
--
ALTER TABLE `project_todo_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_todo_comments`
--
ALTER TABLE `project_todo_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_notifications`
--
ALTER TABLE `system_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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

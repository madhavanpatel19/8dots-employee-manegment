<?php
include("admin_area/includes/db.php");

echo "<div style='font-family: system-ui, -apple-system, sans-serif; padding: 40px; line-height: 1.6; max-width: 900px; margin: 0 auto; color: #1e293b;'>";
echo "<h1 style='color: #0f172a; font-size: 24px; font-weight: 700; margin-bottom: 8px;'>8dots CRM Database Migration & Setup</h1>";
echo "<p style='color: #64748b; margin-top: 0;'>Synchronizing all 40 database tables with live schema...</p><hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;'>";

$tables = array (
  'admins' => 'CREATE TABLE IF NOT EXISTS `admins` (
  `admin_id` int(10) NOT NULL AUTO_INCREMENT,
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
  `department` varchar(255) DEFAULT \'Management\',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'announcement_read' => 'CREATE TABLE IF NOT EXISTS `announcement_read` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ann_read_emp` (`emp_id`),
  CONSTRAINT `fk_ann_read_emp` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'announcements' => 'CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `publish_date` datetime DEFAULT current_timestamp(),
  `end_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'attendance' => 'CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `status` varchar(20) DEFAULT \'present\',
  `remarks` text DEFAULT NULL,
  `work_photos` text DEFAULT NULL,
  `performance` int(11) DEFAULT NULL,
  `total_duration_secs` int(11) DEFAULT 0,
  `last_resume_time` datetime DEFAULT NULL,
  `is_working` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emp_date` (`emp_id`,`attendance_date`),
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'attendance_logs' => 'CREATE TABLE IF NOT EXISTS `attendance_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `att_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `action` varchar(20) NOT NULL,
  `action_time` datetime NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `att_id` (`att_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'categories' => 'CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_title` text NOT NULL,
  `cat_top` text NOT NULL,
  `cat_image` text NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'client_industries' => 'CREATE TABLE IF NOT EXISTS `client_industries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `industry_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'client_project_remarks' => 'CREATE TABLE IF NOT EXISTS `client_project_remarks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `posted_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `client_project_remarks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `client_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'client_projects' => 'CREATE TABLE IF NOT EXISTS `client_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `project_date` date DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT NULL,
  `currency` varchar(20) DEFAULT \'INR\',
  `status` varchar(50) DEFAULT \'Active\',
  `source` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deadline` date DEFAULT NULL,
  `project_desc` text DEFAULT NULL,
  `project_image` varchar(255) DEFAULT NULL,
  `assigned_employees` text DEFAULT NULL,
  `assigned_users` text DEFAULT NULL,
  `assigned_admins` text DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `client_projects_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'clients' => 'CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum(\'Active\',\'Inactive\') DEFAULT \'Active\',
  `industry` varchar(100) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'company_links' => 'CREATE TABLE IF NOT EXISTS `company_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_name` varchar(255) NOT NULL,
  `link_url` text NOT NULL,
  `category` varchar(255) DEFAULT \'General\',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_pinned` tinyint(1) DEFAULT 0,
  `uploaded_by_type` enum(\'admin\',\'employee\') DEFAULT \'admin\',
  `uploaded_by_id` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'company_links_assignments' => 'CREATE TABLE IF NOT EXISTS `company_links_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cat_emp` (`category`,`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'customer_feedback' => 'CREATE TABLE IF NOT EXISTS `customer_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'emp_list' => 'CREATE TABLE IF NOT EXISTS `emp_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `department` varchar(100) DEFAULT \'Not Assigned\',
  `designation` varchar(100) DEFAULT \'Not Assigned\',
  `status` enum(\'Active\',\'Inactive\') DEFAULT \'Active\',
  `deleted_at` datetime DEFAULT NULL,
  `allowed_leaves` int(11) DEFAULT NULL,
  `extra_leaves` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'emp_performance' => 'CREATE TABLE IF NOT EXISTS `emp_performance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `perf_year` int(11) NOT NULL,
  `perf_month` int(11) NOT NULL,
  `absent` tinyint(3) unsigned DEFAULT 0,
  `late` tinyint(3) unsigned DEFAULT 0,
  `task_sheet` tinyint(3) unsigned DEFAULT 0,
  `performance_score` tinyint(3) unsigned DEFAULT 0,
  `dressing_behaviour` tinyint(3) unsigned DEFAULT 0,
  `rnd` tinyint(3) unsigned DEFAULT 0,
  `total` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `emp_month` (`emp_id`,`perf_year`,`perf_month`),
  CONSTRAINT `emp_performance_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'emp_personal_categories' => 'CREATE TABLE IF NOT EXISTS `emp_personal_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'emp_personal_documents' => 'CREATE TABLE IF NOT EXISTS `emp_personal_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `doc_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'emp_personal_resources' => 'CREATE TABLE IF NOT EXISTS `emp_personal_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `resource_type` varchar(50) DEFAULT \'link\',
  `link_name` varchar(255) NOT NULL,
  `link_url` text DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'emp_salary_history' => 'CREATE TABLE IF NOT EXISTS `emp_salary_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `net_pay` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emp_month` (`emp_id`,`month`),
  CONSTRAINT `fk_salary_emp` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'employee_documents' => 'CREATE TABLE IF NOT EXISTS `employee_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emp_id` (`emp_id`),
  CONSTRAINT `employee_documents_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'experience_letters' => 'CREATE TABLE IF NOT EXISTS `experience_letters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `relieve_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'lead_followups' => 'CREATE TABLE IF NOT EXISTS `lead_followups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_id` int(11) NOT NULL,
  `followup_date` date NOT NULL,
  `followup_method` enum(\'Phone\',\'Email\',\'WhatsApp\',\'Meeting\',\'Other\') DEFAULT \'Phone\',
  `followup_type` varchar(100) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_id` (`lead_id`),
  CONSTRAINT `lead_followups_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'lead_sources' => 'CREATE TABLE IF NOT EXISTS `lead_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `source_name` (`source_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'leads' => 'CREATE TABLE IF NOT EXISTS `leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `budget` varchar(50) DEFAULT NULL,
  `currency` varchar(10) DEFAULT \'INR\',
  `remark` text DEFAULT NULL,
  `lead_source` varchar(255) DEFAULT NULL,
  `status` enum(\'active\',\'future\',\'expired\') DEFAULT \'active\',
  `assigned_employees` text DEFAULT NULL,
  `assigned_admins` text DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'leave_applications' => 'CREATE TABLE IF NOT EXISTS `leave_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `leave_from` date NOT NULL,
  `leave_to` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum(\'pending\',\'approved\',\'rejected\') NOT NULL DEFAULT \'pending\',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_leave_emp` (`emp_id`),
  CONSTRAINT `fk_leave_emp` FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'leave_types' => 'CREATE TABLE IF NOT EXISTS `leave_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leave_name` varchar(255) NOT NULL,
  `num_of_leave` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'nda_forms' => 'CREATE TABLE IF NOT EXISTS `nda_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `start_date` date NOT NULL,
  `notice_period` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'offer_letters' => 'CREATE TABLE IF NOT EXISTS `offer_letters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `number` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `notice_period` varchar(100) DEFAULT NULL,
  `salary` decimal(10,2) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'project_budget_phases' => 'CREATE TABLE IF NOT EXISTS `project_budget_phases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `phase_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `expected_date` date DEFAULT NULL,
  `cost` decimal(15,2) DEFAULT 0.00,
  `received_amount` decimal(15,2) DEFAULT 0.00,
  `received_date` date DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'project_documents' => 'CREATE TABLE IF NOT EXISTS `project_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `is_proposal` tinyint(1) NOT NULL DEFAULT 0,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'project_expenses' => 'CREATE TABLE IF NOT EXISTS `project_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'project_links' => 'CREATE TABLE IF NOT EXISTS `project_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `link_url` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'project_phase_payments' => 'CREATE TABLE IF NOT EXISTS `project_phase_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `phase_name` varchar(255) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT 0.00,
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'project_sop_checklist' => 'CREATE TABLE IF NOT EXISTS `project_sop_checklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `sop_item_id` int(11) NOT NULL,
  `is_checked` tinyint(1) DEFAULT 0,
  `checked_by` varchar(255) DEFAULT NULL,
  `checked_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_sop` (`project_id`,`sop_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'project_sop_items' => 'CREATE TABLE IF NOT EXISTS `project_sop_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `item_text` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'project_team_todos' => 'CREATE TABLE IF NOT EXISTS `project_team_todos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `priority` varchar(50) DEFAULT \'Medium\',
  `status` tinyint(1) DEFAULT 0,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'project_todo_attachments' => 'CREATE TABLE IF NOT EXISTS `project_todo_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` varchar(50) DEFAULT NULL,
  `uploaded_by_admin` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'project_todo_comments' => 'CREATE TABLE IF NOT EXISTS `project_todo_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `attachment_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'system_notifications' => 'CREATE TABLE IF NOT EXISTS `system_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipient_type` varchar(20) NOT NULL,
  `recipient_id` int(11) DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT \'info\',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
  'users' => 'CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
);

$successCount = 0;
$errorCount = 0;

echo "<h3 style='color: #334155; margin-top: 20px;'>Creating / Verifying Tables (" . count($tables) . ")</h3>";
echo "<div style='max-height: 400px; overflow-y: auto; background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 13px;'>";

foreach ($tables as $name => $sql) {
    if (mysqli_query($con, $sql)) {
        $successCount++;
        echo "<div style='color: #059669; margin-bottom: 6px;'>✔ Table <b>" . htmlspecialchars($name) . "</b> ready.</div>";
    } else {
        $errorCount++;
        echo "<div style='color: #dc2626; margin-bottom: 6px;'>✘ Error creating " . htmlspecialchars($name) . ": " . htmlspecialchars(mysqli_error($con)) . "</div>";
    }
}
echo "</div>";

// Column migrations check for older databases
echo "<h3 style='margin-top: 30px; color: #334155;'>Running Column Migrations & Schema Fixes...</h3>";
echo "<div style='background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 13px;'>";

$columnChecks = [
    ["table" => "attendance", "column" => "work_photos", "sql" => "ALTER TABLE attendance ADD COLUMN work_photos TEXT DEFAULT NULL AFTER remarks"],
    ["table" => "attendance", "column" => "performance", "sql" => "ALTER TABLE attendance ADD COLUMN performance INT(11) DEFAULT NULL AFTER work_photos"],
    ["table" => "attendance", "column" => "total_duration_secs", "sql" => "ALTER TABLE attendance ADD COLUMN total_duration_secs INT(11) DEFAULT 0 AFTER performance"],
    ["table" => "attendance", "column" => "last_resume_time", "sql" => "ALTER TABLE attendance ADD COLUMN last_resume_time DATETIME DEFAULT NULL AFTER total_duration_secs"],
    ["table" => "attendance", "column" => "is_working", "sql" => "ALTER TABLE attendance ADD COLUMN is_working TINYINT(1) DEFAULT 0 AFTER last_resume_time"],
    ["table" => "attendance", "column" => "ip_address", "sql" => "ALTER TABLE attendance ADD COLUMN ip_address VARCHAR(50) DEFAULT NULL"],
    ["table" => "attendance", "column" => "location", "sql" => "ALTER TABLE attendance ADD COLUMN location VARCHAR(255) DEFAULT NULL"],
    ["table" => "leads", "column" => "currency", "sql" => "ALTER TABLE leads ADD COLUMN currency VARCHAR(10) DEFAULT 'INR' AFTER budget"],
    ["table" => "project_documents", "column" => "is_proposal", "sql" => "ALTER TABLE project_documents ADD COLUMN is_proposal TINYINT(1) NOT NULL DEFAULT 0 AFTER document_name"],
    ["table" => "admins", "column" => "is_super_admin", "sql" => "ALTER TABLE admins ADD COLUMN is_super_admin TINYINT(1) NOT NULL DEFAULT 0"],
    ["table" => "admins", "column" => "permissions", "sql" => "ALTER TABLE admins ADD COLUMN permissions TEXT DEFAULT NULL"],
    ["table" => "admins", "column" => "department", "sql" => "ALTER TABLE admins ADD COLUMN department VARCHAR(255) DEFAULT 'Management'"]
];

foreach ($columnChecks as $check) {
    $tbl = $check["table"];
    $col = $check["column"];
    $query = $check["sql"];
    
    $check_res = mysqli_query($con, "SHOW COLUMNS FROM `$tbl` LIKE '$col'");
    if ($check_res && mysqli_num_rows($check_res) == 0) {
        if (mysqli_query($con, $query)) {
            echo "<div style='color: #059669; margin-bottom: 6px;'>✔ Added column <b>" . htmlspecialchars($col) . "</b> to <b>" . htmlspecialchars($tbl) . "</b>.</div>";
        } else {
            echo "<div style='color: #dc2626; margin-bottom: 6px;'>✘ Error adding column " . htmlspecialchars($col) . " to " . htmlspecialchars($tbl) . ": " . htmlspecialchars(mysqli_error($con)) . "</div>";
        }
    } else {
        echo "<div style='color: #64748b; margin-bottom: 6px;'>• Column <b>" . htmlspecialchars($col) . "</b> in <b>" . htmlspecialchars($tbl) . "</b> exists.</div>";
    }
}

// Modify remarks column in attendance to TEXT if needed
mysqli_query($con, "ALTER TABLE attendance MODIFY COLUMN remarks TEXT");

echo "</div>";

// Seed default data
echo "<h3 style='margin-top: 30px; color: #334155;'>Seeding Initial Data...</h3>";
echo "<div style='background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 13px;'>";

// Default Admin
$admin_check = mysqli_query($con, "SELECT admin_id FROM admins LIMIT 1");
if (!$admin_check || mysqli_num_rows($admin_check) == 0) {
    $insert_admin = "INSERT INTO admins (admin_id, admin_name, admin_email, admin_pass, admin_image, admin_contact, admin_country, admin_job, admin_about, is_super_admin, department)
                     VALUES (1, 'admin', 'admin@gmail.com', '123', 'admin.jpg', '9876543210', 'India', 'CEO', 'Super Admin', 1, 'Management')";
    if (mysqli_query($con, $insert_admin)) {
        echo "<div style='color: #059669; margin-bottom: 6px;'>✔ Created default super admin user (admin@gmail.com).</div>";
    } else {
        echo "<div style='color: #dc2626; margin-bottom: 6px;'>✘ Error inserting default admin: " . htmlspecialchars(mysqli_error($con)) . "</div>";
    }
} else {
    echo "<div style='color: #64748b; margin-bottom: 6px;'>• Admin records already exist.</div>";
}

// Default Lead Sources
$ls_check = mysqli_query($con, "SELECT id FROM lead_sources LIMIT 1");
if (!$ls_check || mysqli_num_rows($ls_check) == 0) {
    $default_sources = ["Google", "LinkedIn", "Referral", "Direct", "Website", "Other"];
    foreach ($default_sources as $source) {
        mysqli_query($con, "INSERT IGNORE INTO lead_sources (source_name) VALUES ('$source')");
    }
    echo "<div style='color: #059669; margin-bottom: 6px;'>✔ Seeded default lead sources.</div>";
} else {
    echo "<div style='color: #64748b; margin-bottom: 6px;'>• Lead sources already seeded.</div>";
}

echo "</div>";

echo "<div style='margin-top: 30px; padding: 20px; background: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0; color: #166534;'>";
echo "<strong>Migration Complete!</strong> All " . $successCount . " tables verified/created successfully. You can now use the application.";
echo "</div>";
echo "</div>";

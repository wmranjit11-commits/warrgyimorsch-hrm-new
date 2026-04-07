-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2026 at 02:50 PM
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
-- Database: `wm_hrms`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'present',
  `total_hours` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `employee_id`, `attendance_date`, `check_in`, `check_out`, `status`, `total_hours`, `created_at`, `updated_at`) VALUES
(81, 7, '2026-04-01', '14:11:00', '18:11:00', 'half_day', 4.00, '2026-04-07 03:12:51', '2026-04-07 03:12:51'),
(82, 8, '2026-04-01', '09:12:00', '18:12:00', 'present', 9.00, '2026-04-07 03:12:51', '2026-04-07 03:12:51'),
(83, 9, '2026-04-01', '09:12:00', '18:16:00', 'present', 9.07, '2026-04-07 03:12:52', '2026-04-07 03:12:52'),
(84, 7, '2026-04-02', '14:13:00', NULL, 'present', 0.00, '2026-04-07 03:13:29', '2026-04-07 03:13:29'),
(85, 8, '2026-04-02', '09:13:00', '14:13:00', 'present', 5.00, '2026-04-07 03:13:29', '2026-04-07 03:13:29'),
(86, 9, '2026-04-02', '14:13:00', NULL, 'present', 0.00, '2026-04-07 03:13:29', '2026-04-07 03:13:29'),
(93, 9, '2026-04-07', '09:30:00', '14:23:00', 'present', 4.88, '2026-04-07 04:20:23', '2026-04-07 04:27:14'),
(94, 7, '2026-04-06', '16:10:00', NULL, 'present', 0.00, '2026-04-07 05:10:51', '2026-04-07 05:10:51'),
(95, 8, '2026-04-06', '16:10:00', NULL, 'present', 0.00, '2026-04-07 05:10:51', '2026-04-07 05:10:51'),
(96, 9, '2026-04-06', '09:15:00', '18:12:00', 'present', 8.95, '2026-04-07 05:10:51', '2026-04-07 05:10:51');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-admin@admin.com|127.0.0.1', 'i:1;', 1775540779),
('laravel-cache-admin@admin.com|127.0.0.1:timer', 'i:1775540779;', 1775540779),
('laravel-cache-admin@example.com|127.0.0.1', 'i:1;', 1774847967),
('laravel-cache-admin@example.com|127.0.0.1:timer', 'i:1774847967;', 1774847967),
('laravel-cache-admin@gmail.com|127.0.0.1', 'i:1;', 1774946739),
('laravel-cache-admin@gmail.com|127.0.0.1:timer', 'i:1774946739;', 1774946739),
('laravel-cache-prakash@wm.com|127.0.0.1', 'i:2;', 1775557913),
('laravel-cache-prakash@wm.com|127.0.0.1:timer', 'i:1775557913;', 1775557913),
('laravel-cache-radmin@gmail.com|127.0.0.1', 'i:1;', 1774695774),
('laravel-cache-radmin@gmail.com|127.0.0.1:timer', 'i:1774695774;', 1774695774),
('laravel-cache-superadmin1@gmail.com|127.0.0.1', 'i:1;', 1774696245),
('laravel-cache-superadmin1@gmail.com|127.0.0.1:timer', 'i:1774696245;', 1774696245);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_code` int(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile_number` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(255) NOT NULL DEFAULT 'male',
  `employee_type` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `aadhaar_number` varchar(255) DEFAULT NULL,
  `pan_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `leave` decimal(8,2) NOT NULL DEFAULT 0.00,
  `photo` varchar(255) DEFAULT NULL,
  `pf` tinyint(1) NOT NULL DEFAULT 0,
  `pf_number` varchar(255) DEFAULT NULL,
  `esi` tinyint(1) NOT NULL DEFAULT 0,
  `esi_number` varchar(255) DEFAULT NULL,
  `insurance` tinyint(1) NOT NULL DEFAULT 0,
  `insurance_provider` varchar(255) DEFAULT NULL,
  `insurance_policy_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `basic_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `hra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `conveyance_allowance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `medical_allowance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other_allowance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_code`, `name`, `email`, `mobile_number`, `role`, `department`, `designation`, `date_of_joining`, `date_of_birth`, `gender`, `employee_type`, `username`, `password`, `aadhaar_number`, `pan_number`, `address`, `time_in`, `time_out`, `leave`, `photo`, `pf`, `pf_number`, `esi`, `esi_number`, `insurance`, `insurance_provider`, `insurance_policy_number`, `bank_name`, `account_number`, `ifsc_code`, `basic_salary`, `hra`, `conveyance_allowance`, `medical_allowance`, `other_allowance`, `created_at`, `updated_at`) VALUES
(7, 0, 'Ranjit Singh Shaktawat', 'ranjit.warrgyizmorsch@gmail.com', '7775896590', 'employee', 'web_development', 'Backend Developer (Laravel / Node.js)', '2026-04-01', '2004-08-24', 'male', 'permanent', 'ranjit.warrgyizmorsch@gmail.com', '$2y$12$MPPykOhhvr2knVhvSfLbA.G6n8VEC0g8hTxgtAHOjEfnnaKSmlFh2', '456978523598', 'FGDRQ4545R', 'Udaipur', '09:00:00', '19:00:00', 1.50, 'uploads/employees/1775127788_Warr.png', 0, NULL, 0, NULL, 0, NULL, NULL, 'HDFC', '678456789654', 'HDFC0000003', 20000.00, 0.00, 0.00, 0.00, 0.00, '2026-04-02 05:33:08', '2026-04-07 01:59:15'),
(8, 0, 'Aaditya', 'aadi@gmail.com', '7775896540', 'team_leader', 'hr', 'hr_manager', '2026-04-01', '2003-05-01', 'male', 'permanent', 'ranjit.warrgyizmorsch@gmail.com', 'aadi2003', '456978523598', 'FGDRQ4545T', 'neemuch', '09:00:00', '19:00:00', 1.50, 'uploads/employees/1775191306_Animal 2.webp', 1, '4520678', 1, '23501', 1, 'life insurance', '45269787', 'HDFC', '678456789654', 'HDFC0000003', 15000.00, 2000.00, 2000.00, 2000.00, 1000.00, '2026-04-02 23:11:46', '2026-04-02 23:11:46'),
(9, 34, 'Prakash Sharma', 'prakash@wm.com', '9664100138', 'business_operation_head', 'administration', 'Chief Technology Officer (CTO)', '2025-12-04', '1992-10-17', 'male', NULL, NULL, '$2y$12$punai/ZbkztojwVeiok46.b8YBp3W4B14oq7od034JytGPcCq4onW', '433064458086', 'EGMPS7128P', 'Nakoda Nagar, Udaipur', '09:30:00', '18:00:00', 1.50, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, 'Kotak Bank', '4587596068', 'KKBK00125', 50000.00, 0.00, 0.00, 0.00, 0.00, '2026-04-07 02:04:44', '2026-04-07 03:10:49');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`id`, `title`, `date`, `created_at`, `updated_at`) VALUES
(5, 'LABOUR DAY', '2026-05-01', '2026-03-31 02:25:44', '2026-03-31 02:25:44'),
(6, 'BAKRA EID', '2026-05-27', '2026-03-31 02:26:06', '2026-03-31 02:26:06'),
(7, 'INDEPENDENCE DAY', '2026-08-15', '2026-03-31 02:26:47', '2026-03-31 02:26:47'),
(8, 'RAKSHA BANDHAN', '2026-08-28', '2026-03-31 02:27:25', '2026-03-31 02:27:25'),
(9, 'GANDHI JYANTI', '2026-10-02', '2026-03-31 02:27:47', '2026-03-31 02:27:47'),
(10, 'DIWALI', '2026-11-08', '2026-03-31 02:28:11', '2026-03-31 02:28:11'),
(11, 'DIWALI (GOVARDHAN PUJA)', '2026-11-09', '2026-03-31 02:28:42', '2026-03-31 02:28:42'),
(12, 'CHRISTMAS DAY', '2026-12-25', '2026-03-31 02:29:26', '2026-03-31 02:29:26'),
(15, 'NEW YEAR\'S DAY', '2026-01-01', '2026-04-02 06:07:07', '2026-04-02 06:07:07'),
(16, 'REPUBLIC DAY', '2026-01-26', '2026-04-02 06:07:34', '2026-04-02 06:07:34'),
(17, 'HOLI', '2026-03-04', '2026-04-02 06:07:51', '2026-04-02 06:07:51'),
(18, 'EID-UL-FITAR (TENTATIVE)', '2026-03-21', '2026-04-02 06:08:44', '2026-04-02 06:08:44');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_allotments`
--

CREATE TABLE `leave_allotments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `month` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `leave_count` decimal(8,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_allotments`
--

INSERT INTO `leave_allotments` (`id`, `employee_id`, `month`, `year`, `leave_count`, `created_at`, `updated_at`) VALUES
(13, 7, '04', '2026', 1.50, '2026-04-03 00:48:32', '2026-04-03 00:48:32'),
(14, 8, '04', '2026', 1.50, '2026-04-03 00:48:32', '2026-04-03 00:48:32'),
(15, 7, '03', '2026', 1.50, '2026-04-07 00:22:12', '2026-04-07 00:22:12'),
(16, 8, '03', '2026', 1.50, '2026-04-07 00:22:12', '2026-04-07 00:22:12');

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

CREATE TABLE `leave_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type` varchar(255) NOT NULL,
  `leave_category` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `total_days` double NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_applications`
--

INSERT INTO `leave_applications` (`id`, `employee_id`, `leave_type`, `leave_category`, `start_date`, `end_date`, `start_time`, `end_time`, `reason`, `message`, `status`, `total_days`, `created_at`, `updated_at`) VALUES
(5, 7, 'Sick Leave', 'half', '2026-04-03', '2026-04-04', NULL, NULL, 'Health Issue', 'health', 'approved', 0.5, '2026-04-03 00:50:12', '2026-04-03 00:50:24'),
(6, 8, 'Gatepass Leave', 'gatepass', '2026-04-03', '2026-04-03', '17:00:00', '18:00:00', 'Emergency', NULL, 'approved', 0, '2026-04-03 01:22:57', '2026-04-03 01:23:14'),
(7, 8, 'Paid Leave', 'full', '2026-04-06', '2026-04-07', NULL, NULL, 'going outside city', NULL, 'on_hold', 1, '2026-04-03 01:25:52', '2026-04-03 01:28:13'),
(8, 7, 'Gatepass Leave', 'gatepass', '2026-04-03', '2026-04-03', '17:15:00', '18:15:00', 'some personal reason', NULL, 'approved', 0, '2026-04-03 01:41:37', '2026-04-03 01:41:50'),
(9, 7, 'Paid Leave', 'full', '2026-04-08', '2026-04-09', NULL, NULL, 'urgent leave', 'sdf', 'approved', 1, '2026-04-07 00:22:59', '2026-04-07 00:23:13');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_03_20_000000_create_employees_table', 1),
(5, '2026_03_20_063252_create_holidays_table', 1),
(6, '2026_03_24_083553_create_attendances_table', 1),
(7, '2026_03_24_083603_create_payrolls_table', 1),
(8, '2026_03_28_045414_add_details_to_employees_table', 2),
(9, '2026_03_31_055403_create_leave_allotments_table', 3),
(10, '2026_03_31_120000_create_leave_applications_table', 4),
(11, '2026_03_31_122000_add_time_to_leave_applications', 5),
(12, '2026_04_01_000000_add_salary_loss_to_payrolls_table', 6),
(13, '2026_04_02_000000_update_leave_to_decimal', 6),
(14, '2026_04_02_153000_add_missing_columns_to_payrolls_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payrolls`
--

CREATE TABLE `payrolls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `month` varchar(255) NOT NULL,
  `payable_days` int(11) NOT NULL DEFAULT 0,
  `unpaid_days` decimal(8,2) NOT NULL DEFAULT 0.00,
  `salary_loss` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gross_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `hra` decimal(12,2) NOT NULL DEFAULT 0.00,
  `conveyance_allowance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `medical_allowance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `other_allowance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `pf_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `esi_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `other_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `monthly_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payrolls`
--

INSERT INTO `payrolls` (`id`, `employee_id`, `month`, `payable_days`, `unpaid_days`, `salary_loss`, `gross_salary`, `basic_salary`, `hra`, `conveyance_allowance`, `medical_allowance`, `other_allowance`, `deductions`, `pf_deduction`, `esi_deduction`, `other_deduction`, `net_salary`, `monthly_salary`, `status`, `payment_date`, `remarks`, `created_at`, `updated_at`) VALUES
(25, 9, '2026-04', 3, 0.00, 0.00, 181.25, 181.25, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 181.25, 0.00, 'pending', NULL, NULL, '2026-04-07 04:56:51', '2026-04-07 04:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('9gDyk682oU9SfcyCiC19nVkYUM9gB1krhtUIAsJt', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTThkUjNOMFBkNThTSzIzYjgxdTBZd3NvNGVRSTluZElKV0pPaWlFRSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wYXlyb2xsL2F0dGVuZGFuY2UiO3M6NToicm91dGUiO3M6MTg6InBheXJvbGwuYXR0ZW5kYW5jZSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1775566158);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Ranjit Singh Shaktawat', 'ranjit.warrgyizmorsch@gmail.com', NULL, '$2y$12$VWCgohh.Mtwi6mvhC5GPp.E9csKSee8ie1WVSGGsGjza0uCiaLrMa', 'nz58RxnywQRBjRm9JStNSJdjjeTf0kfNRt3nZQLmJXMiJ716Yuo8em7VUEXQ', '2026-03-27 23:13:01', '2026-03-27 23:13:01'),
(2, 'Admin', 'admin@admin.com', NULL, '$2y$12$Bdln0M.OzzJTvwmcEm/QBeeSoSewtmtptZrwQT4YlGEiiwrERM6iu', NULL, '2026-03-27 23:36:08', '2026-03-27 23:36:08'),
(3, 'Prakash Sharma', 'prakash@wm.com', NULL, '$2y$12$quxUUxuViQkOwMQZW/Py6uhJw3hfp2l5O2auk3dXOk69m62z.vOQ.', NULL, '2026-04-07 05:05:30', '2026-04-07 05:05:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_employee_id_index` (`employee_id`),
  ADD KEY `attendances_attendance_date_index` (`attendance_date`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_allotments`
--
ALTER TABLE `leave_allotments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `leave_allotments_employee_id_month_year_unique` (`employee_id`,`month`,`year`);

--
-- Indexes for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_applications_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payrolls_employee_id_index` (`employee_id`),
  ADD KEY `payrolls_month_index` (`month`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_allotments`
--
ALTER TABLE `leave_allotments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_allotments`
--
ALTER TABLE `leave_allotments`
  ADD CONSTRAINT `leave_allotments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD CONSTRAINT `leave_applications_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD CONSTRAINT `payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

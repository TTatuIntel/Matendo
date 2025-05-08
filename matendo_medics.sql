-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 08:55 AM
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
-- Database: `matendo_medics`
--

-- --------------------------------------------------------

--
-- Table structure for table `facility_hiring_requests`
--

CREATE TABLE `facility_hiring_requests` (
  `id` int(11) NOT NULL,
  `facility_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `coordinates` varchar(100) DEFAULT NULL,
  `facility_type` text NOT NULL,
  `positions` text NOT NULL,
  `employment_type` text NOT NULL,
  `shift_type` text NOT NULL,
  `staff_number` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `job_requirement_option` varchar(50) DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `job_description` text DEFAULT NULL,
  `job_description_file` varchar(255) DEFAULT NULL,
  `reference_number` varchar(20) NOT NULL,
  `submission_date` datetime NOT NULL,
  `csrf_token` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facility_hiring_requests`
--

INSERT INTO `facility_hiring_requests` (`id`, `facility_name`, `contact_person`, `email`, `phone`, `coordinates`, `facility_type`, `positions`, `employment_type`, `shift_type`, `staff_number`, `start_date`, `job_requirement_option`, `qualifications`, `experience`, `job_description`, `job_description_file`, `reference_number`, `submission_date`, `csrf_token`, `created_at`) VALUES
(1, 'Medical ', 'Cheif Manager', 'steve@gmail.com', '0755897643', '', 'Laboratory', 'Pharmacy Dispensers', 'Part-Time', 'Night Shift', 2, '2025-05-24', 'none', '', '', '', '', 'REQ-795573-XHE', '2025-05-06 04:46:35', '', '2025-05-06 04:46:35'),
(2, 'Steve', 'Steve Steven', 'steve@gmail.com', '0755897643', '', 'Pharmacy', 'Pharmacy Dispensers', 'Full-Time', 'Rotating', 2, '2025-05-31', 'none', '', '', '', '', 'REQ-864536-JKY', '2025-05-06 05:21:04', '', '2025-05-06 05:21:04'),
(3, 'Testing', 'Steve Steven', 'steve@gmail.com', '0755897643', '', 'Clinic', 'Radiographers', 'Short-Term', 'Day Shift', 22, '2025-05-07', 'none', '', '', '', '', 'REQ-071439-WUO', '2025-05-06 05:41:11', '', '2025-05-06 05:41:11'),
(4, 'Steve Accurua testing', 'Steve Steven', 'steve@gmail.com', '0755897643', '', 'Hospital', 'Medical Officers', 'Full-Time', 'Rotating', 4, '2025-05-22', 'none', '', '', '', '', 'REQ-085067-V1V', '2025-05-06 05:58:05', '', '2025-05-06 05:58:05'),
(5, 'Steve Accurua testing', 'Steve Steven', 'steve@gmail.com', '0755897643', '', 'Other', 'Other', 'Short-Term', 'Rotating', 3, '2025-05-23', 'none', '', '', '', '', 'REQ-240109-WOR', '2025-05-06 09:54:00', '', '2025-05-06 09:54:00'),
(6, 'Steve Accurua testing Merge', 'Steve Steven', 'steve@gmail.com', '0755897643', '', 'Clinic', 'Radiographers', 'Full-Time', 'Rotating', 5, '2025-06-01', 'none', '', '', '', '', 'REQ-760806-RAU', '2025-05-07 06:52:40', '', '2025-05-07 06:52:40'),
(7, 'Micheal Testing', 'Steve Steven', 'testing@gmail.com', '0755897643', '', 'Hospital', 'Medical Officers', 'Short-Term', 'Day Shift', 2, '2025-05-16', 'none', '', '', '', '', 'REQ-283241-BQT', '2025-05-07 07:01:23', '', '2025-05-07 07:01:23'),
(8, 'Steve Accurua testing', 'Steve Steven', 'steve@gmail.com', '0755897643', '', 'Pharmacy', 'Medical Officers', 'Part-Time', 'Night Shift', 4, '2025-05-28', 'none', '', '', '', '', 'REQ-705925-MSE', '2025-05-07 08:48:25', '', '2025-05-07 08:48:26');

-- --------------------------------------------------------

--
-- Table structure for table `healthcare_professionals`
--

CREATE TABLE `healthcare_professionals` (
  `id` int(11) NOT NULL,
  `reference_number` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `coordinates` varchar(255) DEFAULT NULL,
  `profession` varchar(255) NOT NULL,
  `other_profession` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `years_experience` int(11) NOT NULL,
  `license_number` varchar(255) DEFAULT NULL,
  `work_type` varchar(255) NOT NULL,
  `shift_type` varchar(255) NOT NULL,
  `preferred_location` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `submission_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resume_data` blob NOT NULL,
  `resume_name` varchar(255) NOT NULL,
  `license_doc_data` blob DEFAULT NULL,
  `license_doc_name` varchar(255) DEFAULT NULL,
  `certifications_data` blob DEFAULT NULL,
  `certifications_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_care_requests`
--

CREATE TABLE `personal_care_requests` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `care_type` text NOT NULL,
  `care_frequency` text NOT NULL,
  `start_date` date NOT NULL,
  `medical_conditions` text DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `reference_number` varchar(20) NOT NULL,
  `submission_date` datetime NOT NULL,
  `csrf_token` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `care_requirements` text DEFAULT NULL,
  `schedule` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `emergency_phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personal_care_requests`
--

INSERT INTO `personal_care_requests` (`id`, `full_name`, `email`, `phone`, `address`, `care_type`, `care_frequency`, `start_date`, `medical_conditions`, `additional_notes`, `reference_number`, `submission_date`, `csrf_token`, `created_at`, `care_requirements`, `schedule`, `medications`, `allergies`, `emergency_contact`, `emergency_phone`) VALUES
(1, 'Steve Steven Testing Personal', 'steve@gmail.com', '0755897643', 'Kampala', 'recovery', '', '0000-00-00', 'testing', '', 'REQ-359240-H3Q', '2025-05-06 05:45:59', '', '2025-05-06 05:45:59', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'SteveSteven', 'steve@gmail.com', '0755897643', 'Kampala', 'elderly', '', '0000-00-00', 'testing bad', NULL, 'REQ-915246-7MM', '2025-05-06 05:55:15', '', '2025-05-06 05:55:15', 'testing', 'Occasional', 'non', 'no', 'Steve Steven', '0755897643'),
(3, 'SteveSteven Personal', 'steve@gmail.com', '0755897643', 'Kampala', 'disability', '', '0000-00-00', 'test', NULL, 'REQ-145452-D6Z', '2025-05-06 05:59:05', '', '2025-05-06 05:59:05', 'test', 'Full-Time', 'test', 'test', 'Mic Mike', '0755897643'),
(4, 'SteveSteven Personal testing ', 'steve@gmail.com', '0755897643', 'Kampala', 'elderly', '', '0000-00-00', 'non', NULL, 'REQ-399955-2D0', '2025-05-07 07:03:19', '', '2025-05-07 07:03:20', 'help', 'Part-Time', 'testing', 'testing', 'Steve Steven', '0755897643');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `created_at`) VALUES
(1, 'testing@gmail.com', '$2y$10$5sHwCQTAyxGwUMT.JN9v7.rrQlL.jC/K5dIUdLetujIQHpxWGTvM6', '2025-05-06 09:54:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `facility_hiring_requests`
--
ALTER TABLE `facility_hiring_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `healthcare_professionals`
--
ALTER TABLE `healthcare_professionals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `personal_care_requests`
--
ALTER TABLE `personal_care_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `facility_hiring_requests`
--
ALTER TABLE `facility_hiring_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `healthcare_professionals`
--
ALTER TABLE `healthcare_professionals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_care_requests`
--
ALTER TABLE `personal_care_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `healthcare_professionals`
--
ALTER TABLE `healthcare_professionals`
  ADD CONSTRAINT `healthcare_professionals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

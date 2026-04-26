-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 04, 2026 at 01:46 PM
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
-- Database: `online_pharmacy`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `medicine_id`, `quantity`, `created_at`) VALUES
(35, 4, 5, 1, '2026-01-02 10:16:44'),
(44, 6, 1, 1, '2026-01-11 10:36:02');

-- --------------------------------------------------------

--
-- Table structure for table `Logs`
--

CREATE TABLE `Logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Logs`
--

INSERT INTO `Logs` (`log_id`, `user_id`, `action`, `details`, `ip_address`, `status`, `created_at`) VALUES
(1, 1, 'login_success', 'User Alice logged in', '127.0.0.1', 'success', '2025-09-11 11:18:58'),
(2, 3, 'rx_approved', 'Prescription rx1.pdf approved', '127.0.0.1', 'success', '2025-09-11 11:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `Medicines`
--

CREATE TABLE `Medicines` (
  `medicine_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(80) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `prescription_needed` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Medicines`
--

INSERT INTO `Medicines` (`medicine_id`, `name`, `description`, `category`, `price`, `stock`, `prescription_needed`, `created_at`, `updated_at`, `is_deleted`) VALUES
(1, 'Paracetamol', 'Pain reliever and fever reducer', 'Analgesic', 2.55, 100, 0, '2025-09-11 11:18:58', '2025-12-27 09:22:23', 0),
(2, 'Amoxicillin', 'Antibiotic for bacterial infections', 'Antibiotic', 8.75, 31, 1, '2025-09-11 11:18:58', '2025-12-03 20:03:11', 0),
(3, 'Cough Syrup', 'Relieves cough and sore throat', 'Cold & Flu', 5.20, 66, 0, '2025-09-11 11:18:58', '2025-12-03 20:14:44', 0),
(4, 'Insulin', 'Used for diabetes treatment', 'Endocrine', 20.50, 27, 1, '2025-09-11 11:18:58', '2025-12-28 10:38:04', 0),
(5, 'Vitamin D', NULL, NULL, 6.90, 69, 0, '2025-12-28 10:47:07', '2026-01-02 10:07:04', 0);

-- --------------------------------------------------------

--
-- Table structure for table `Notifications`
--

CREATE TABLE `Notifications` (
  `notif_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `sent_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Notifications`
--

INSERT INTO `Notifications` (`notif_id`, `user_id`, `order_id`, `type`, `message`, `status`, `sent_at`, `created_at`) VALUES
(1, 1, 1, 'email', 'Your order #1 has been placed.', 'sent', '2025-09-11 11:18:58', '2025-09-11 11:18:58'),
(2, 2, 2, 'sms', 'Your order #2 is ready for pickup.', 'queued', NULL, '2025-09-11 11:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `OrderItems`
--

CREATE TABLE `OrderItems` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `OrderItems`
--

INSERT INTO `OrderItems` (`order_item_id`, `order_id`, `medicine_id`, `quantity`, `subtotal`) VALUES
(1, 1, 1, 2, 5.00),
(2, 1, 3, 1, 5.20),
(3, 2, 2, 1, 8.75),
(4, 5, 4, 3, 60.00),
(5, 5, 2, 1, 8.75),
(6, 5, 2, 1, 8.75),
(7, 5, 2, 1, 8.75),
(8, 5, 2, 1, 8.75),
(9, 5, 2, 1, 8.75),
(10, 5, 3, 1, 5.20),
(11, 5, 2, 1, 8.75),
(12, 5, 4, 3, 60.00),
(13, 5, 2, 1, 8.75),
(14, 5, 2, 1, 8.75),
(15, 5, 2, 1, 8.75),
(16, 5, 2, 1, 8.75),
(17, 5, 2, 1, 8.75),
(18, 5, 3, 1, 5.20),
(19, 5, 2, 1, 8.75),
(20, 5, 4, 3, 60.00),
(21, 5, 2, 1, 8.75),
(22, 5, 2, 1, 8.75),
(23, 5, 2, 1, 8.75),
(24, 5, 2, 1, 8.75),
(25, 5, 2, 1, 8.75),
(26, 5, 3, 1, 5.20),
(27, 5, 2, 1, 8.75),
(28, 5, 4, 3, 60.00),
(29, 5, 2, 1, 8.75),
(30, 5, 2, 1, 8.75),
(31, 5, 2, 1, 8.75),
(32, 5, 2, 1, 8.75),
(33, 5, 2, 1, 8.75),
(34, 5, 3, 1, 5.20),
(35, 5, 2, 1, 8.75),
(36, 5, 4, 3, 60.00),
(37, 5, 2, 1, 8.75),
(38, 5, 2, 1, 8.75),
(39, 5, 2, 1, 8.75),
(40, 5, 2, 1, 8.75),
(41, 5, 2, 1, 8.75),
(42, 5, 3, 1, 5.20),
(43, 5, 2, 1, 8.75),
(44, 5, 4, 3, 60.00),
(45, 5, 2, 1, 8.75),
(46, 5, 2, 1, 8.75),
(47, 5, 2, 1, 8.75),
(48, 5, 2, 1, 8.75),
(49, 5, 2, 1, 8.75),
(50, 5, 3, 1, 5.20),
(51, 5, 2, 1, 8.75),
(52, 5, 4, 3, 60.00),
(53, 5, 2, 1, 8.75),
(54, 5, 2, 1, 8.75),
(55, 5, 2, 1, 8.75),
(56, 5, 2, 1, 8.75),
(57, 5, 2, 1, 8.75),
(58, 5, 3, 1, 5.20),
(59, 5, 2, 1, 8.75),
(60, 5, 4, 3, 60.00),
(61, 5, 2, 1, 8.75),
(62, 5, 2, 1, 8.75),
(63, 5, 2, 1, 8.75),
(64, 5, 2, 1, 8.75),
(65, 5, 2, 1, 8.75),
(66, 5, 3, 1, 5.20),
(67, 5, 2, 1, 8.75),
(68, 6, 2, 1, 8.75),
(69, 7, 2, 1, 8.75),
(70, 7, 3, 1, 5.20),
(71, 8, 2, 1, 8.75),
(72, 9, 4, 1, 20.00),
(73, 10, 2, 1, 8.75),
(74, 11, 2, 1, 8.75),
(75, 11, 3, 1, 5.20),
(76, 11, 4, 1, 20.00),
(77, 12, 2, 1, 8.75),
(78, 12, 3, 1, 5.20),
(79, 12, 4, 1, 20.00),
(80, 13, 2, 1, 8.75),
(81, 13, 3, 1, 5.20),
(82, 14, 2, 3, 26.25),
(83, 14, 3, 1, 5.20),
(84, 15, 3, 3, 15.60),
(85, 15, 2, 1, 8.75),
(86, 16, 3, 1, 5.20),
(87, 22, 2, 2, 17.50),
(88, 23, 4, 1, 20.50),
(91, 33, 2, 1, 8.75),
(92, 34, 3, 1, 5.20);

-- --------------------------------------------------------

--
-- Table structure for table `Orders`
--

CREATE TABLE `Orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL,
  `order_type` varchar(10) NOT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `prescription_file` varchar(255) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Orders`
--

INSERT INTO `Orders` (`order_id`, `user_id`, `order_date`, `status`, `order_type`, `total_price`, `phone`, `address`, `prescription_file`, `approved_by`, `approved_at`) VALUES
(1, 1, '2025-09-11 11:18:58', 'Pending', 'Delivery', 11.25, '5251234', '123 Main St', NULL, NULL, NULL),
(2, 2, '2025-09-11 11:18:58', 'Approved', 'Pickup', 8.75, '5255678', NULL, 'prescriptions/rx1.pdf', 3, '2025-09-11 11:18:58'),
(5, 6, '2025-11-30 16:41:19', 'Pending', 'Online', 117.70, NULL, NULL, NULL, NULL, NULL),
(6, 6, '2025-11-30 16:46:40', 'Pending', 'Online', 8.75, NULL, NULL, NULL, NULL, NULL),
(7, 6, '2025-11-30 16:52:47', 'Pending', 'Online', 13.95, NULL, NULL, NULL, NULL, NULL),
(8, 6, '2025-11-30 16:59:34', 'Pending', 'Online', 8.75, '', '', '', NULL, NULL),
(9, 6, '2025-11-30 16:59:52', 'Pending', 'Online', 20.00, '', '', '', NULL, NULL),
(10, 6, '2025-11-30 17:00:12', 'Pending', 'Online', 8.75, '', '', '', NULL, NULL),
(11, 6, '2025-11-30 17:05:45', 'Pending', 'Online', 33.95, NULL, NULL, NULL, NULL, NULL),
(12, 6, '2025-11-30 17:06:07', 'Pending', 'Online', 33.95, NULL, NULL, NULL, NULL, NULL),
(13, 6, '2025-11-30 17:08:18', 'Pending', 'Online', 13.95, NULL, NULL, NULL, NULL, NULL),
(14, 6, '2025-12-02 21:03:24', 'Pending', 'Online', 31.45, NULL, NULL, NULL, NULL, NULL),
(15, 6, '2025-12-03 20:03:11', 'Pending', 'Online', 24.35, '', '', '', NULL, NULL),
(16, 6, '2025-12-03 20:14:44', 'Pending', 'Online', 5.20, '', '', '', NULL, NULL),
(17, 6, '2025-12-05 14:08:27', 'pending', 'online', 30.40, NULL, NULL, NULL, NULL, NULL),
(18, 6, '2025-12-05 14:08:38', 'pending', 'online', 30.40, NULL, NULL, NULL, NULL, NULL),
(19, 6, '2025-12-06 13:03:14', 'pending', 'online', 30.40, NULL, NULL, NULL, NULL, NULL),
(20, 1, '2026-01-04 17:39:18', 'pending', 'online', 100.00, NULL, NULL, NULL, NULL, NULL),
(21, 6, '2026-01-05 18:39:36', 'pending', 'online', 17.50, NULL, NULL, NULL, NULL, NULL),
(22, 6, '2026-01-05 18:41:37', 'pending', 'online', 17.50, NULL, NULL, NULL, NULL, NULL),
(23, 6, '2026-01-05 18:42:10', 'pending', 'online', 20.50, NULL, NULL, NULL, NULL, NULL),
(33, 6, '2026-01-06 20:24:27', 'pending_prescription', 'Online', 8.75, NULL, NULL, NULL, NULL, NULL),
(34, 6, '2026-01-11 10:06:51', 'pending', 'delivery', 5.20, '12345678', 'Ben10 Avenue , Flacq', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Payments`
--

CREATE TABLE `Payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `method` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Payments`
--

INSERT INTO `Payments` (`payment_id`, `order_id`, `method`, `amount`, `status`, `paid_at`, `created_at`) VALUES
(1, 1, 'COD', 11.25, 'Pending', NULL, '2025-09-11 11:18:58'),
(2, 2, 'Online', 8.75, 'Paid', '2025-09-11 11:18:58', '2025-09-11 11:18:58'),
(3, 34, 'mock_card', 5.20, 'paid', '2026-01-11 07:06:51', '2026-01-11 10:06:51');

-- --------------------------------------------------------

--
-- Table structure for table `Prescriptions`
--

CREATE TABLE `Prescriptions` (
  `prescription_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Prescriptions`
--

INSERT INTO `Prescriptions` (`prescription_id`, `order_id`, `user_id`, `file_path`, `status`, `uploaded_at`) VALUES
(1, 33, 6, 'uploads/prescriptions/1767716667_Screenshot 2026-01-04 at 09.34.22.png', 'approved', '2026-01-06 20:24:27');

-- --------------------------------------------------------

--
-- Table structure for table `Refunds`
--

CREATE TABLE `Refunds` (
  `refund_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `requested_at` datetime DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Refunds`
--

INSERT INTO `Refunds` (`refund_id`, `payment_id`, `amount`, `reason`, `status`, `requested_at`, `processed_at`) VALUES
(1, 2, 8.75, 'Wrong medicine delivered', 'Requested', '2025-09-11 11:18:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`user_id`, `name`, `email`, `password`, `phone`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Alice Doe', 'alice@example.com', 'hashed_password1', '5251234', 'customer', '2025-09-11 11:18:58', '2025-09-11 11:18:58'),
(2, 'Bob Smith', 'bob@example.com', 'hashed_password2', '5255678', 'customer', '2025-09-11 11:18:58', '2025-09-11 11:18:58'),
(3, 'Dr. Jane Pharma', 'jane@pharmacy.com', '$2y$10$s8D3wehGJIzNieGfJE.ta.ukzctBr9rRld8tvzWincmGhI4lSbXsC', '5258765', 'pharmacist', '2025-09-11 11:18:58', '2026-01-03 14:55:38'),
(4, 'Admin User', 'admin@system.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '5259999', 'admin', '2025-09-11 11:18:58', '2025-12-21 10:18:51'),
(5, 'ashmit', 'bomboclat@hotmail.com', '$2y$10$.hGJHkQq6oQMpKHXKKboCebHKeLlvMrfKJzE4kTmRYV/oWtGrSxd2', NULL, 'customer', '2025-11-27 20:15:09', '2025-11-27 20:15:09'),
(6, 'doberman', 'roxy@dober.com', '$2y$10$F2doqcLOC.KaJlvF1y427OS8p7Pw/zJdZPAcu6wLZC6QdMMdFFaEK', NULL, 'customer', '2025-11-28 21:36:09', '2025-11-28 21:36:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `Logs`
--
ALTER TABLE `Logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Medicines`
--
ALTER TABLE `Medicines`
  ADD PRIMARY KEY (`medicine_id`);

--
-- Indexes for table `Notifications`
--
ALTER TABLE `Notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `OrderItems`
--
ALTER TABLE `OrderItems`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `Orders`
--
ALTER TABLE `Orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `Payments`
--
ALTER TABLE `Payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `Prescriptions`
--
ALTER TABLE `Prescriptions`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Refunds`
--
ALTER TABLE `Refunds`
  ADD PRIMARY KEY (`refund_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `Logs`
--
ALTER TABLE `Logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Medicines`
--
ALTER TABLE `Medicines`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Notifications`
--
ALTER TABLE `Notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `OrderItems`
--
ALTER TABLE `OrderItems`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `Orders`
--
ALTER TABLE `Orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `Payments`
--
ALTER TABLE `Payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Prescriptions`
--
ALTER TABLE `Prescriptions`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Refunds`
--
ALTER TABLE `Refunds`
  MODIFY `refund_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`);

--
-- Constraints for table `Logs`
--
ALTER TABLE `Logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`);

--
-- Constraints for table `Notifications`
--
ALTER TABLE `Notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `Orders` (`order_id`);

--
-- Constraints for table `OrderItems`
--
ALTER TABLE `OrderItems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `Orders` (`order_id`),
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `Medicines` (`medicine_id`);

--
-- Constraints for table `Orders`
--
ALTER TABLE `Orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `Users` (`user_id`);

--
-- Constraints for table `Payments`
--
ALTER TABLE `Payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `Orders` (`order_id`);

--
-- Constraints for table `Prescriptions`
--
ALTER TABLE `Prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `Orders` (`order_id`),
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`);

--
-- Constraints for table `Refunds`
--
ALTER TABLE `Refunds`
  ADD CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `Payments` (`payment_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- --------------------------------------------------------
-- Demo login normalisation for coursework testing
START TRANSACTION;
UPDATE `Users` SET `email`='admin@system.com', `password`='$2y$12$4ArtHChWKXib3vQkmJGU.OZXA72sc5vZ0FK5zeengFQ9D0bMJ1qBu', `role`='admin', `name`='Admin User' WHERE `user_id`=4;
UPDATE `Users` SET `email`='jane@pharmacy.com', `password`='$2y$12$.cE5MIXMm9HI4qexN39vJ.KwY94Zz1x4YupY5Zvn4/T9yM.1qZd9u', `role`='pharmacist', `name`='Dr. Jane Pharma' WHERE `user_id`=3;
UPDATE `Users` SET `email`='bomboclat@hotmail.com', `password`='$2y$12$7MEtO441pIrOttS7ppGOIO4eBEqsz/tLbUVIj1wHOB4kHhQzGuZRa', `role`='customer', `name`='Test Customer' WHERE `user_id`=5;
COMMIT;

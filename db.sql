-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2024 at 09:18 AM
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
-- Database: `pharmacy_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `table_affected` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`log_id`, `user_id`, `timestamp`, `action`, `table_affected`, `record_id`) VALUES
(1, 1, '2023-05-01 10:00:00', 'INSERT', 'PRESCRIPTION', 1),
(3, 1, '2024-11-07 21:31:09', 'LOGOUT', 'USER', 1),
(4, 1, '2024-11-07 21:32:04', 'LOGOUT', 'USER', 1),
(5, 3, '2024-11-07 21:32:42', 'LOGOUT', 'USER', 3),
(6, 4, '2024-11-07 21:35:27', 'LOGOUT', 'USER', 4),
(7, 1, '2024-11-07 21:35:42', 'CREATE', 'COUNTER_SALE', 3),
(8, 4, '2024-11-08 14:29:05', 'LOGOUT', 'USER', 4),
(9, 1, '2024-11-08 14:29:10', 'LOGOUT', 'USER', 1),
(10, 3, '2024-11-08 14:29:17', 'LOGOUT', 'USER', 3),
(11, 4, '2024-11-08 14:31:54', 'LOGOUT', 'USER', 4),
(12, 1, '2024-11-08 14:32:41', 'LOGOUT', 'USER', 1),
(13, 4, '2024-11-08 14:40:22', 'LOGOUT', 'USER', 4),
(14, 1, '2024-11-08 14:40:30', 'LOGOUT', 'USER', 1),
(15, 3, '2024-11-08 14:40:38', 'LOGOUT', 'USER', 3),
(16, 4, '2024-11-08 14:43:55', 'LOGOUT', 'USER', 4),
(17, 1, '2024-11-08 14:45:10', 'LOGOUT', 'USER', 1),
(18, 4, '2024-11-08 14:56:10', 'LOGOUT', 'USER', 4),
(19, 1, '2024-11-08 14:56:29', 'LOGOUT', 'USER', 1),
(20, 4, '2024-11-08 15:08:31', 'INSERT', 'DRUG', 3),
(21, 4, '2024-11-08 15:08:40', 'LOGOUT', 'USER', 4),
(22, 1, '2024-11-08 15:09:18', 'INSERT', 'DRUG', 4),
(23, 1, '2024-11-08 15:09:34', 'LOGOUT', 'USER', 1),
(24, 4, '2024-11-08 15:11:21', 'LOGOUT', 'USER', 4),
(25, 4, '2024-11-08 15:19:56', 'LOGOUT', 'USER', 4),
(26, 1, '2024-11-08 15:20:10', 'LOGOUT', 'USER', 1),
(27, 3, '2024-11-08 15:20:17', 'LOGOUT', 'USER', 3),
(28, 4, '2024-11-08 15:20:46', 'LOGOUT', 'USER', 4),
(29, 1, '2024-11-08 15:20:57', 'LOGOUT', 'USER', 1),
(30, 3, '2024-11-08 15:21:02', 'LOGOUT', 'USER', 3),
(31, 4, '2024-11-08 15:29:38', 'LOGOUT', 'USER', 4),
(32, 3, '2024-11-08 15:29:43', 'LOGOUT', 'USER', 3),
(33, 1, '2024-11-08 15:29:50', 'LOGOUT', 'USER', 1),
(34, 4, '2024-11-08 15:43:36', 'LOGOUT', 'USER', 4),
(35, 1, '2024-11-08 15:44:48', 'CREATE', 'COUNTER_SALE', 4),
(36, 4, '2024-11-08 18:50:19', 'LOGOUT', 'USER', 4),
(37, 1, '2024-11-08 18:50:50', 'LOGOUT', 'USER', 1),
(38, 4, '2024-11-08 19:19:12', 'LOGOUT', 'USER', 4),
(39, 1, '2024-11-08 19:19:38', 'LOGOUT', 'USER', 1),
(40, 3, '2024-11-08 19:19:48', 'LOGOUT', 'USER', 3),
(41, 4, '2024-11-08 19:22:31', 'LOGOUT', 'USER', 4),
(42, 3, '2024-11-08 19:22:37', 'LOGOUT', 'USER', 3),
(43, 4, '2024-11-08 19:28:13', 'LOGOUT', 'USER', 4),
(44, 1, '2024-11-08 19:28:23', 'LOGOUT', 'USER', 1),
(45, 4, '2024-11-08 19:43:10', 'LOGOUT', 'USER', 4),
(46, 4, '2024-11-08 19:44:28', 'LOGOUT', 'USER', 4),
(47, 1, '2024-11-08 19:44:32', 'LOGOUT', 'USER', 1),
(48, 4, '2024-11-08 19:45:12', 'LOGOUT', 'USER', 4),
(49, 1, '2024-11-08 19:45:17', 'LOGOUT', 'USER', 1),
(50, 3, '2024-11-08 19:45:24', 'LOGOUT', 'USER', 3),
(51, 4, '2024-11-08 19:46:50', 'LOGOUT', 'USER', 4),
(52, 3, '2024-11-08 19:50:50', 'LOGOUT', 'USER', 3),
(53, 4, '2024-11-08 19:50:57', 'LOGOUT', 'USER', 4),
(54, 4, '2024-11-08 19:52:49', 'LOGOUT', 'USER', 4),
(55, 1, '2024-11-08 19:54:03', 'LOGOUT', 'USER', 1),
(56, 4, '2024-11-08 20:00:34', 'LOGOUT', 'USER', 4),
(57, 4, '2024-11-08 20:08:56', 'LOGOUT', 'USER', 4),
(58, 1, '2024-11-08 20:09:01', 'LOGOUT', 'USER', 1),
(59, 1, '2024-11-08 21:42:08', 'LOGOUT', 'USER', 1),
(60, 4, '2024-11-08 21:46:25', 'LOGOUT', 'USER', 4),
(61, 1, '2024-11-08 21:46:41', 'LOGOUT', 'USER', 1),
(62, 4, '2024-11-08 22:05:09', 'LOGOUT', 'USER', 4),
(63, 1, '2024-11-08 22:05:15', 'LOGOUT', 'USER', 1),
(64, 1, '2024-11-08 22:05:50', 'LOGOUT', 'USER', 1),
(65, 4, '2024-11-08 22:30:55', 'LOGOUT', 'USER', 4),
(66, 4, '2024-11-08 22:33:03', 'LOGOUT', 'USER', 4),
(67, 1, '2024-11-08 22:33:07', 'LOGOUT', 'USER', 1),
(68, 4, '2024-11-08 22:45:33', 'LOGOUT', 'USER', 4),
(69, 1, '2024-11-08 22:45:56', 'LOGOUT', 'USER', 1),
(70, 4, '2024-11-08 22:54:56', 'LOGOUT', 'USER', 4),
(71, 1, '2024-11-08 22:55:22', 'LOGOUT', 'USER', 1),
(72, 4, '2024-11-08 23:16:08', 'LOGOUT', 'USER', 4),
(73, 4, '2024-11-09 14:12:23', 'LOGOUT', 'USER', 4),
(74, 4, '2024-11-09 14:12:49', 'LOGOUT', 'USER', 4),
(75, 1, '2024-11-09 14:12:53', 'LOGOUT', 'USER', 1),
(76, 3, '2024-11-09 14:12:58', 'LOGOUT', 'USER', 3);

-- --------------------------------------------------------

--
-- Table structure for table `counter_sale`
--

CREATE TABLE `counter_sale` (
  `sale_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counter_sale`
--

INSERT INTO `counter_sale` (`sale_id`, `customer_id`, `sale_date`, `total_amount`, `user_id`) VALUES
(3, NULL, '2024-11-07', 2.50, 1),
(4, NULL, '2024-11-08', 40.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `counter_sale_item`
--

CREATE TABLE `counter_sale_item` (
  `sale_item_id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `stock_item_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counter_sale_item`
--

INSERT INTO `counter_sale_item` (`sale_item_id`, `sale_id`, `stock_item_id`, `quantity`, `unit_price`) VALUES
(4, 4, 3, 2, 20.00);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_info` varchar(200) DEFAULT NULL,
  `registration_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `contact_info`, `registration_date`) VALUES
(1, 'John Doe', 'john@email.com', '2023-01-15'),
(2, 'Jane Smith', 'jane@email.com', '2023-02-20');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `doctor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `contact_info` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`doctor_id`, `name`, `specialization`, `contact_info`) VALUES
(1, 'Dr. Brown', 'General Practitioner', 'dr.brown@hospital.com'),
(2, 'Dr. White', 'Cardiologist', 'dr.white@hospital.com');

-- --------------------------------------------------------

--
-- Table structure for table `drug`
--

CREATE TABLE `drug` (
  `drug_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `dosage_form` varchar(50) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug`
--

INSERT INTO `drug` (`drug_id`, `name`, `description`, `dosage_form`, `category_id`, `supplier_id`) VALUES
(3, 'Napa', 'fever ', 'Tablet', 2, NULL),
(4, 'NapaEXT', 'fever', 'Tablet', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `drug_category`
--

CREATE TABLE `drug_category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug_category`
--

INSERT INTO `drug_category` (`category_id`, `name`, `description`) VALUES
(1, 'Antibiotics', 'Medicines that inhibit the growth of or destroy microorganisms'),
(2, 'Painkillers', 'Medicines that relieve pain');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `paid_status` enum('unpaid','paid') DEFAULT 'unpaid',
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `supplier_id`, `invoice_date`, `total_amount`, `status`, `user_id`, `paid_status`, `amount`) VALUES
(8, 3, '2024-11-16', 10000.00, 'Paid', 1, 'unpaid', NULL),
(9, 2, '2024-11-08', 10000.00, 'Pending', 1, 'unpaid', NULL),
(12, NULL, '2024-11-08', 100.00, 'Pending', 4, 'unpaid', NULL),
(13, 2, '2024-11-23', 2500.00, 'Paid', 1, 'unpaid', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_item`
--

CREATE TABLE `invoice_item` (
  `invoice_item_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `stock_item_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `prescription_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `prescription_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`prescription_id`, `customer_id`, `doctor_id`, `prescription_date`, `status`, `user_id`) VALUES
(1, 1, 1, '2023-05-01', 'Filled', 1),
(2, 2, 2, '2023-05-02', 'Pending', 1);

-- --------------------------------------------------------

--
-- Table structure for table `prescription_item`
--

CREATE TABLE `prescription_item` (
  `prescription_item_id` int(11) NOT NULL,
  `prescription_id` int(11) DEFAULT NULL,
  `drug_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `dosage_instructions` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_item`
--

CREATE TABLE `stock_item` (
  `stock_item_id` int(11) NOT NULL,
  `drug_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_item`
--

INSERT INTO `stock_item` (`stock_item_id`, `drug_id`, `supplier_id`, `quantity`, `expiry_date`, `unit_price`, `user_id`) VALUES
(3, 3, 2, 8, '2026-06-30', 20.00, 4),
(4, 4, 2, 10, '2024-11-22', 20.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_info` varchar(200) DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `name`, `contact_info`, `payment_terms`, `user_id`) VALUES
(2, 'SquarePharma', 'iftiSquare@medisupply.com', 'Monthly', 4),
(3, 'AristroPharma', 'aristro@pharma.com', 'Bi-Weekly', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `supplies`
--

CREATE TABLE `supplies` (
  `supply_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `supply_date` date DEFAULT NULL,
  `status` enum('pending','accepted') DEFAULT 'pending',
  `total_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplies`
--

INSERT INTO `supplies` (`supply_id`, `supplier_id`, `supply_date`, `status`, `total_amount`) VALUES
(1, NULL, '2024-11-08', 'pending', NULL),
(2, NULL, '2024-11-08', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `supply_items`
--

CREATE TABLE `supply_items` (
  `supply_item_id` int(11) NOT NULL,
  `supply_id` int(11) DEFAULT NULL,
  `drug_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password_hash`, `role`, `last_login`) VALUES
(1, 'admin', 'admin', 'Administrator', '2024-11-09 14:12:51'),
(3, 'saima', 'saima123', 'Cashier', '2024-11-09 14:12:56'),
(4, 'ifti_Square', 'ifti123', 'Supplier', '2024-11-09 14:06:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `counter_sale`
--
ALTER TABLE `counter_sale`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `counter_sale_item`
--
ALTER TABLE `counter_sale_item`
  ADD PRIMARY KEY (`sale_item_id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `stock_item_id` (`stock_item_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`doctor_id`);

--
-- Indexes for table `drug`
--
ALTER TABLE `drug`
  ADD PRIMARY KEY (`drug_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `drug_category`
--
ALTER TABLE `drug_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `invoice_item`
--
ALTER TABLE `invoice_item`
  ADD PRIMARY KEY (`invoice_item_id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `stock_item_id` (`stock_item_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `prescription_item`
--
ALTER TABLE `prescription_item`
  ADD PRIMARY KEY (`prescription_item_id`),
  ADD KEY `prescription_id` (`prescription_id`),
  ADD KEY `drug_id` (`drug_id`);

--
-- Indexes for table `stock_item`
--
ALTER TABLE `stock_item`
  ADD PRIMARY KEY (`stock_item_id`),
  ADD KEY `drug_id` (`drug_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `supplies`
--
ALTER TABLE `supplies`
  ADD PRIMARY KEY (`supply_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `supply_items`
--
ALTER TABLE `supply_items`
  ADD PRIMARY KEY (`supply_item_id`),
  ADD KEY `supply_id` (`supply_id`),
  ADD KEY `drug_id` (`drug_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `counter_sale`
--
ALTER TABLE `counter_sale`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `counter_sale_item`
--
ALTER TABLE `counter_sale_item`
  MODIFY `sale_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `drug`
--
ALTER TABLE `drug`
  MODIFY `drug_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `drug_category`
--
ALTER TABLE `drug_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `invoice_item`
--
ALTER TABLE `invoice_item`
  MODIFY `invoice_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `prescription`
--
ALTER TABLE `prescription`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `prescription_item`
--
ALTER TABLE `prescription_item`
  MODIFY `prescription_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stock_item`
--
ALTER TABLE `stock_item`
  MODIFY `stock_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `supplies`
--
ALTER TABLE `supplies`
  MODIFY `supply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `supply_items`
--
ALTER TABLE `supply_items`
  MODIFY `supply_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `counter_sale`
--
ALTER TABLE `counter_sale`
  ADD CONSTRAINT `counter_sale_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `counter_sale_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `counter_sale_item`
--
ALTER TABLE `counter_sale_item`
  ADD CONSTRAINT `counter_sale_item_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `counter_sale` (`sale_id`),
  ADD CONSTRAINT `counter_sale_item_ibfk_2` FOREIGN KEY (`stock_item_id`) REFERENCES `stock_item` (`stock_item_id`);

--
-- Constraints for table `drug`
--
ALTER TABLE `drug`
  ADD CONSTRAINT `drug_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `drug_category` (`category_id`);

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`),
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `invoice_item`
--
ALTER TABLE `invoice_item`
  ADD CONSTRAINT `invoice_item_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`),
  ADD CONSTRAINT `invoice_item_ibfk_2` FOREIGN KEY (`stock_item_id`) REFERENCES `stock_item` (`stock_item_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`),
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `prescription_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `prescription_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`doctor_id`),
  ADD CONSTRAINT `prescription_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `prescription_item`
--
ALTER TABLE `prescription_item`
  ADD CONSTRAINT `prescription_item_ibfk_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescription` (`prescription_id`),
  ADD CONSTRAINT `prescription_item_ibfk_2` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`drug_id`);

--
-- Constraints for table `stock_item`
--
ALTER TABLE `stock_item`
  ADD CONSTRAINT `stock_item_ibfk_1` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`drug_id`),
  ADD CONSTRAINT `stock_item_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`),
  ADD CONSTRAINT `stock_item_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `supplier`
--
ALTER TABLE `supplier`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `supplies`
--
ALTER TABLE `supplies`
  ADD CONSTRAINT `supplies_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`);

--
-- Constraints for table `supply_items`
--
ALTER TABLE `supply_items`
  ADD CONSTRAINT `supply_items_ibfk_1` FOREIGN KEY (`supply_id`) REFERENCES `supplies` (`supply_id`),
  ADD CONSTRAINT `supply_items_ibfk_2` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`drug_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2024 at 01:26 PM
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
(102, 15, '2024-11-09 20:53:25', 'LOGOUT', 'USER', 15),
(103, 11, '2024-11-09 20:53:30', 'LOGOUT', 'USER', 11),
(104, 14, '2024-11-09 20:53:34', 'LOGOUT', 'USER', 14),
(105, 13, '2024-11-09 20:53:40', 'LOGOUT', 'USER', 13),
(106, 11, '2024-11-09 20:54:22', 'LOGOUT', 'USER', 11),
(107, 15, '2024-11-09 20:56:32', 'LOGOUT', 'USER', 15),
(108, 11, '2024-11-09 21:06:33', 'UPDATE', 'DRUG', 5),
(109, 11, '2024-11-09 21:06:44', 'UPDATE', 'DRUG', 5),
(110, 11, '2024-11-09 21:07:03', 'INSERT', 'DRUG', 6),
(111, 11, '2024-11-09 21:07:40', 'CREATE', 'COUNTER_SALE', 5),
(112, 11, '2024-11-09 21:08:02', 'UPDATE', 'DRUG', 5),
(113, 11, '2024-11-09 21:08:54', 'UPDATE', 'DRUG', 6),
(114, 11, '2024-11-09 21:09:04', 'UPDATE', 'DRUG', 6),
(115, 11, '2024-11-09 21:09:25', 'UPDATE', 'DRUG', 6),
(116, 11, '2024-11-09 21:10:30', 'CREATE', 'COUNTER_SALE', 6),
(117, 11, '2024-11-09 21:10:42', 'UPDATE', 'DRUG', 6),
(118, 11, '2024-11-09 21:17:48', 'LOGOUT', 'USER', 11),
(119, 15, '2024-11-09 21:18:07', 'CREATE', 'COUNTER_SALE', 7),
(120, 15, '2024-11-09 21:20:01', 'CREATE', 'COUNTER_SALE', 9),
(121, 15, '2024-11-09 21:20:11', 'LOGOUT', 'USER', 15),
(122, 11, '2024-11-09 21:31:24', 'LOGOUT', 'USER', 11),
(123, 11, '2024-11-09 21:31:40', 'LOGOUT', 'USER', 11),
(124, 15, '2024-11-09 21:31:56', 'LOGOUT', 'USER', 15),
(125, 11, '2024-11-09 21:32:39', 'LOGOUT', 'USER', 11),
(126, 11, '2024-11-09 21:39:55', 'UPDATE', 'DRUG', 6),
(127, 11, '2024-11-09 21:40:29', 'INSERT', 'DRUG', 7),
(128, 11, '2024-11-09 21:44:29', 'INSERT', 'DRUG', 8),
(129, 11, '2024-11-09 21:46:45', 'INSERT', 'DRUG', 9),
(130, 11, '2024-11-09 21:47:29', 'LOGOUT', 'USER', 11),
(131, 13, '2024-11-09 21:52:48', 'LOGOUT', 'USER', 13),
(132, 11, '2024-11-09 21:52:57', 'LOGOUT', 'USER', 11),
(133, 13, '2024-11-09 21:53:30', 'LOGOUT', 'USER', 13),
(134, 11, '2024-11-09 21:53:53', 'INSERT', 'DRUG', 10),
(135, 11, '2024-11-09 22:07:07', 'LOGOUT', 'USER', 11),
(136, 13, '2024-11-09 22:07:34', 'INSERT', 'DRUG', 11),
(137, 13, '2024-11-09 22:07:50', 'LOGOUT', 'USER', 13),
(138, 11, '2024-11-09 22:08:51', 'LOGOUT', 'USER', 11),
(139, 13, '2024-11-09 22:34:03', 'INSERT', 'DRUG', 12),
(140, 13, '2024-11-09 22:39:50', 'LOGOUT', 'USER', 13),
(141, 11, '2024-11-09 22:40:24', 'INSERT', 'DRUG', 13),
(142, 11, '2024-11-09 22:40:57', 'INSERT', 'DRUG', 14),
(143, 11, '2024-11-09 22:41:02', 'LOGOUT', 'USER', 11),
(144, 13, '2024-11-09 22:41:12', 'LOGOUT', 'USER', 13),
(145, 14, '2024-11-09 22:41:44', 'INSERT', 'DRUG', 15),
(146, 14, '2024-11-09 22:50:25', 'LOGOUT', 'USER', 14),
(147, 11, '2024-11-09 22:50:37', 'LOGOUT', 'USER', 11),
(148, 13, '2024-11-09 22:53:49', 'LOGOUT', 'USER', 13),
(149, 11, '2024-11-09 22:53:58', 'LOGOUT', 'USER', 11),
(150, 13, '2024-11-09 22:54:14', 'LOGOUT', 'USER', 13),
(151, 11, '2024-11-09 22:54:23', 'LOGOUT', 'USER', 11),
(152, 13, '2024-11-09 23:04:15', 'LOGOUT', 'USER', 13),
(153, 13, '2024-11-09 23:05:34', 'LOGOUT', 'USER', 13),
(154, 14, '2024-11-09 23:05:45', 'LOGOUT', 'USER', 14),
(155, 11, '2024-11-09 23:06:13', 'LOGOUT', 'USER', 11),
(156, 11, '2024-11-09 23:09:13', 'LOGOUT', 'USER', 11),
(157, 11, '2024-11-09 23:26:29', 'LOGOUT', 'USER', 11),
(158, 13, '2024-11-09 23:31:46', 'UPDATE', 'DRUG', 12),
(159, 13, '2024-11-09 23:31:52', 'LOGOUT', 'USER', 13),
(160, 11, '2024-11-09 23:32:51', 'UPDATE', 'DRUG', 12),
(161, 11, '2024-11-09 23:33:15', 'LOGOUT', 'USER', 11),
(162, 13, '2024-11-09 23:34:10', 'LOGOUT', 'USER', 13),
(163, 11, '2024-11-09 23:36:28', 'UPDATE', 'DRUG', 12),
(164, 11, '2024-11-09 23:36:32', 'LOGOUT', 'USER', 11),
(165, 13, '2024-11-09 23:48:14', 'LOGOUT', 'USER', 13),
(166, 11, '2024-11-09 23:48:33', 'LOGOUT', 'USER', 11),
(167, 13, '2024-11-09 23:50:02', 'LOGOUT', 'USER', 13),
(168, 11, '2024-11-11 13:01:37', 'INSERT', 'DRUG', 16),
(169, 11, '2024-11-11 13:02:33', 'LOGOUT', 'USER', 11),
(170, 15, '2024-11-11 13:03:45', 'LOGOUT', 'USER', 15),
(171, 13, '2024-11-11 13:04:20', 'LOGOUT', 'USER', 13),
(172, 11, '2024-11-11 22:33:24', 'CREATE', 'COUNTER_SALE', 10),
(173, 11, '2024-11-11 22:35:22', 'LOGOUT', 'USER', 11),
(174, 15, '2024-11-11 22:35:39', 'LOGOUT', 'USER', 15),
(175, 13, '2024-11-11 22:36:28', 'LOGOUT', 'USER', 13),
(176, 14, '2024-11-11 22:38:33', 'LOGOUT', 'USER', 14),
(177, 11, '2024-11-11 22:45:53', 'LOGOUT', 'USER', 11),
(178, 13, '2024-11-11 22:46:19', 'LOGOUT', 'USER', 13),
(179, 15, '2024-11-11 22:47:40', 'LOGOUT', 'USER', 15),
(180, 11, '2024-11-12 08:27:22', 'LOGOUT', 'USER', 11),
(181, 15, '2024-11-12 08:27:38', 'LOGOUT', 'USER', 15),
(182, 13, '2024-11-12 08:28:02', 'UPDATE', 'DRUG', 12),
(183, 13, '2024-11-12 08:28:17', 'UPDATE', 'DRUG', 12),
(184, 13, '2024-11-12 08:30:56', 'LOGOUT', 'USER', 13),
(185, 15, '2024-11-12 08:32:15', 'CREATE', 'COUNTER_SALE', 12),
(186, 15, '2024-11-12 08:32:56', 'LOGOUT', 'USER', 15),
(187, 11, '2024-11-12 08:38:25', 'LOGOUT', 'USER', 11),
(188, 11, '2024-11-12 08:39:22', 'LOGOUT', 'USER', 11),
(189, 11, '2024-11-12 08:40:11', 'LOGOUT', 'USER', 11),
(190, 13, '2024-11-12 08:40:30', 'LOGOUT', 'USER', 13),
(191, 15, '2024-11-12 08:55:22', 'LOGOUT', 'USER', 15),
(192, 11, '2024-11-12 08:59:02', 'LOGOUT', 'USER', 11),
(194, 11, '2024-11-12 09:00:55', 'LOGOUT', 'USER', 11),
(195, 15, '2024-11-12 09:01:09', 'LOGOUT', 'USER', 15),
(196, 15, '2024-11-12 09:01:59', 'LOGOUT', 'USER', 15),
(197, 11, '2024-11-12 09:02:43', 'LOGOUT', 'USER', 11),
(199, 11, '2024-11-12 09:03:49', 'UPDATE', 'DRUG', 16),
(200, 11, '2024-11-12 09:04:07', 'LOGOUT', 'USER', 11),
(201, 13, '2024-11-12 09:04:40', 'LOGOUT', 'USER', 13),
(202, 11, '2024-11-12 09:05:21', 'LOGOUT', 'USER', 11),
(203, 15, '2024-11-12 09:06:00', 'LOGOUT', 'USER', 15),
(204, 15, '2024-11-12 09:06:19', 'CREATE', 'COUNTER_SALE', 14),
(205, 15, '2024-11-12 09:08:24', 'LOGOUT', 'USER', 15),
(206, 11, '2024-11-12 09:10:16', 'LOGOUT', 'USER', 11),
(208, 11, '2024-11-12 09:12:27', 'LOGOUT', 'USER', 11),
(209, 11, '2024-11-12 09:19:26', 'LOGOUT', 'USER', 11),
(210, 11, '2024-11-12 17:08:28', 'LOGOUT', 'USER', 11),
(211, 18, '2024-11-12 17:08:44', 'LOGOUT', 'USER', 18),
(212, 18, '2024-11-12 17:20:20', 'LOGOUT', 'USER', 18),
(213, 11, '2024-11-12 17:20:27', 'LOGOUT', 'USER', 11),
(214, 11, '2024-11-12 17:20:37', 'LOGOUT', 'USER', 11),
(215, 15, '2024-11-12 17:21:05', 'LOGOUT', 'USER', 15),
(216, 18, '2024-11-12 17:21:11', 'LOGOUT', 'USER', 18),
(217, 15, '2024-11-12 17:23:54', 'LOGOUT', 'USER', 15),
(218, 18, '2024-11-12 17:23:59', 'LOGOUT', 'USER', 18),
(219, 15, '2024-11-12 17:24:34', 'LOGOUT', 'USER', 15),
(220, 11, '2024-11-12 17:24:42', 'LOGOUT', 'USER', 11),
(221, 13, '2024-11-12 17:24:49', 'LOGOUT', 'USER', 13),
(222, 15, '2024-11-12 17:24:55', 'LOGOUT', 'USER', 15),
(223, 18, '2024-11-12 17:26:03', 'CREATE', 'COUNTER_SALE', 24),
(224, 18, '2024-11-12 17:27:02', 'LOGOUT', 'USER', 18),
(225, 15, '2024-11-12 17:30:18', 'LOGOUT', 'USER', 15),
(226, 18, '2024-11-12 17:31:40', 'LOGOUT', 'USER', 18),
(227, 15, '2024-11-12 17:31:48', 'LOGOUT', 'USER', 15),
(228, 11, '2024-11-12 17:31:55', 'LOGOUT', 'USER', 11),
(229, 18, '2024-11-12 17:32:44', 'LOGOUT', 'USER', 18),
(230, 11, '2024-11-12 17:32:49', 'LOGOUT', 'USER', 11),
(231, 11, '2024-11-12 17:33:04', 'LOGOUT', 'USER', 11),
(232, 15, '2024-11-12 17:33:12', 'LOGOUT', 'USER', 15),
(233, 11, '2024-11-12 17:34:39', 'LOGOUT', 'USER', 11),
(234, 15, '2024-11-12 17:34:46', 'LOGOUT', 'USER', 15),
(235, 18, '2024-11-12 17:39:05', 'LOGOUT', 'USER', 18),
(236, 11, '2024-11-12 17:40:50', 'LOGOUT', 'USER', 11),
(237, 19, '2024-11-12 17:43:41', 'LOGOUT', 'USER', 19),
(238, 15, '2024-11-12 17:43:48', 'LOGOUT', 'USER', 15),
(239, 11, '2024-11-12 17:43:54', 'LOGOUT', 'USER', 11),
(240, 19, '2024-11-12 17:58:22', 'LOGOUT', 'USER', 19),
(241, 11, '2024-11-12 17:59:28', 'LOGOUT', 'USER', 11),
(242, 19, '2024-11-12 18:09:46', 'LOGOUT', 'USER', 19),
(243, 11, '2024-11-12 18:10:07', 'LOGOUT', 'USER', 11);

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
(5, NULL, '2024-11-09', 50.00, 11),
(6, NULL, '2024-11-09', 50.00, 11),
(7, NULL, '2024-11-09', 150.00, 15),
(8, 2, '2024-11-09', 100.00, 15),
(9, 2, '2024-11-09', 250.00, 15),
(10, 1, '2024-11-11', 1040.00, 11),
(11, 2, '2024-11-11', 1040.00, 11),
(12, 2, '2024-11-12', 500.00, 15),
(13, 2, '2024-11-12', 1000.00, 15),
(14, NULL, '2024-11-12', 2600.00, 15),
(15, 2, '2024-11-12', 100.00, 11),
(16, 2, '2024-11-12', 1040.00, 11),
(17, 2, '2024-11-12', 1040.00, 11),
(18, 1, '2024-11-12', 2080.00, 11),
(19, 2, '2024-11-12', 1040.00, 11),
(20, 2, '2024-11-12', 1040.00, 11),
(21, 2, '2024-11-12', 1000.00, 11),
(22, 2, '2024-11-12', 1040.00, 11),
(23, 2, '2024-11-12', 1040.00, 11),
(24, NULL, '2024-11-12', 1040.00, 18),
(25, 2, '2024-11-12', 1040.00, 11),
(26, 1, '2024-11-12', 1040.00, 11),
(27, 2, '2024-11-12', 200.00, 11);

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
(10, 10, 12, 2, 520.00),
(11, 11, 12, 2, 520.00),
(12, 12, 13, 10, 50.00),
(13, 13, 14, 10, 100.00),
(14, 14, 12, 5, 520.00),
(15, 15, 13, 2, 50.00),
(16, 16, 12, 2, 520.00),
(17, 17, 12, 2, 520.00),
(18, 18, 12, 4, 520.00),
(19, 19, 12, 2, 520.00),
(20, 20, 12, 2, 520.00),
(21, 21, 14, 10, 100.00),
(22, 22, 12, 2, 520.00),
(23, 23, 12, 2, 520.00),
(24, 24, 12, 2, 520.00),
(25, 25, 12, 2, 520.00),
(26, 26, 12, 2, 520.00),
(27, 27, 14, 2, 100.00);

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
  `contact_info` varchar(200) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`doctor_id`, `name`, `specialization`, `contact_info`, `user_id`) VALUES
(5, 'Dr.Mitul', 'Intern Doctor ', 'mitul@hospital.com', 18),
(7, 'Dr.Khaled', 'Cardiac', '01381387137', 19);

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
(12, 'Ace', 'dasfasfasfs', 'Tablet', 1, 9),
(13, 'Ambrox', 'SYRUP', 'Tablet', 1, 10),
(14, 'MaxPro', 'Acidity', 'Tablet', 1, 9),
(15, 'sad', 'asd', 'Tablet', 1, 10),
(16, 'Napa', 'kills pain', 'Tablet', 2, 9);

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
(19, 9, '2024-11-21', 356356.00, 'Paid', 13, 'unpaid', NULL),
(20, 9, '2024-11-09', 5646.00, 'Paid', 13, 'unpaid', NULL),
(21, 9, '2024-11-13', 435346.00, 'Paid', 13, 'unpaid', NULL);

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
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`prescription_id`, `customer_id`, `doctor_id`, `prescription_date`, `status`) VALUES
(21, 2, 7, '2024-11-12', 'Pending'),
(22, 2, 7, '2024-11-12', 'Pending'),
(23, 2, 7, '2024-11-12', 'Filled'),
(24, 2, 7, '2024-11-12', 'Filled'),
(25, 2, 7, '2024-11-12', 'Pending'),
(26, 1, 7, '2024-11-12', 'Pending'),
(29, 1, 7, '2024-11-12', 'Filled');

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

--
-- Dumping data for table `prescription_item`
--

INSERT INTO `prescription_item` (`prescription_item_id`, `prescription_id`, `drug_id`, `quantity`, `dosage_instructions`) VALUES
(21, 21, 12, 2, 'After dinner'),
(22, 22, 12, 2, 'After dinner'),
(23, 23, 12, 2, 'After dinner'),
(24, 24, 14, 2, 'After dinner'),
(25, 25, 12, 2, 'After dinner'),
(26, 26, 12, 2, 'After dinner'),
(29, 29, 12, 2, 'After dinner');

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
(12, 12, 9, 69, '2026-10-16', 520.00, 13),
(13, 13, 10, 88, '2024-11-28', 50.00, 11),
(14, 14, 9, 78, '2027-10-20', 100.00, 11),
(15, 15, 10, 43634, '2024-11-28', 44.00, 14),
(16, 16, 9, 100, '2030-02-06', 5.00, 11);

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
(9, 'SquarePharma', 'Narshingdi', 'Bi-Weekly', 13),
(10, 'OrionPharma ', 'Gazipur', 'Monthly', 14);

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
(11, 'admin123', '$2y$10$4hds7y48E2S0fXU9PA.y.OhR6sqNCJfAulwufDf1pPI8uuB32kaTa', 'Administrator', '2024-11-12 18:09:52'),
(13, 'ifti_Square', '$2y$10$yzScuFtRAHHvtG3Q642eOeHNqGpihOEPNl1lY7svRaCqTrcDFVGMa', 'Supplier', '2024-11-12 17:24:46'),
(14, 'Orion', '$2y$10$VV9ctOir6WOCFzm81v5jp.uMGTYf1JoKSej6MxS4scAmqy2Wgs03y', 'Supplier', '2024-11-11 22:36:32'),
(15, 'saima', '$2y$10$Vh4uzCXG/9uHDuLhyEO.guNhOtQwLzOdWavxareKrxi0xEEvR645q', 'Cashier', '2024-11-12 17:43:45'),
(18, 'Doctor123', '$2y$10$g2IsY3wLrRpzNCGIrAzVwOvF16.jF6dC546f.9OdXXvy8wWGdHtzW', 'Doctor', '2024-11-12 18:10:12'),
(19, 'Dr.Khaled', '$2y$10$7ShsnJEeH1Np3QMRFD/28uxHFMyxpnpanFD3zuhfGY1M82UJ9bExK', 'Doctor', '2024-11-12 17:59:31');

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
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fk_supplier_id` (`supplier_id`);

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
  ADD KEY `doctor_id` (`doctor_id`);

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=244;

--
-- AUTO_INCREMENT for table `counter_sale`
--
ALTER TABLE `counter_sale`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `counter_sale_item`
--
ALTER TABLE `counter_sale_item`
  MODIFY `sale_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `drug`
--
ALTER TABLE `drug`
  MODIFY `drug_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `drug_category`
--
ALTER TABLE `drug_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `prescription_item`
--
ALTER TABLE `prescription_item`
  MODIFY `prescription_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `stock_item`
--
ALTER TABLE `stock_item`
  MODIFY `stock_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  ADD CONSTRAINT `counter_sale_item_ibfk_2` FOREIGN KEY (`stock_item_id`) REFERENCES `stock_item` (`stock_item_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `drug`
--
ALTER TABLE `drug`
  ADD CONSTRAINT `drug_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `drug_category` (`category_id`),
  ADD CONSTRAINT `fk_supplier_id` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
  ADD CONSTRAINT `prescription_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`doctor_id`);

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
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 07, 2025 at 12:19 AM
-- Server version: 8.0.41
-- PHP Version: 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mifmail_aelite`
--

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwd` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `refcode` varchar(20) DEFAULT NULL,
  `id_referral` int DEFAULT NULL,
  `status` enum('new','active','disabled') NOT NULL DEFAULT 'new',
  `timezone` varchar(50) NOT NULL,
  `otp` char(4) DEFAULT NULL,
  `role` enum('member','admin','referral','manager','superadmin') NOT NULL DEFAULT 'member',
  `ip_addr` varchar(45) DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `email`, `passwd`, `created_at`, `updated_at`, `refcode`, `id_referral`, `status`, `timezone`, `otp`, `role`, `ip_addr`, `is_delete`) VALUES
(1, 'yisayi7090@macho3.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '2025-04-01 03:03:34', '2025-04-04 23:48:54', '0mfk32m4', NULL, 'active', 'Asia/Singapore', NULL, 'member', '180.254.224.15', 0),
(2, 'a@a.a', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '2025-04-01 03:03:34', '2025-04-01 03:03:34', '0mfk32m3', 1, 'active', 'Asia/Singapore', NULL, 'member', '180.254.224.15', 0);

-- --------------------------------------------------------

--
-- Table structure for table `member_commission`
--

CREATE TABLE `member_commission` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `upline_id` int NOT NULL,
  `amount` decimal(10,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_deposit`
--

CREATE TABLE `member_deposit` (
  `id` int NOT NULL,
  `invoice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `commission` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','complete','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `member_deposit`
--

INSERT INTO `member_deposit` (`id`, `invoice`, `member_id`, `amount`, `commission`, `created_at`, `status`) VALUES
(1, 'INV-123919', 2, 10000.00, 100.00, '2025-04-02 15:35:46', 'complete'),
(2, 'INV-CED750AC', 1, 2000.00, 0.00, '2025-04-06 15:07:14', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `member_sinyal`
--

CREATE TABLE `member_sinyal` (
  `id` int NOT NULL,
  `amount_btc` decimal(16,6) NOT NULL,
  `member_id` int DEFAULT NULL,
  `sinyal_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `member_sinyal`
--

INSERT INTO `member_sinyal` (`id`, `amount_btc`, `member_id`, `sinyal_id`, `created_at`, `updated_at`) VALUES
(26, 0.000290, 143, 50, '2025-03-28 09:16:08', '2025-03-28 09:16:08'),
(27, 0.000725, 144, 50, '2025-03-28 09:16:08', '2025-03-28 09:16:08'),
(28, 0.000243, 143, 52, '2025-03-28 15:05:53', '2025-03-28 15:05:53'),
(29, 0.000606, 144, 52, '2025-03-28 15:05:53', '2025-03-28 15:05:53'),
(30, 0.000243, 143, 53, '2025-03-28 15:12:09', '2025-03-28 15:12:09'),
(31, 0.000606, 144, 53, '2025-03-28 15:12:09', '2025-03-28 15:12:09');

-- --------------------------------------------------------

--
-- Table structure for table `proxies`
--

CREATE TABLE `proxies` (
  `id` bigint UNSIGNED NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `proxies`
--

INSERT INTO `proxies` (`id`, `ip_address`, `port`, `username`, `password`, `created_at`) VALUES
(1, '38.154.227.167', 5868, 'brwbdrjy', '4mqmjsgb0t3i', NULL),
(2, '38.153.152.244', 9594, 'brwbdrjy', '4mqmjsgb0t3i', NULL),
(3, '173.211.0.148', 6641, 'brwbdrjy', '4mqmjsgb0t3i', NULL),
(4, '86.38.234.176', 6630, 'brwbdrjy', '4mqmjsgb0t3i', NULL),
(5, '161.123.152.115', 6360, 'brwbdrjy', '4mqmjsgb0t3i', NULL),
(6, '23.94.138.75', 6349, 'brwbdrjy', '4mqmjsgb0t3i', NULL),
(7, '64.64.118.149', 6732, 'brwbdrjy', '4mqmjsgb0t3i', NULL),
(8, '198.105.101.92', 5721, 'brwbdrjy', '4mqmjsgb0t3i', NULL),
(9, '166.88.58.10', 5735, 'brwbdrjy', '4mqmjsgb0t3i', NULL),
(10, '45.151.162.198', 6600, 'brwbdrjy', '4mqmjsgb0t3i', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`) VALUES
(3, 'price', '2000'),
(4, 'cost', '0.005'),
(5, 'referral_fee', '0.01');

-- --------------------------------------------------------

--
-- Table structure for table `sinyal`
--

CREATE TABLE `sinyal` (
  `id` int NOT NULL,
  `order_id` bigint DEFAULT NULL,
  `type` enum('Buy A','Buy B','Buy C','Buy D','Sell A','Sell B','Sell C','Sell D') NOT NULL DEFAULT 'Buy A',
  `entry_price` decimal(10,2) NOT NULL,
  `pair_id` int DEFAULT NULL,
  `admin_id` int DEFAULT NULL,
  `ip_addr` varchar(45) DEFAULT NULL,
  `is_deleted` enum('no','yes') NOT NULL DEFAULT 'no',
  `status` enum('pending','filled','canceled') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sinyal`
--

INSERT INTO `sinyal` (`id`, `order_id`, `type`, `entry_price`, `pair_id`, `admin_id`, `ip_addr`, `is_deleted`, `status`, `created_at`, `updated_at`) VALUES
(50, 11016135, 'Buy A', 75000.00, NULL, 143, '127.0.0.1', 'yes', 'pending', '2025-03-28 16:16:08', '2025-03-28 22:05:34'),
(52, 11125241, 'Buy A', 90000.00, NULL, 143, '127.0.0.1', 'no', 'filled', '2025-03-28 22:05:53', '2025-03-28 22:56:28'),
(53, 11127872, 'Buy B', 90000.00, NULL, 143, '127.0.0.1', 'no', 'filled', '2025-03-28 22:12:09', '2025-03-30 15:59:11'),
(57, 11418901, 'Sell A', 80000.00, 52, 143, '127.0.0.1', 'no', 'filled', '2025-03-29 13:39:06', '2025-03-30 16:02:50');

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE `wallet` (
  `id` int NOT NULL,
  `master_wallet` decimal(20,2) NOT NULL DEFAULT '0.00',
  `client_wallet` decimal(20,2) NOT NULL DEFAULT '0.00',
  `member_id` int NOT NULL,
  `order_id` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet`
--

INSERT INTO `wallet` (`id`, `master_wallet`, `client_wallet`, `member_id`, `order_id`) VALUES
(1, 7.92, 7.92, 143, 11418901),
(2, 19.79, 19.79, 144, 11418901);

-- --------------------------------------------------------

--
-- Table structure for table `withdraw`
--

CREATE TABLE `withdraw` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `withdraw_type` enum('fiat','usdt','btc') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'fiat',
  `amount` decimal(18,2) NOT NULL,
  `payment_details` text,
  `wallet_address` varchar(255) DEFAULT NULL,
  `status` enum('pending','rejected','completed') NOT NULL DEFAULT 'pending',
  `jenis` enum('trade','withdraw','balance') NOT NULL DEFAULT 'withdraw',
  `requested_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at` datetime DEFAULT NULL,
  `admin_notes` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `withdraw`
--

INSERT INTO `withdraw` (`id`, `member_id`, `withdraw_type`, `amount`, `payment_details`, `wallet_address`, `status`, `jenis`, `requested_at`, `processed_at`, `admin_notes`) VALUES
(41, 1, 'usdt', 10.00, NULL, NULL, 'pending', 'withdraw', '2025-04-02 23:36:41', NULL, NULL),
(42, 1, 'fiat', 20.00, NULL, NULL, 'pending', 'withdraw', '2025-04-02 23:36:41', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `refcode` (`refcode`),
  ADD KEY `id_referral` (`id_referral`);

--
-- Indexes for table `member_commission`
--
ALTER TABLE `member_commission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `downline_id` (`upline_id`);

--
-- Indexes for table `member_deposit`
--
ALTER TABLE `member_deposit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `member_sinyal`
--
ALTER TABLE `member_sinyal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_member_sinyal_member` (`member_id`),
  ADD KEY `fk_member_sinyal_sinyal` (`sinyal_id`);

--
-- Indexes for table `proxies`
--
ALTER TABLE `proxies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `sinyal`
--
ALTER TABLE `sinyal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `fk_admin` (`admin_id`);

--
-- Indexes for table `wallet`
--
ALTER TABLE `wallet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `withdraw`
--
ALTER TABLE `withdraw`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `member_commission`
--
ALTER TABLE `member_commission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_deposit`
--
ALTER TABLE `member_deposit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `member_sinyal`
--
ALTER TABLE `member_sinyal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `proxies`
--
ALTER TABLE `proxies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sinyal`
--
ALTER TABLE `sinyal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `wallet`
--
ALTER TABLE `wallet`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `withdraw`
--
ALTER TABLE `withdraw`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `member_ibfk_1` FOREIGN KEY (`id_referral`) REFERENCES `member` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `member_commission`
--
ALTER TABLE `member_commission`
  ADD CONSTRAINT `member_commission_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `member_commission_ibfk_2` FOREIGN KEY (`upline_id`) REFERENCES `member` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `member_deposit`
--
ALTER TABLE `member_deposit`
  ADD CONSTRAINT `member_deposit_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `member_sinyal`
--
ALTER TABLE `member_sinyal`
  ADD CONSTRAINT `fk_member_sinyal_member` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_member_sinyal_sinyal` FOREIGN KEY (`sinyal_id`) REFERENCES `sinyal` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sinyal`
--
ALTER TABLE `sinyal`
  ADD CONSTRAINT `fk_admin` FOREIGN KEY (`admin_id`) REFERENCES `member` (`id`);

--
-- Constraints for table `wallet`
--
ALTER TABLE `wallet`
  ADD CONSTRAINT `wallet_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`),
  ADD CONSTRAINT `wallet_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `sinyal` (`order_id`);

--
-- Constraints for table `withdraw`
--
ALTER TABLE `withdraw`
  ADD CONSTRAINT `withdraw_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

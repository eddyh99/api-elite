-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 17, 2025 at 05:57 PM
-- Server version: 8.0.43
-- PHP Version: 8.4.10

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
  `status` enum('new','active','disabled','referral') NOT NULL DEFAULT 'new',
  `timezone` varchar(50) NOT NULL,
  `otp` char(4) DEFAULT NULL,
  `role` enum('member','admin','referral','manager','superadmin') NOT NULL DEFAULT 'member',
  `position_a` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `position_b` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `position_c` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `position_d` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `ip_addr` varchar(45) DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `email`, `passwd`, `created_at`, `updated_at`, `refcode`, `id_referral`, `status`, `timezone`, `otp`, `role`, `position_a`, `position_b`, `position_c`, `position_d`, `ip_addr`, `is_delete`) VALUES
(1, 'a@a.a', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '2025-05-16 15:34:43', '2025-08-17 07:36:24', 'm4573r', NULL, 'active', '', NULL, 'superadmin', 0.0000, 0.0000, 0.0000, 0.0000, NULL, 0),
(9, '3a3aj4g4@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '2025-05-20 00:53:33', '2025-08-17 09:48:09', NULL, 13, 'active', 'Asia/Shanghai', NULL, 'member', 0.0000, 0.0000, 0.0000, 0.0000, '103.175.212.89', 0),
(13, 'principe.nerini@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '2025-05-26 01:08:02', '2025-08-17 09:48:20', 'prince', NULL, 'active', 'Asia/Singapore', NULL, 'referral', 0.0000, 0.0000, 0.0000, 0.0000, '59.153.130.103', 0);

-- --------------------------------------------------------

--
-- Table structure for table `member_commission`
--

CREATE TABLE `member_commission` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `downline_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `order_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `member_commission`
--

INSERT INTO `member_commission` (`id`, `member_id`, `downline_id`, `amount`, `order_id`, `created_at`) VALUES
(1, 13, 9, 1.34, 15207752, '2025-08-17 06:33:54');

-- --------------------------------------------------------

--
-- Table structure for table `member_deposit`
--

CREATE TABLE `member_deposit` (
  `id` int NOT NULL,
  `invoice` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `member_id` int NOT NULL,
  `upline_id` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `commission` decimal(10,2) NOT NULL,
  `is_manual` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','complete','failed') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `member_deposit`
--

INSERT INTO `member_deposit` (`id`, `invoice`, `member_id`, `upline_id`, `amount`, `commission`, `is_manual`, `created_at`, `status`) VALUES
(1, 'INV-CF720137', 9, 13, 1000.00, 20.00, 1, '2025-08-17 06:29:34', 'complete'),
(2, 'INV-972775B3', 9, NULL, 1000.00, 20.00, 1, '2025-08-17 07:29:07', 'complete');

-- --------------------------------------------------------

--
-- Table structure for table `member_sinyal`
--

CREATE TABLE `member_sinyal` (
  `id` int NOT NULL,
  `amount_btc` decimal(16,8) NOT NULL,
  `amount_usdt` decimal(16,2) NOT NULL,
  `member_id` int DEFAULT NULL,
  `sinyal_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `member_sinyal`
--

INSERT INTO `member_sinyal` (`id`, `amount_btc`, `amount_usdt`, `member_id`, `sinyal_id`, `created_at`, `updated_at`) VALUES
(1, 0.00231250, 250.00, 9, 1, '2025-08-17 06:33:18', '2025-08-17 06:33:28'),
(2, 0.00004625, 5.00, 13, 1, '2025-08-17 06:33:18', '2025-08-17 06:33:28'),
(5, 0.00231250, 277.20, 9, 2, '2025-08-17 06:33:54', '2025-08-17 06:33:54'),
(6, 0.00004625, 5.53, 13, 2, '2025-08-17 06:33:54', '2025-08-17 06:33:54'),
(7, 0.00007773, 8.17, 1, 3, '2025-08-17 07:35:11', '2025-08-17 07:35:23'),
(8, 0.00478921, 503.37, 9, 3, '2025-08-17 07:35:11', '2025-08-17 07:35:23'),
(9, 0.00005138, 5.40, 13, 3, '2025-08-17 07:35:11', '2025-08-17 07:35:23'),
(13, 0.00007773, 9.23, 1, 4, '2025-08-17 07:36:24', '2025-08-17 07:36:24'),
(14, 0.00478921, 569.33, 9, 4, '2025-08-17 07:36:24', '2025-08-17 07:36:24'),
(15, 0.00005138, 6.10, 13, 4, '2025-08-17 07:36:24', '2025-08-17 07:36:24');

-- --------------------------------------------------------

--
-- Table structure for table `proxies`
--

CREATE TABLE `proxies` (
  `id` bigint UNSIGNED NOT NULL,
  `ip_address` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `port` int NOT NULL,
  `username` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(3, 'price', '500'),
(4, 'cost', '0.01'),
(5, 'referral_fee', '0.02'),
(6, 'cost_trade', '0.01'),
(7, 'asset_btc', '0.00001838'),
(8, 'step', '500'),
(9, 'bank_account_name', 'agus satrio'),
(10, 'bank_account_type', 'checking'),
(11, 'bank_routing_number', '123456'),
(12, 'bank_account_number', '741258963');

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
(1, 15207652, 'Buy A', 108000.00, 1, 1, '180.254.225.106', 'no', 'filled', '2025-08-17 14:33:18', '2025-08-17 14:33:54'),
(2, 15207752, 'Sell A', 120000.00, 1, 1, '180.254.225.106', 'no', 'filled', '2025-08-17 14:33:46', '2025-08-17 14:33:54'),
(3, 15213245, 'Buy A', 105000.00, 3, 1, '180.254.225.106', 'no', 'filled', '2025-08-17 15:35:11', '2025-08-17 15:36:24'),
(4, 15213317, 'Sell A', 119000.00, 3, 1, '180.254.225.106', 'no', 'filled', '2025-08-17 15:36:15', '2025-08-17 15:36:24');

-- --------------------------------------------------------

--
-- Table structure for table `tb_member_onetone`
--

CREATE TABLE `tb_member_onetone` (
  `id` int UNSIGNED NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_payment_onetoone`
--

CREATE TABLE `tb_payment_onetoone` (
  `id` int UNSIGNED NOT NULL,
  `id_member_onetoone` int UNSIGNED NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `status_invoice` enum('paid','unpaid') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'unpaid',
  `link_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `invoice_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE `wallet` (
  `id` int NOT NULL,
  `master_wallet` decimal(20,2) NOT NULL DEFAULT '0.00',
  `client_wallet` decimal(20,2) NOT NULL DEFAULT '0.00',
  `member_id` int NOT NULL,
  `order_id` bigint NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wallet`
--

INSERT INTO `wallet` (`id`, `master_wallet`, `client_wallet`, `member_id`, `order_id`, `created_at`) VALUES
(1, 12.39, 13.47, 9, 15207752, '2025-08-17 14:33:54'),
(2, 0.27, 0.26, 13, 15207752, '2025-08-17 14:33:54'),
(3, 0.54, 0.52, 1, 15213317, '2025-08-17 15:36:24'),
(4, 33.31, 32.65, 9, 15213317, '2025-08-17 15:36:24'),
(5, 0.35, 0.35, 13, 15213317, '2025-08-17 15:36:24');

-- --------------------------------------------------------

--
-- Table structure for table `withdraw`
--

CREATE TABLE `withdraw` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `withdraw_type` enum('fiat','usdt','btc','usdc') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'fiat',
  `amount` decimal(18,6) NOT NULL,
  `payment_details` text,
  `wallet_address` varchar(255) DEFAULT NULL,
  `status` enum('pending','rejected','completed') NOT NULL DEFAULT 'pending',
  `jenis` enum('trade','withdraw','balance','comission') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'withdraw',
  `requested_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at` datetime DEFAULT NULL,
  `admin_notes` text,
  `ref_id` int DEFAULT NULL,
  `is_topup` enum('yes','no') NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `withdraw`
--

INSERT INTO `withdraw` (`id`, `member_id`, `withdraw_type`, `amount`, `payment_details`, `wallet_address`, `status`, `jenis`, `requested_at`, `processed_at`, `admin_notes`, `ref_id`, `is_topup`) VALUES
(1, 9, 'usdt', 1000.000000, NULL, NULL, 'pending', 'trade', '2025-08-17 14:29:34', NULL, NULL, 1, 'yes'),
(2, 13, 'usdt', 20.000000, NULL, NULL, 'pending', 'comission', '2025-08-17 14:29:34', NULL, NULL, 1, 'yes'),
(3, 13, 'usdt', 20.000000, NULL, NULL, 'pending', 'trade', '2025-08-17 14:29:34', NULL, NULL, 1, 'yes'),
(4, 13, 'usdt', 1.340000, NULL, NULL, 'pending', 'comission', '2025-08-17 14:33:54', NULL, NULL, NULL, 'no'),
(5, 13, 'usdt', 1.340000, NULL, NULL, 'pending', 'trade', '2025-08-17 14:33:54', NULL, NULL, NULL, 'no'),
(8, 9, 'usdt', 1000.000000, NULL, NULL, 'pending', 'trade', '2025-08-17 15:29:07', NULL, NULL, 2, 'yes'),
(9, 13, 'usdt', 20.000000, NULL, NULL, 'pending', 'comission', '2025-08-17 15:29:07', NULL, NULL, 2, 'yes'),
(10, 13, 'usdt', 20.000000, NULL, NULL, 'pending', 'trade', '2025-08-17 15:29:07', NULL, NULL, 2, 'yes');

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
  ADD KEY `downline_id` (`downline_id`),
  ADD KEY `member_commission_ibfk_3` (`order_id`);

--
-- Indexes for table `member_deposit`
--
ALTER TABLE `member_deposit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `member_deposit_ibfk_2` (`upline_id`);

--
-- Indexes for table `member_sinyal`
--
ALTER TABLE `member_sinyal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_id` (`member_id`,`sinyal_id`),
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
-- Indexes for table `tb_member_onetone`
--
ALTER TABLE `tb_member_onetone`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_payment_onetoone`
--
ALTER TABLE `tb_payment_onetoone`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tb_payment_onetoone_id_member_onetoone_foreign` (`id_member_onetoone`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `member_commission`
--
ALTER TABLE `member_commission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `member_deposit`
--
ALTER TABLE `member_deposit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `member_sinyal`
--
ALTER TABLE `member_sinyal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `proxies`
--
ALTER TABLE `proxies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sinyal`
--
ALTER TABLE `sinyal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_member_onetone`
--
ALTER TABLE `tb_member_onetone`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_payment_onetoone`
--
ALTER TABLE `tb_payment_onetoone`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallet`
--
ALTER TABLE `wallet`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `withdraw`
--
ALTER TABLE `withdraw`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  ADD CONSTRAINT `member_commission_ibfk_2` FOREIGN KEY (`downline_id`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `member_commission_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `sinyal` (`order_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `member_deposit`
--
ALTER TABLE `member_deposit`
  ADD CONSTRAINT `member_deposit_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `member_deposit_ibfk_2` FOREIGN KEY (`upline_id`) REFERENCES `member` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

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
-- Constraints for table `tb_payment_onetoone`
--
ALTER TABLE `tb_payment_onetoone`
  ADD CONSTRAINT `tb_payment_onetoone_id_member_onetoone_foreign` FOREIGN KEY (`id_member_onetoone`) REFERENCES `tb_member_onetone` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

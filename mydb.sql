-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2025 at 03:33 PM
-- Server version: 8.0.42
-- PHP Version: 8.3.22

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
  `ip_addr` varchar(45) DEFAULT NULL,
  `position` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `email`, `passwd`, `created_at`, `updated_at`, `refcode`, `id_referral`, `status`, `timezone`, `otp`, `role`, `ip_addr`, `position`, `is_delete`) VALUES
(1, 'a@a.a', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '2025-05-16 15:34:43', '2025-06-19 10:04:14', NULL, NULL, 'active', '', NULL, 'superadmin', NULL, 0.0000, 0),
(2, 'dilame3476@deusa7.com', '7c222fb2927d828af22f592134e8932480637c0d', '2025-05-16 16:02:38', '2025-05-16 08:04:08', NULL, NULL, 'active', 'Asia/Makassar', NULL, 'member', '182.253.116.2', 0.0000, 0),
(3, 'hafidelamranijoutey2@gmail.com', 'eed526cd6d50d073aa2e6366c743a5ef4de4c452', '2025-05-18 10:09:19', '2025-05-27 07:56:58', 'h4f1d', NULL, 'active', 'Asia/Makassar', NULL, 'referral', '103.24.150.159', 0.0000, 0),
(4, 'samuelegranocchia@gmail.com_2025-05-23', '2b8b0868102df8d9bc9bea937c3bd96a5a4e7146', '2025-05-19 09:13:04', '2025-05-26 02:18:57', NULL, NULL, 'new', 'Europe/Rome', '2047', 'member', '62.211.27.92', 0.0000, 1),
(6, 'b@b.b', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '2025-05-19 09:16:20', '2025-06-18 09:17:56', NULL, 3, 'active', 'Europe/Rome', NULL, 'member', '62.211.27.92', 0.0000, 0),
(7, 'danieldocooh@gmail.com', '5d9a94b24b414bec2225463d03fd04c04f1aa466', '2025-05-19 09:25:24', '2025-06-02 05:22:46', 'r3b3cc4', 13, 'active', 'Asia/Shanghai', NULL, 'referral', '103.175.212.66', 0.0000, 0),
(8, 'ssilenziog@gmail.com', '78f2d37fb951d3456c35b096ba5511eeaa0f73fe', '2025-05-19 10:38:12', '2025-05-24 15:14:29', NULL, 3, 'active', 'Europe/Rome', NULL, 'member', '217.202.8.52', 0.0000, 0),
(9, '3a3aj4g4@gmail.com', '23de24af77f1d5c4fdacf90ae06cf0c10320709b', '2025-05-20 00:53:33', '2025-05-26 02:04:03', NULL, 13, 'active', 'Asia/Shanghai', NULL, 'member', '103.175.212.89', 0.0000, 0),
(10, 'lisette.paula8899@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', '2025-05-20 05:25:54', '2025-06-02 04:27:01', 'p4ul4', NULL, 'active', 'Asia/Makassar', NULL, 'referral', '110.139.176.94', 0.0000, 0),
(11, 'maci81x@hotmail.it', '0d296436b80bc54f847035d231af30e72624530d', '2025-05-23 12:20:09', '2025-05-24 15:13:34', 'zzhr34o5', 3, 'active', 'Europe/Rome', NULL, 'member', '213.243.250.56', 0.0000, 0),
(12, 'stefano.giovagnoli1234@gmail.com', '7f838487959c746237accb0dc2b5848679221fab', '2025-05-23 15:11:23', '2025-05-23 07:13:06', 'poi6v814', NULL, 'active', 'Europe/Rome', NULL, 'member', '128.116.239.58', 0.0000, 0),
(13, 'principe.nerini@gmail.com', '884d1f5d29ba0927983cf11bf835badbdc5d3472', '2025-05-26 01:08:02', '2025-06-12 15:43:29', '69spoj50', NULL, 'active', 'Asia/Singapore', NULL, 'referral', '59.153.130.103', 0.0000, 0),
(14, 'aymanezza44@gmail.com_2025-06-19', '9ded1e71d9a28c6ac2ad51b229d9df8a4c92b2e8', '2025-06-06 09:40:49', '2025-06-18 23:49:06', NULL, NULL, 'disabled', 'Europe/Rome', '7953', 'member', '46.149.102.19', 0.0000, 1),
(16, 'ezzuzzu100@gmail.com', '9ded1e71d9a28c6ac2ad51b229d9df8a4c92b2e8', '2025-06-06 10:10:44', '2025-06-06 02:11:37', NULL, NULL, 'new', 'Europe/Rome', '3645', 'member', '46.149.102.19', 0.0000, 0),
(17, 'hafid.elamrani@icloud.com', 'f500ba2a5af141279659c22ccad5a8adce514396', '2025-06-06 10:13:36', '2025-06-06 02:16:24', NULL, NULL, 'new', 'Asia/Makassar', '6541', 'member', '103.24.150.159', 0.0000, 0),
(18, 'hafidelamranijoutey@gmail.com', 'cb7710a473de9120005f6049137520fbe42b30b6', '2025-06-06 10:18:19', '2025-06-06 02:19:11', NULL, NULL, 'new', 'Asia/Makassar', '8616', 'member', '103.24.150.159', 0.0000, 0),
(20, 'pippobaudo376@gmail.com', '9ded1e71d9a28c6ac2ad51b229d9df8a4c92b2e8', '2025-06-06 11:01:03', '2025-06-06 03:02:18', NULL, NULL, 'new', 'Europe/Rome', '2532', 'member', '46.149.102.19', 0.0000, 0),
(31, 'fabio.guerra1975@gmail.com', 'c7683c398c995ed5023c5e324e66f4125d781eaa', '2025-06-06 12:40:16', '2025-06-12 00:18:24', NULL, 7, 'active', 'Asia/Makassar', NULL, 'member', '110.136.212.108', 0.0000, 0),
(40, 'nevertouchme21@gmail.com', 'fbdef424b8d10220b478b5656aa73913439fcb2f', '2025-06-12 08:34:36', '2025-06-14 10:05:22', NULL, 13, 'active', 'Europe/Madrid', NULL, 'member', '81.38.78.146', 0.0000, 0),
(41, 'baruhbiton@delightmoney.com', 'a9dd8ac7aa806116b656e82ceb48549c8b103d9b', '2025-06-12 14:35:56', '2025-06-12 15:37:24', NULL, 13, 'active', 'Europe/Rome', NULL, 'member', '149.34.244.175', 0.0000, 0),
(43, 'bqqp9yedjo@zvvzuv.com', 'e51929cc849e6261b840ec7d8742a8826bff7043', '2025-06-19 07:20:57', '2025-06-18 23:24:03', 'zvvzuv', 3, 'active', 'Asia/Makassar', NULL, 'referral', '104.28.215.133', 0.0000, 0),
(45, 'qhfh2hfvwg@ibolinva.com', 'e51929cc849e6261b840ec7d8742a8826bff7043', '2025-06-19 07:29:35', '2025-06-18 23:35:36', NULL, 43, 'active', 'Asia/Jakarta', '8082', 'member', '104.28.215.133', 0.0000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `member_commission`
--

CREATE TABLE `member_commission` (
  `id` int NOT NULL,
  `member_id` int NOT NULL,
  `downline_id` int NOT NULL,
  `amount` decimal(10,4) NOT NULL,
  `order_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `member_deposit`
--

CREATE TABLE `member_deposit` (
  `id` int NOT NULL,
  `invoice` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `commission` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','complete','failed') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `member_deposit`
--

INSERT INTO `member_deposit` (`id`, `invoice`, `member_id`, `amount`, `commission`, `created_at`, `status`) VALUES
(1, 'INV-F8BFB5CF', 2, 500.00, 0.00, '2025-05-16 16:06:07', 'pending'),
(2, 'INV-5214A5D5', 3, 2700.00, 0.00, '2025-05-18 10:10:48', 'pending'),
(3, 'INV-E967A6A0', 6, 2000.00, 40.00, '2025-05-19 09:18:12', 'complete'),
(4, 'INV-182E29BE', 3, 500.00, 0.00, '2025-05-19 09:19:30', 'pending'),
(5, 'INV-67DAA062', 7, 1000.00, 0.00, '2025-05-19 09:35:08', 'pending'),
(6, 'INV-75632121', 7, 1000.00, 0.00, '2025-05-19 09:35:12', 'pending'),
(7, 'INV-BCC9B83F', 7, 1000.00, 0.00, '2025-05-19 09:35:13', 'pending'),
(8, 'INV-298C9E8C', 7, 1000.00, 0.00, '2025-05-19 09:35:14', 'pending'),
(9, 'INV-66ADD1B0', 7, 1000.00, 0.00, '2025-05-19 09:35:15', 'pending'),
(10, 'INV-BB501E97', 7, 1000.00, 0.00, '2025-05-19 09:35:15', 'pending'),
(11, 'INV-A901054F', 7, 1000.00, 0.00, '2025-05-19 09:35:16', 'pending'),
(12, 'INV-F6E714FA', 7, 1000.00, 0.00, '2025-05-19 09:35:17', 'pending'),
(13, 'INV-F1DCEF06', 9, 10000.00, 0.00, '2025-05-20 00:57:19', 'pending'),
(14, 'INV-27D07739', 9, 10000.00, 0.00, '2025-05-20 00:57:36', 'complete'),
(15, 'INV-63FEA1DF', 10, 500.00, 0.00, '2025-05-20 05:36:36', 'pending'),
(16, 'INV-0E6263B1', 9, 500.00, 0.00, '2025-05-22 08:25:14', 'pending'),
(17, 'INV-1A50E271', 8, 500.00, 10.00, '2025-05-22 08:29:06', 'complete'),
(18, 'INV-17A3BF8C', 9, 500.00, 0.00, '2025-05-22 08:34:08', 'pending'),
(19, 'INV-5FBC14AC', 11, 1000.00, 20.00, '2025-05-23 12:28:55', 'complete'),
(30, 'INV-7CE3476A', 7, 2500.00, 50.00, '2025-05-27 05:25:10', 'complete'),
(31, 'INV-7693428E', 3, 2300.00, 46.00, '2025-05-27 08:05:18', 'pending'),
(32, 'INV-27D07739', 13, 10000.00, 0.00, '2025-05-20 00:57:36', 'complete'),
(47, 'INV-A7E76787', 3, 500.00, 10.00, '2025-06-10 12:07:43', 'pending'),
(48, 'INV-C94808E4', 31, 5000.00, 100.00, '2025-06-12 08:22:08', 'pending'),
(49, 'INV-3F78EE19', 3, 500.00, 10.00, '2025-06-12 12:24:28', 'pending'),
(50, 'INV-03B28497', 3, 500.00, 10.00, '2025-06-12 12:24:43', 'pending'),
(51, 'INV-5144010B', 41, 600.00, 12.00, '2025-06-12 15:14:14', 'pending'),
(52, 'INV-B50CC5E1', 41, 59500.00, 1190.00, '2025-06-12 15:21:22', 'pending'),
(54, 'INV-AF9F3C6A', 45, 500.00, 10.00, '2025-06-19 07:31:11', 'complete');

-- --------------------------------------------------------

--
-- Table structure for table `member_sinyal`
--

CREATE TABLE `member_sinyal` (
  `id` int NOT NULL,
  `amount_btc` decimal(16,6) NOT NULL,
  `amount_usdt` decimal(16,6) NOT NULL,
  `member_id` int DEFAULT NULL,
  `sinyal_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `member_sinyal`
--

INSERT INTO `member_sinyal` (`id`, `amount_btc`, `amount_usdt`, `member_id`, `sinyal_id`, `created_at`, `updated_at`) VALUES
(1, 0.002153, 237.564709, 6, 1, '2025-06-19 09:06:36', '2025-06-19 09:07:01'),
(2, 0.001076, 118.782355, 45, 1, '2025-06-19 09:06:36', '2025-06-19 09:07:01'),
(5, 0.001076, 112.705512, 45, 2, '2025-06-19 09:13:02', '2025-06-19 09:13:02');

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
(7, 'asset_btc', '0.0256');

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
(1, 6514557, 'Buy A', 110000.00, 1, 12, '104.28.215.133', 'no', 'filled', '2025-06-19 17:06:36', '2025-06-19 17:13:02'),
(2, 6516196, 'Sell A', 100000.00, 1, 12, '104.28.215.133', 'no', 'filled', '2025-06-19 17:12:22', '2025-06-19 17:13:02');

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE `wallet` (
  `id` int NOT NULL,
  `master_wallet` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `client_wallet` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `member_id` int NOT NULL,
  `order_id` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wallet`
--

INSERT INTO `wallet` (`id`, `master_wallet`, `client_wallet`, `member_id`, `order_id`) VALUES
(1, -3.0100, -3.0100, 45, 6516196);

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
  `admin_notes` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `withdraw`
--

INSERT INTO `withdraw` (`id`, `member_id`, `withdraw_type`, `amount`, `payment_details`, `wallet_address`, `status`, `jenis`, `requested_at`, `processed_at`, `admin_notes`) VALUES
(1, 43, 'usdt', 5.000000, NULL, NULL, 'pending', 'comission', '2025-06-19 15:44:44', NULL, NULL),
(2, 43, 'usdt', 5.000000, NULL, NULL, 'pending', 'trade', '2025-06-19 15:44:44', NULL, NULL),
(3, 43, 'usdt', 5.000000, NULL, NULL, 'pending', 'comission', '2025-06-19 15:44:49', NULL, NULL),
(4, 43, 'usdt', 5.000000, NULL, NULL, 'pending', 'trade', '2025-06-19 15:45:11', NULL, NULL),
(5, 43, 'usdt', 10.000000, NULL, NULL, 'pending', 'balance', '2025-06-19 15:45:18', NULL, NULL),
(6, 43, 'usdt', 5.000000, '{\"recipient\":null,\"routing_number\":null,\"account_type\":null,\"swift_code\":null,\"address\":null,\"network\":\"BEP20\"}', '0x000000000000000000000000000000000000dEaD', 'completed', 'withdraw', '2025-06-19 15:46:45', NULL, NULL),
(7, 43, 'usdc', 5.000000, '{\"recipient\":null,\"routing_number\":null,\"account_type\":null,\"swift_code\":null,\"address\":null,\"network\":\"BEP20\"}', '0x000000000000000000000000000000000000dEaD', 'completed', 'withdraw', '2025-06-19 15:46:55', NULL, NULL),
(8, 45, 'usdt', 500.000000, NULL, NULL, 'pending', 'trade', '2025-06-19 16:13:54', NULL, NULL),
(9, 6, 'usdt', 1000.000000, NULL, NULL, 'pending', 'trade', '2025-06-19 16:14:39', NULL, NULL),
(10, 6, 'btc', 0.002151, NULL, NULL, 'pending', 'balance', '2025-06-19 16:22:53', NULL, NULL);

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
  ADD KEY `member_id` (`member_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `member_commission`
--
ALTER TABLE `member_commission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_deposit`
--
ALTER TABLE `member_deposit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `member_sinyal`
--
ALTER TABLE `member_sinyal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `proxies`
--
ALTER TABLE `proxies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sinyal`
--
ALTER TABLE `sinyal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wallet`
--
ALTER TABLE `wallet`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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

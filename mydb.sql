-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 07, 2025 at 08:16 AM
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
-- Database: `mifmail_hedgefund`
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
(1, 'pnglobal.usa@gmail.com', 'd6089ff3da5e59f6729670e427baafbbec8b76e2', '2025-05-16 15:34:43', '2025-07-06 14:06:34', 'm4573r', NULL, 'active', '', NULL, 'superadmin', 90.1225, 0.0000, 0.0000, 0.0000, NULL, 0),
(2, 'dilame3476@deusa7.com', '7c222fb2927d828af22f592134e8932480637c0d', '2025-05-16 16:02:38', '2025-05-16 08:04:08', NULL, NULL, 'active', 'Asia/Makassar', NULL, 'member', 0.0000, 0.0000, 0.0000, 0.0000, '182.253.116.2', 0),
(3, 'hafidelamranijoutey2@gmail.com', 'eed526cd6d50d073aa2e6366c743a5ef4de4c452', '2025-05-18 10:09:19', '2025-07-06 14:06:34', 'h4f1d', NULL, 'active', 'Asia/Makassar', NULL, 'referral', 23.7950, 0.0000, 0.0000, 0.0000, '103.24.150.159', 0),
(4, 'samuelegranocchia@gmail.com_2025-05-23', '2b8b0868102df8d9bc9bea937c3bd96a5a4e7146', '2025-05-19 09:13:04', '2025-05-26 02:18:57', NULL, NULL, 'new', 'Europe/Rome', '2047', 'member', 0.0000, 0.0000, 0.0000, 0.0000, '62.211.27.92', 1),
(6, 'brio21569@gmail.com', 'a338fc407b2199b042afe64bcdb0d8e419822c29', '2025-05-19 09:16:20', '2025-07-06 14:06:34', NULL, 3, 'active', 'Europe/Rome', NULL, 'member', 505.9125, 0.0000, 0.0000, 0.0000, '62.211.27.92', 0),
(7, 'danieldocooh@gmail.com', '5d9a94b24b414bec2225463d03fd04c04f1aa466', '2025-05-19 09:25:24', '2025-07-06 14:06:34', 'r3b3cc4', 13, 'active', 'Asia/Shanghai', NULL, 'referral', 658.0375, 0.0000, 0.0000, 0.0000, '103.175.212.66', 0),
(8, 'ssilenziog@gmail.com', '78f2d37fb951d3456c35b096ba5511eeaa0f73fe', '2025-05-19 10:38:12', '2025-07-06 14:06:34', NULL, 3, 'active', 'Europe/Rome', NULL, 'member', 126.4800, 0.0000, 0.0000, 0.0000, '217.202.8.52', 0),
(9, '3a3aj4g4@gmail.com', '23de24af77f1d5c4fdacf90ae06cf0c10320709b', '2025-05-20 00:53:33', '2025-07-06 14:06:34', NULL, 13, 'active', 'Asia/Shanghai', NULL, 'member', 2529.5500, 0.0000, 0.0000, 0.0000, '103.175.212.89', 0),
(10, 'lisette.paula8899@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', '2025-05-20 05:25:54', '2025-06-02 04:27:01', 'p4ul4', NULL, 'active', 'Asia/Makassar', NULL, 'referral', 0.0000, 0.0000, 0.0000, 0.0000, '110.139.176.94', 0),
(11, 'maci81x@hotmail.it', '0d296436b80bc54f847035d231af30e72624530d', '2025-05-23 12:20:09', '2025-07-06 14:06:34', 'zzhr34o5', 3, 'active', 'Europe/Rome', NULL, 'member', 252.9550, 0.0000, 0.0000, 0.0000, '213.243.250.56', 0),
(12, 'stefano.giovagnoli1234@gmail.com', '7f838487959c746237accb0dc2b5848679221fab', '2025-05-23 15:11:23', '2025-05-23 07:13:06', 'poi6v814', NULL, 'active', 'Europe/Rome', NULL, 'member', 0.0000, 0.0000, 0.0000, 0.0000, '128.116.239.58', 0),
(13, 'principe.nerini@gmail.com', '884d1f5d29ba0927983cf11bf835badbdc5d3472', '2025-05-26 01:08:02', '2025-07-06 14:06:34', '69spoj50', NULL, 'active', 'Asia/Singapore', NULL, 'referral', 2654.9550, 0.0000, 0.0000, 0.0000, '59.153.130.103', 0),
(14, 'aymanezza44@gmail.com_2025-06-21', '9ded1e71d9a28c6ac2ad51b229d9df8a4c92b2e8', '2025-06-06 09:40:49', '2025-06-20 22:52:04', NULL, NULL, 'new', 'Europe/Rome', '7953', 'member', 0.0000, 0.0000, 0.0000, 0.0000, '46.149.102.19', 1),
(16, 'ezzuzzu100@gmail.com_2025-06-21', '9ded1e71d9a28c6ac2ad51b229d9df8a4c92b2e8', '2025-06-06 10:10:44', '2025-06-20 22:52:15', NULL, NULL, 'new', 'Europe/Rome', '3645', 'member', 0.0000, 0.0000, 0.0000, 0.0000, '46.149.102.19', 1),
(17, 'hafid.elamrani@icloud.com_2025-06-21', 'f500ba2a5af141279659c22ccad5a8adce514396', '2025-06-06 10:13:36', '2025-06-20 22:52:31', NULL, NULL, 'new', 'Asia/Makassar', '6541', 'member', 0.0000, 0.0000, 0.0000, 0.0000, '103.24.150.159', 1),
(18, 'hafidelamranijoutey@gmail.com_2025-06-21', 'cb7710a473de9120005f6049137520fbe42b30b6', '2025-06-06 10:18:19', '2025-06-20 22:52:38', NULL, NULL, 'new', 'Asia/Makassar', '8616', 'member', 0.0000, 0.0000, 0.0000, 0.0000, '103.24.150.159', 1),
(20, 'pippobaudo376@gmail.com_2025-06-21', '9ded1e71d9a28c6ac2ad51b229d9df8a4c92b2e8', '2025-06-06 11:01:03', '2025-06-20 22:52:46', NULL, NULL, 'new', 'Europe/Rome', '2532', 'member', 0.0000, 0.0000, 0.0000, 0.0000, '46.149.102.19', 1),
(31, 'fabio.guerra1975@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '2025-06-06 12:40:16', '2025-07-06 14:06:34', NULL, 7, 'active', 'Asia/Makassar', NULL, 'member', 1254.7300, 0.0000, 0.0000, 0.0000, '110.136.212.108', 0),
(40, 'nevertouchme21@gmail.com', 'fbdef424b8d10220b478b5656aa73913439fcb2f', '2025-06-12 08:34:36', '2025-07-06 14:06:34', NULL, 13, 'active', 'Europe/Madrid', NULL, 'member', 5010.2250, 5000.0000, 0.0000, 0.0000, '81.38.78.146', 0),
(41, 'baruhbiton@delightmoney.com', 'a9dd8ac7aa806116b656e82ceb48549c8b103d9b', '2025-06-12 14:35:56', '2025-06-12 15:37:24', NULL, 13, 'active', 'Europe/Rome', NULL, 'member', 0.0000, 0.0000, 0.0000, 0.0000, '149.34.244.175', 0),
(43, 'dcatacchio@gmail.com', 'fca0336f1973c2d494b23837a242b237d19fd3b2', '2025-06-22 10:25:35', '2025-07-06 14:06:34', NULL, 3, 'active', 'Europe/Rome', '4371', 'member', 250.5125, 250.0000, 0.0000, 0.0000, '93.42.33.25', 0),
(44, 'rillino@yahoo.it', 'c5c2c819e888c0f82c00c5b3092650c58f7b0ebd', '2025-06-23 04:01:38', '2025-06-22 20:03:03', NULL, NULL, 'active', 'Europe/Rome', NULL, 'member', 0.0000, 0.0000, 0.0000, 0.0000, '37.159.45.44', 0),
(45, 'profitdelights@gmail.com_2025-06-25', '67722d5df937c7682aa8b14a63dc150bcc61390c', '2025-06-23 04:03:36', '2025-06-24 20:57:32', NULL, NULL, 'new', 'Asia/Singapore', '7102', 'member', 0.0000, 0.0000, 0.0000, 0.0000, '59.153.130.103', 1),
(47, 'armidaneglia27@gmail.com', 'cb7710a473de9120005f6049137520fbe42b30b6', '2025-06-24 13:11:24', '2025-06-24 20:57:24', NULL, NULL, 'active', 'Asia/Makassar', '2455', 'member', 0.0000, 0.0000, 0.0000, 0.0000, '103.24.150.159', 0),
(49, 'eddy_h99@yahoo.com', '01b307acba4f54f55aafc33bb06bbbf6ca803e9a', '2025-06-27 03:05:23', '2025-06-27 03:05:36', NULL, 10, 'active', 'Asia/Makassar', '8775', 'member', 0.0000, 0.0000, 0.0000, 0.0000, '59.153.129.5', 0),
(50, 'ezzuzzu100@gmail.com', '9ded1e71d9a28c6ac2ad51b229d9df8a4c92b2e8', '2025-06-28 16:41:49', '2025-06-29 18:46:03', NULL, NULL, 'active', 'Europe/Rome', '3942', 'member', 0.0000, 0.0000, 0.0000, 0.0000, '78.208.191.159', 0),
(52, 'kateehafidassistente@gmail.com', '9ded1e71d9a28c6ac2ad51b229d9df8a4c92b2e8', '2025-06-28 16:43:10', '2025-06-29 18:46:22', NULL, NULL, 'active', 'Europe/Rome', '1841', 'member', 0.0000, 0.0000, 0.0000, 0.0000, '78.208.191.159', 0),
(53, 'falzaranostefano@gmail.com', '0c1e62afdf7ca7d3c54c207ef0aec951c69afe16', '2025-07-02 13:55:37', '2025-07-03 17:03:43', NULL, NULL, 'active', 'Europe/Rome', '3231', 'member', 0.0000, 0.0000, 0.0000, 0.0000, '151.37.111.217', 0),
(67, 'falzarentsrl@gmail.com', '0eb5238845f31e97c470c2a4559ca4f4cc49fc81', '2025-07-02 14:13:48', '2025-07-02 06:15:02', NULL, NULL, 'active', 'Europe/Rome', NULL, 'member', 0.0000, 0.0000, 0.0000, 0.0000, '151.37.111.217', 0),
(74, 'rupo2010@virgilio.it', '765dc782b52c7bbec4dd2c06e791cb1fd170f840', '2025-07-06 02:51:40', '2025-07-06 14:06:34', NULL, 13, 'active', 'Asia/Makassar', '6829', 'member', 375.0000, 0.0000, 0.0000, 0.0000, '103.175.212.89', 0);

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
(1, 3, 6, 0.72, 1268192, '2025-07-05 04:38:48'),
(2, 13, 7, 1.20, 1268192, '2025-07-05 04:38:48'),
(3, 3, 8, 0.18, 1268192, '2025-07-05 04:38:48'),
(4, 13, 9, 3.60, 1268192, '2025-07-05 04:38:48'),
(5, 3, 11, 0.36, 1268192, '2025-07-05 04:38:48'),
(6, 3, 6, 0.49, 1272951, '2025-07-05 04:48:06'),
(7, 13, 7, 0.83, 1272951, '2025-07-05 04:48:06'),
(8, 3, 8, 0.12, 1272951, '2025-07-05 04:48:06'),
(9, 13, 9, 2.49, 1272951, '2025-07-05 04:48:06'),
(10, 3, 11, 0.24, 1272951, '2025-07-05 04:48:06'),
(11, 3, 6, 0.26, 1277675, '2025-07-05 04:58:58'),
(12, 13, 7, 0.45, 1277675, '2025-07-05 04:58:58'),
(13, 3, 8, 0.06, 1277675, '2025-07-05 04:58:58'),
(14, 13, 9, 1.30, 1277675, '2025-07-05 04:58:58'),
(15, 3, 11, 0.13, 1277675, '2025-07-05 04:58:58'),
(16, 7, 31, 0.86, 1277675, '2025-07-05 04:58:58'),
(17, 3, 6, 0.46, 1294824, '2025-07-05 05:47:37'),
(18, 3, 8, 0.11, 1294824, '2025-07-05 05:47:37'),
(19, 13, 9, 2.33, 1294824, '2025-07-05 05:47:37'),
(20, 3, 11, 0.23, 1294824, '2025-07-05 05:47:37'),
(21, 3, 6, 0.41, 1303635, '2025-07-05 06:08:54'),
(22, 13, 7, 0.53, 1303635, '2025-07-05 06:08:54'),
(23, 3, 8, 0.10, 1303635, '2025-07-05 06:08:54'),
(24, 13, 9, 2.06, 1303635, '2025-07-05 06:08:54'),
(25, 3, 11, 0.20, 1303635, '2025-07-05 06:08:54'),
(26, 7, 31, 1.02, 1303635, '2025-07-05 06:08:54'),
(27, 13, 40, 4.09, 1303635, '2025-07-05 06:08:54'),
(28, 3, 43, 0.20, 1303635, '2025-07-05 06:08:54');

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
(48, 'INV-C94808E4', 31, 5000.00, 100.00, '2025-06-12 08:22:08', 'complete'),
(49, 'INV-3F78EE19', 3, 500.00, 10.00, '2025-06-12 12:24:28', 'pending'),
(50, 'INV-03B28497', 3, 500.00, 10.00, '2025-06-12 12:24:43', 'pending'),
(51, 'INV-5144010B', 41, 600.00, 12.00, '2025-06-12 15:14:14', 'pending'),
(52, 'INV-B50CC5E1', 41, 59500.00, 1190.00, '2025-06-12 15:21:22', 'pending'),
(53, 'INV-88C77C93', 44, 2400.00, 48.00, '2025-06-23 04:03:58', 'pending'),
(54, 'INV-28E4D4A1', 43, 1000.00, 20.00, '2025-06-23 20:54:12', 'pending'),
(55, 'INV-137AA9DB', 3, 500.00, 10.00, '2025-06-23 20:57:54', 'pending'),
(56, 'INV-FDB185C4', 3, 500.00, 10.00, '2025-06-23 20:59:29', 'pending'),
(57, 'INV-3A8C6E52', 3, 500.00, 10.00, '2025-06-23 20:59:46', 'pending'),
(58, 'INV-B92CD121', 3, 500.00, 10.00, '2025-06-23 20:59:47', 'pending'),
(59, 'INV-BF4293E2', 43, 1000.00, 20.00, '2025-06-23 21:18:12', 'complete'),
(60, 'INV-137D0551', 3, 500.00, 10.00, '2025-06-24 13:34:31', 'pending'),
(61, 'INV-37A99288', 7, 1000.00, 20.00, '2025-06-25 10:39:17', 'pending'),
(62, 'INV-D796041F', 7, 1000.00, 20.00, '2025-06-25 10:40:45', 'pending'),
(63, 'INV-CDFA99F6', 40, 20000.00, 400.00, '2025-06-25 16:55:03', 'complete'),
(64, 'INV-E11817AD', 13, 2000.00, 40.00, '2025-06-28 06:09:28', 'pending'),
(65, 'INV-808BEFC5', 3, 500.00, 10.00, '2025-07-02 13:43:48', 'pending'),
(66, 'INV-100E7624', 67, 500.00, 10.00, '2025-07-02 14:15:25', 'pending'),
(71, 'INV-3D8210A7', 10, 500.00, 10.00, '2025-07-06 03:10:01', 'pending'),
(72, 'INV-90CDC461', 74, 1500.00, 30.00, '2025-07-06 03:28:10', 'complete');

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
(1, 0.00454711, 500.00, 6, 1, '2025-07-05 04:13:31', '2025-07-05 04:13:52'),
(2, 0.00113678, 125.00, 8, 1, '2025-07-05 04:13:31', '2025-07-05 04:13:52'),
(3, 0.02273555, 2500.00, 9, 1, '2025-07-05 04:13:31', '2025-07-05 04:13:52'),
(4, 0.00227355, 250.00, 11, 1, '2025-07-05 04:13:31', '2025-07-05 04:13:52'),
(9, 0.00022022, 23.33, 3, 2, '2025-07-05 04:30:44', '2025-07-05 04:31:31'),
(10, 0.00471894, 500.00, 6, 2, '2025-07-05 04:30:44', '2025-07-05 04:31:31'),
(11, 0.00786490, 833.33, 7, 2, '2025-07-05 04:30:44', '2025-07-05 04:31:31'),
(12, 0.00117974, 125.00, 8, 2, '2025-07-05 04:30:44', '2025-07-05 04:31:31'),
(13, 0.02359471, 2500.00, 9, 2, '2025-07-05 04:30:44', '2025-07-05 04:31:31'),
(14, 0.00235947, 250.00, 11, 2, '2025-07-05 04:30:44', '2025-07-05 04:31:31'),
(15, 0.03161691, 3350.00, 13, 2, '2025-07-05 04:30:44', '2025-07-05 04:31:31'),
(23, 0.00022022, 24.01, 3, 3, '2025-07-05 04:38:48', '2025-07-05 04:38:48'),
(24, 0.00471894, 514.55, 6, 3, '2025-07-05 04:38:48', '2025-07-05 04:38:48'),
(25, 0.00786490, 857.59, 7, 3, '2025-07-05 04:38:48', '2025-07-05 04:38:48'),
(26, 0.00117974, 128.63, 8, 3, '2025-07-05 04:38:48', '2025-07-05 04:38:48'),
(27, 0.02359471, 2572.78, 9, 3, '2025-07-05 04:38:48', '2025-07-05 04:38:48'),
(28, 0.00235947, 257.27, 11, 3, '2025-07-05 04:38:48', '2025-07-05 04:38:48'),
(29, 0.03161691, 3447.53, 13, 3, '2025-07-05 04:38:48', '2025-07-05 04:38:48'),
(30, 0.00033791, 35.12, 1, 4, '2025-07-05 04:42:38', '2025-07-05 04:43:37'),
(31, 0.00022959, 23.86, 3, 4, '2025-07-05 04:42:38', '2025-07-05 04:43:37'),
(32, 0.00483291, 502.40, 6, 4, '2025-07-05 04:42:38', '2025-07-05 04:43:37'),
(33, 0.00805488, 837.33, 7, 4, '2025-07-05 04:42:38', '2025-07-05 04:43:37'),
(34, 0.00120823, 125.60, 8, 4, '2025-07-05 04:42:38', '2025-07-05 04:43:37'),
(35, 0.02416464, 2512.01, 9, 4, '2025-07-05 04:42:38', '2025-07-05 04:43:37'),
(36, 0.00241645, 251.20, 11, 4, '2025-07-05 04:42:38', '2025-07-05 04:43:37'),
(37, 0.03239601, 3367.69, 13, 4, '2025-07-05 04:42:38', '2025-07-05 04:43:37'),
(46, 0.00033791, 35.83, 1, 5, '2025-07-05 04:48:06', '2025-07-05 04:48:06'),
(47, 0.00022959, 24.34, 3, 5, '2025-07-05 04:48:06', '2025-07-05 04:48:06'),
(48, 0.00483291, 512.50, 6, 5, '2025-07-05 04:48:06', '2025-07-05 04:48:06'),
(49, 0.00805488, 854.17, 7, 5, '2025-07-05 04:48:06', '2025-07-05 04:48:06'),
(50, 0.00120823, 128.12, 8, 5, '2025-07-05 04:48:06', '2025-07-05 04:48:06'),
(51, 0.02416464, 2562.51, 9, 5, '2025-07-05 04:48:06', '2025-07-05 04:48:06'),
(52, 0.00241645, 256.24, 11, 5, '2025-07-05 04:48:06', '2025-07-05 04:48:06'),
(53, 0.03239601, 3435.39, 13, 5, '2025-07-05 04:48:06', '2025-07-05 04:48:06'),
(54, 0.00057702, 59.98, 1, 6, '2025-07-05 04:53:29', '2025-07-05 04:54:17'),
(55, 0.00023308, 24.23, 3, 6, '2025-07-05 04:53:29', '2025-07-05 04:54:17'),
(56, 0.00484897, 504.07, 6, 6, '2025-07-05 04:53:29', '2025-07-05 04:54:17'),
(57, 0.00840228, 873.45, 7, 6, '2025-07-05 04:53:29', '2025-07-05 04:54:17'),
(58, 0.00121224, 126.01, 8, 6, '2025-07-05 04:53:29', '2025-07-05 04:54:17'),
(59, 0.02424484, 2520.34, 9, 6, '2025-07-05 04:53:29', '2025-07-05 04:54:17'),
(60, 0.00242447, 252.03, 11, 6, '2025-07-05 04:53:29', '2025-07-05 04:54:17'),
(61, 0.03251411, 3379.97, 13, 6, '2025-07-05 04:53:29', '2025-07-05 04:54:17'),
(62, 0.01603274, 1666.66, 31, 6, '2025-07-05 04:53:29', '2025-07-05 04:54:17'),
(72, 0.00057702, 60.61, 1, 7, '2025-07-05 04:58:58', '2025-07-05 04:58:58'),
(73, 0.00023308, 24.48, 3, 7, '2025-07-05 04:58:58', '2025-07-05 04:58:58'),
(74, 0.00484897, 509.35, 6, 7, '2025-07-05 04:58:58', '2025-07-05 04:58:58'),
(75, 0.00840228, 882.61, 7, 7, '2025-07-05 04:58:58', '2025-07-05 04:58:58'),
(76, 0.00121224, 127.33, 8, 7, '2025-07-05 04:58:58', '2025-07-05 04:58:58'),
(77, 0.02424484, 2546.79, 9, 7, '2025-07-05 04:58:58', '2025-07-05 04:58:58'),
(78, 0.00242447, 254.67, 11, 7, '2025-07-05 04:58:58', '2025-07-05 04:58:58'),
(79, 0.03251411, 3415.43, 13, 7, '2025-07-05 04:58:58', '2025-07-05 04:58:58'),
(80, 0.01603274, 1684.15, 31, 7, '2025-07-05 04:58:58', '2025-07-05 04:58:58'),
(81, 0.00454711, 509.44, 6, 8, '2025-07-05 05:47:37', '2025-07-05 05:47:37'),
(82, 0.00113678, 127.36, 8, 8, '2025-07-05 05:47:37', '2025-07-05 05:47:37'),
(83, 0.02273555, 2547.24, 9, 8, '2025-07-05 05:47:37', '2025-07-05 05:47:37'),
(84, 0.00227355, 254.72, 11, 8, '2025-07-05 05:47:37', '2025-07-05 05:47:37'),
(85, 0.00059486, 64.16, 1, 9, '2025-07-05 06:04:05', '2025-07-05 06:04:27'),
(86, 0.00021804, 23.51, 3, 9, '2025-07-05 06:04:05', '2025-07-05 06:04:27'),
(87, 0.00468095, 504.87, 6, 9, '2025-07-05 06:04:05', '2025-07-05 06:04:27'),
(88, 0.00608614, 656.43, 7, 9, '2025-07-05 06:04:05', '2025-07-05 06:04:27'),
(89, 0.00117027, 126.22, 8, 9, '2025-07-05 06:04:05', '2025-07-05 06:04:27'),
(90, 0.02340474, 2524.38, 9, 9, '2025-07-05 06:04:05', '2025-07-05 06:04:27'),
(91, 0.00234046, 252.43, 11, 9, '2025-07-05 06:04:05', '2025-07-05 06:04:27'),
(92, 0.02448023, 2640.38, 13, 9, '2025-07-05 06:04:05', '2025-07-05 06:04:27'),
(93, 0.01160942, 1252.16, 31, 9, '2025-07-05 06:04:05', '2025-07-05 06:04:27'),
(94, 0.04635731, 5000.00, 40, 9, '2025-07-05 06:04:05', '2025-07-05 06:04:27'),
(95, 0.00231787, 250.00, 43, 9, '2025-07-05 06:04:05', '2025-07-05 06:04:27'),
(107, 0.00059486, 65.22, 1, 10, '2025-07-05 06:08:54', '2025-07-05 06:08:54'),
(108, 0.00021804, 23.90, 3, 10, '2025-07-05 06:08:54', '2025-07-05 06:08:54'),
(109, 0.00468095, 513.22, 6, 10, '2025-07-05 06:08:54', '2025-07-05 06:08:54'),
(110, 0.00608614, 667.28, 7, 10, '2025-07-05 06:08:54', '2025-07-05 06:08:54'),
(111, 0.00117027, 128.30, 8, 10, '2025-07-05 06:08:54', '2025-07-05 06:08:54'),
(112, 0.02340474, 2566.10, 9, 10, '2025-07-05 06:08:54', '2025-07-05 06:08:54'),
(113, 0.00234046, 256.60, 11, 10, '2025-07-05 06:08:54', '2025-07-05 06:08:54'),
(114, 0.02448023, 2684.01, 13, 10, '2025-07-05 06:08:54', '2025-07-05 06:08:54'),
(115, 0.01160942, 1272.85, 31, 10, '2025-07-05 06:08:54', '2025-07-05 06:08:54'),
(116, 0.04635731, 5082.62, 40, 10, '2025-07-05 06:08:54', '2025-07-05 06:08:54'),
(117, 0.00231787, 254.13, 43, 10, '2025-07-05 06:08:54', '2025-07-05 06:08:54'),
(118, 0.00082786, 90.12, 1, 11, '2025-07-06 14:06:34', '2025-07-07 00:12:45'),
(119, 0.00021863, 23.80, 3, 11, '2025-07-06 14:06:34', '2025-07-07 00:13:04'),
(120, 0.00464739, 505.91, 6, 11, '2025-07-06 14:06:34', '2025-07-07 00:13:20'),
(121, 0.00604489, 658.04, 7, 11, '2025-07-06 14:06:34', '2025-07-07 00:13:38'),
(122, 0.00116187, 126.48, 8, 11, '2025-07-06 14:06:34', '2025-07-07 00:13:52'),
(123, 0.02323697, 2529.55, 9, 11, '2025-07-06 14:06:34', '2025-07-07 00:14:23'),
(124, 0.00232374, 252.96, 11, 11, '2025-07-06 14:06:34', '2025-07-07 00:14:38'),
(125, 0.02438901, 2654.96, 13, 11, '2025-07-06 14:06:34', '2025-07-07 00:14:55'),
(126, 0.01152621, 1254.73, 31, 11, '2025-07-06 14:06:34', '2025-07-07 00:15:09'),
(127, 0.04602501, 5010.23, 40, 11, '2025-07-06 14:06:34', '2025-07-07 00:15:24'),
(128, 0.00230124, 250.51, 43, 11, '2025-07-06 14:06:34', '2025-07-07 00:15:38'),
(129, 0.00344483, 375.00, 74, 11, '2025-07-06 14:06:34', '2025-07-07 00:15:51');

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
(7, 'asset_btc', '0.00000953');

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
(1, 1256233, 'Buy A', 109850.00, 1, 1, '36.83.150.207', 'no', 'filled', '2025-05-26 09:49:26', '2025-07-05 19:05:02'),
(2, 1264801, 'Buy B', 105850.00, 2, 1, '36.83.150.207', 'no', 'filled', '2025-05-30 08:30:02', '2025-07-05 19:05:28'),
(3, 1268192, 'Sell B', 109150.00, 2, 1, '36.83.150.207', 'no', 'filled', '2025-06-10 16:57:01', '2025-07-05 19:05:43'),
(4, 1271558, 'Buy B', 103850.00, 4, 1, '36.83.150.207', 'no', 'filled', '2025-06-15 04:07:56', '2025-07-05 19:05:57'),
(5, 1272951, 'Sell B', 106150.00, 4, 1, '36.83.150.207', 'no', 'filled', '2025-06-20 18:12:55', '2025-07-05 19:06:17'),
(6, 1276105, 'Buy B', 103850.00, 6, 1, '36.83.150.207', 'no', 'filled', '2025-06-21 16:59:45', '2025-07-05 19:06:31'),
(7, 1277675, 'Sell B', 105150.00, 6, 1, '36.83.150.207', 'no', 'filled', '2025-06-24 15:36:23', '2025-07-05 19:06:57'),
(8, 1294824, 'Sell A', 112150.00, 1, 1, '36.83.150.207', 'no', 'filled', '2025-06-25 17:45:56', '2025-07-05 19:07:15'),
(9, 1302001, 'Buy A', 107750.00, 9, 1, '36.83.150.207', 'no', 'filled', '2025-06-29 17:00:21', '2025-07-05 19:07:33'),
(10, 1303635, 'Sell A', 109750.00, 9, 1, '36.83.150.207', 'no', 'filled', '2025-06-29 17:20:41', '2025-07-05 19:07:45'),
(11, 45654497121, 'Buy A', 108750.00, NULL, 1, '103.175.212.90', 'no', 'filled', '2025-07-06 22:06:34', '2025-07-06 22:13:01'),
(12, 45654750941, 'Sell A', 111250.00, 11, 1, '103.175.212.81', 'no', 'pending', '2025-07-06 22:13:43', '2025-07-06 22:13:43');

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
(1, 0.34, 0.33, 3, 1268192, '2025-07-05 12:38:48'),
(2, 6.63, 7.20, 6, 1268192, '2025-07-05 12:38:48'),
(3, 11.05, 12.01, 7, 1268192, '2025-07-05 12:38:48'),
(4, 1.65, 1.80, 8, 1268192, '2025-07-05 12:38:48'),
(5, 33.15, 36.02, 9, 1268192, '2025-07-05 12:38:48'),
(6, 3.31, 3.60, 11, 1268192, '2025-07-05 12:38:48'),
(7, 49.25, 48.27, 13, 1268192, '2025-07-05 12:38:48'),
(8, 0.36, 0.35, 1, 1272951, '2025-07-05 12:48:06'),
(9, 0.24, 0.24, 3, 1272951, '2025-07-05 12:48:06'),
(10, 4.60, 4.99, 6, 1272951, '2025-07-05 12:48:06'),
(11, 7.67, 8.33, 7, 1272951, '2025-07-05 12:48:06'),
(12, 1.15, 1.25, 8, 1272951, '2025-07-05 12:48:06'),
(13, 23.00, 24.99, 9, 1272951, '2025-07-05 12:48:06'),
(14, 2.30, 2.49, 11, 1272951, '2025-07-05 12:48:06'),
(15, 34.19, 33.51, 13, 1272951, '2025-07-05 12:48:06'),
(16, 0.31, 0.31, 1, 1277675, '2025-07-05 12:58:58'),
(17, 0.12, 0.12, 3, 1277675, '2025-07-05 12:58:58'),
(18, 2.40, 2.61, 6, 1277675, '2025-07-05 12:58:58'),
(19, 4.17, 4.53, 7, 1277675, '2025-07-05 12:58:58'),
(20, 0.60, 0.65, 8, 1277675, '2025-07-05 12:58:58'),
(21, 12.05, 13.09, 9, 1277675, '2025-07-05 12:58:58'),
(22, 1.20, 1.31, 11, 1277675, '2025-07-05 12:58:58'),
(23, 17.91, 17.55, 13, 1277675, '2025-07-05 12:58:58'),
(24, 7.96, 8.66, 31, 1277675, '2025-07-05 12:58:58'),
(25, 4.30, 4.67, 6, 1294824, '2025-07-05 13:47:37'),
(26, 1.07, 1.16, 8, 1294824, '2025-07-05 13:47:37'),
(27, 21.51, 23.38, 9, 1294824, '2025-07-05 13:47:37'),
(28, 2.15, 2.33, 11, 1294824, '2025-07-05 13:47:37'),
(29, 0.53, 0.52, 1, 1303635, '2025-07-05 14:08:54'),
(30, 0.19, 0.19, 3, 1303635, '2025-07-05 14:08:54'),
(31, 3.80, 4.13, 6, 1303635, '2025-07-05 14:08:54'),
(32, 4.94, 5.37, 7, 1303635, '2025-07-05 14:08:54'),
(33, 0.95, 1.03, 8, 1303635, '2025-07-05 14:08:54'),
(34, 19.00, 20.65, 9, 1303635, '2025-07-05 14:08:54'),
(35, 1.90, 2.06, 11, 1303635, '2025-07-05 14:08:54'),
(36, 22.03, 21.60, 13, 1303635, '2025-07-05 14:08:54'),
(37, 9.42, 10.24, 31, 1303635, '2025-07-05 14:08:54'),
(38, 37.63, 40.90, 40, 1303635, '2025-07-05 14:08:54'),
(39, 1.88, 2.04, 43, 1303635, '2025-07-05 14:08:54');

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
(1, 6, 'usdt', 2000.000000, NULL, NULL, 'pending', 'trade', '2025-07-04 14:05:18', NULL, NULL),
(2, 8, 'usdt', 500.000000, NULL, NULL, 'pending', 'trade', '2025-07-04 14:05:18', NULL, NULL),
(3, 9, 'usdt', 10000.000000, NULL, NULL, 'pending', 'trade', '2025-07-04 14:05:18', NULL, NULL),
(4, 11, 'usdt', 1000.000000, NULL, NULL, 'pending', 'trade', '2025-07-04 14:05:18', NULL, NULL),
(5, 3, 'usdt', 70.000000, NULL, NULL, 'pending', 'comission', '2025-07-04 14:05:18', NULL, NULL),
(6, 3, 'usdt', 70.000000, NULL, NULL, 'pending', 'trade', '2025-07-04 14:05:18', NULL, NULL),
(7, 7, 'usdt', 2500.000000, NULL, NULL, 'pending', 'trade', '2025-07-04 14:16:24', NULL, NULL),
(8, 13, 'usdt', 10000.000000, NULL, NULL, 'pending', 'trade', '2025-07-04 14:16:24', NULL, NULL),
(9, 13, 'usdt', 50.000000, NULL, NULL, 'pending', 'comission', '2025-07-04 14:05:18', NULL, NULL),
(10, 13, 'usdt', 50.000000, NULL, NULL, 'pending', 'trade', '2025-07-04 14:05:18', NULL, NULL),
(11, 3, 'usdt', 0.720000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:38:48', NULL, NULL),
(12, 3, 'usdt', 0.720000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:38:48', NULL, NULL),
(13, 13, 'usdt', 1.200000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:38:48', NULL, NULL),
(14, 13, 'usdt', 1.200000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:38:48', NULL, NULL),
(15, 3, 'usdt', 0.180000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:38:48', NULL, NULL),
(16, 3, 'usdt', 0.180000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:38:48', NULL, NULL),
(17, 13, 'usdt', 3.600000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:38:48', NULL, NULL),
(18, 13, 'usdt', 3.600000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:38:48', NULL, NULL),
(19, 3, 'usdt', 0.360000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:38:48', NULL, NULL),
(20, 3, 'usdt', 0.360000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:38:48', NULL, NULL),
(21, 3, 'usdt', 0.490000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:48:06', NULL, NULL),
(22, 3, 'usdt', 0.490000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:48:06', NULL, NULL),
(23, 13, 'usdt', 0.830000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:48:06', NULL, NULL),
(24, 13, 'usdt', 0.830000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:48:06', NULL, NULL),
(25, 3, 'usdt', 0.120000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:48:06', NULL, NULL),
(26, 3, 'usdt', 0.120000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:48:06', NULL, NULL),
(27, 13, 'usdt', 2.490000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:48:06', NULL, NULL),
(28, 13, 'usdt', 2.490000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:48:06', NULL, NULL),
(29, 3, 'usdt', 0.240000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:48:06', NULL, NULL),
(30, 3, 'usdt', 0.240000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:48:06', NULL, NULL),
(31, 31, 'usdt', 5000.000000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:51:14', NULL, NULL),
(32, 7, 'usdt', 100.000000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:48:06', NULL, NULL),
(33, 7, 'usdt', 100.000000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:48:06', NULL, NULL),
(34, 3, 'usdt', 0.260000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:58:58', NULL, NULL),
(35, 3, 'usdt', 0.260000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:58:58', NULL, NULL),
(36, 13, 'usdt', 0.450000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:58:58', NULL, NULL),
(37, 13, 'usdt', 0.450000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:58:58', NULL, NULL),
(38, 3, 'usdt', 0.060000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:58:58', NULL, NULL),
(39, 3, 'usdt', 0.060000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:58:58', NULL, NULL),
(40, 13, 'usdt', 1.300000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:58:58', NULL, NULL),
(41, 13, 'usdt', 1.300000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:58:58', NULL, NULL),
(42, 3, 'usdt', 0.130000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:58:58', NULL, NULL),
(43, 3, 'usdt', 0.130000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:58:58', NULL, NULL),
(44, 7, 'usdt', 0.860000, NULL, NULL, 'pending', 'comission', '2025-07-05 12:58:58', NULL, NULL),
(45, 7, 'usdt', 0.860000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:58:58', NULL, NULL),
(46, 3, 'usdt', 0.460000, NULL, NULL, 'pending', 'comission', '2025-07-05 13:47:37', NULL, NULL),
(47, 3, 'usdt', 0.460000, NULL, NULL, 'pending', 'trade', '2025-07-05 13:47:37', NULL, NULL),
(48, 3, 'usdt', 0.110000, NULL, NULL, 'pending', 'comission', '2025-07-05 13:47:37', NULL, NULL),
(49, 3, 'usdt', 0.110000, NULL, NULL, 'pending', 'trade', '2025-07-05 13:47:37', NULL, NULL),
(50, 13, 'usdt', 2.330000, NULL, NULL, 'pending', 'comission', '2025-07-05 13:47:37', NULL, NULL),
(51, 13, 'usdt', 2.330000, NULL, NULL, 'pending', 'trade', '2025-07-05 13:47:37', NULL, NULL),
(52, 3, 'usdt', 0.230000, NULL, NULL, 'pending', 'comission', '2025-07-05 13:47:37', NULL, NULL),
(53, 3, 'usdt', 0.230000, NULL, NULL, 'pending', 'trade', '2025-07-05 13:47:37', NULL, NULL),
(54, 40, 'usdt', 20000.000000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:51:14', NULL, NULL),
(55, 43, 'usdt', 1000.000000, NULL, NULL, 'pending', 'trade', '2025-07-05 12:51:14', NULL, NULL),
(56, 13, 'usdt', 400.000000, NULL, NULL, 'pending', 'comission', '2025-07-05 13:47:37', NULL, NULL),
(57, 13, 'usdt', 400.000000, NULL, NULL, 'pending', 'trade', '2025-07-05 13:47:37', NULL, NULL),
(58, 3, 'usdt', 20.000000, NULL, NULL, 'pending', 'comission', '2025-07-05 13:47:37', NULL, NULL),
(59, 3, 'usdt', 20.000000, NULL, NULL, 'pending', 'trade', '2025-07-05 13:47:37', NULL, NULL),
(60, 3, 'usdt', 0.410000, NULL, NULL, 'pending', 'comission', '2025-07-05 14:08:54', NULL, NULL),
(61, 3, 'usdt', 0.410000, NULL, NULL, 'pending', 'trade', '2025-07-05 14:08:54', NULL, NULL),
(62, 13, 'usdt', 0.530000, NULL, NULL, 'pending', 'comission', '2025-07-05 14:08:54', NULL, NULL),
(63, 13, 'usdt', 0.530000, NULL, NULL, 'pending', 'trade', '2025-07-05 14:08:54', NULL, NULL),
(64, 3, 'usdt', 0.100000, NULL, NULL, 'pending', 'comission', '2025-07-05 14:08:54', NULL, NULL),
(65, 3, 'usdt', 0.100000, NULL, NULL, 'pending', 'trade', '2025-07-05 14:08:54', NULL, NULL),
(66, 13, 'usdt', 2.060000, NULL, NULL, 'pending', 'comission', '2025-07-05 14:08:54', NULL, NULL),
(67, 13, 'usdt', 2.060000, NULL, NULL, 'pending', 'trade', '2025-07-05 14:08:54', NULL, NULL),
(68, 3, 'usdt', 0.200000, NULL, NULL, 'pending', 'comission', '2025-07-05 14:08:54', NULL, NULL),
(69, 3, 'usdt', 0.200000, NULL, NULL, 'pending', 'trade', '2025-07-05 14:08:54', NULL, NULL),
(70, 7, 'usdt', 1.020000, NULL, NULL, 'pending', 'comission', '2025-07-05 14:08:54', NULL, NULL),
(71, 7, 'usdt', 1.020000, NULL, NULL, 'pending', 'trade', '2025-07-05 14:08:54', NULL, NULL),
(72, 13, 'usdt', 4.090000, NULL, NULL, 'pending', 'comission', '2025-07-05 14:08:54', NULL, NULL),
(73, 13, 'usdt', 4.090000, NULL, NULL, 'pending', 'trade', '2025-07-05 14:08:54', NULL, NULL),
(74, 3, 'usdt', 0.200000, NULL, NULL, 'pending', 'comission', '2025-07-05 14:08:54', NULL, NULL),
(75, 3, 'usdt', 0.200000, NULL, NULL, 'pending', 'trade', '2025-07-05 14:08:54', NULL, NULL),
(76, 13, 'usdt', 30.000000, NULL, NULL, 'pending', 'comission', '2025-07-06 11:36:09', NULL, NULL),
(77, 13, 'usdt', 30.000000, NULL, NULL, 'pending', 'trade', '2025-07-06 11:36:09', NULL, NULL),
(78, 74, 'usdt', 1500.000000, NULL, NULL, 'pending', 'trade', '2025-07-06 11:41:25', NULL, NULL);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `member_commission`
--
ALTER TABLE `member_commission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `member_deposit`
--
ALTER TABLE `member_deposit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `member_sinyal`
--
ALTER TABLE `member_sinyal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `wallet`
--
ALTER TABLE `wallet`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `withdraw`
--
ALTER TABLE `withdraw`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

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

/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.7.2-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: elite
-- ------------------------------------------------------
-- Server version	11.7.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `passwd` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `refcode` varchar(20) DEFAULT NULL,
  `id_referral` int(11) DEFAULT NULL,
  `status` enum('new','active','disabled','referral') NOT NULL DEFAULT 'new',
  `timezone` varchar(50) NOT NULL,
  `otp` char(4) DEFAULT NULL,
  `role` enum('member','admin','referral','manager','superadmin') NOT NULL DEFAULT 'member',
  `ip_addr` varchar(45) DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `refcode` (`refcode`),
  KEY `id_referral` (`id_referral`),
  CONSTRAINT `member_ibfk_1` FOREIGN KEY (`id_referral`) REFERENCES `member` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` VALUES
(1,'yisayi7090@macho3.com','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-04-01 03:03:34','2025-04-14 02:39:26','0mfk32m4',12,'active','Asia/Singapore',NULL,'member','180.254.224.15',0),
(12,'a@a.a','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-04-01 03:03:34','2025-04-14 02:41:48','0mfk32m5',1,'referral','Asia/Singapore',NULL,'member','180.254.224.15',0);
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_commission`
--

DROP TABLE IF EXISTS `member_commission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_commission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `downline_id` int(11) NOT NULL,
  `amount` decimal(10,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `downline_id` (`downline_id`),
  CONSTRAINT `member_commission_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  CONSTRAINT `member_commission_ibfk_2` FOREIGN KEY (`downline_id`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_amount_positive` CHECK (`amount` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_commission`
--

LOCK TABLES `member_commission` WRITE;
/*!40000 ALTER TABLE `member_commission` DISABLE KEYS */;
/*!40000 ALTER TABLE `member_commission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_deposit`
--

DROP TABLE IF EXISTS `member_deposit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_deposit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice` varchar(255) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `commission` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('pending','complete','failed') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `member_deposit_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_deposit`
--

LOCK TABLES `member_deposit` WRITE;
/*!40000 ALTER TABLE `member_deposit` DISABLE KEYS */;
INSERT INTO `member_deposit` VALUES
(5,'INV-300F2CF1',1,5000.00,50.00,'2025-04-14 05:35:14','complete'),
(6,'INV-B746C393',12,4000.00,40.00,'2025-04-14 05:35:40','complete');
/*!40000 ALTER TABLE `member_deposit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_sinyal`
--

DROP TABLE IF EXISTS `member_sinyal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_sinyal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount_btc` decimal(16,6) NOT NULL,
  `amount_usdt` decimal(16,2) NOT NULL DEFAULT 0.00,
  `member_id` int(11) DEFAULT NULL,
  `sinyal_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_member_sinyal_member` (`member_id`),
  KEY `fk_member_sinyal_sinyal` (`sinyal_id`),
  CONSTRAINT `fk_member_sinyal_member` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_member_sinyal_sinyal` FOREIGN KEY (`sinyal_id`) REFERENCES `sinyal` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_sinyal`
--

LOCK TABLES `member_sinyal` WRITE;
/*!40000 ALTER TABLE `member_sinyal` DISABLE KEYS */;
INSERT INTO `member_sinyal` VALUES
(136,0.017855,0.00,1,171,'2025-04-16 04:41:56','2025-04-16 04:41:56'),
(137,0.014284,0.00,12,171,'2025-04-16 04:41:56','2025-04-16 04:41:56'),
(138,0.017855,0.00,1,172,'2025-04-16 04:47:41','2025-04-16 04:47:41'),
(139,0.014284,0.00,12,172,'2025-04-16 04:47:41','2025-04-16 04:47:41'),
(140,0.017855,0.00,1,173,'2025-04-16 04:56:42','2025-04-16 04:56:42'),
(141,0.014284,0.00,12,173,'2025-04-16 04:56:42','2025-04-16 04:56:42'),
(142,0.017855,0.00,1,174,'2025-04-16 05:05:52','2025-04-16 05:05:52'),
(143,0.014284,0.00,12,174,'2025-04-16 05:05:52','2025-04-16 05:05:52'),
(144,0.016661,0.00,1,175,'2025-04-16 05:07:51','2025-04-16 05:07:51'),
(145,0.013328,0.00,12,175,'2025-04-16 05:07:51','2025-04-16 05:07:51'),
(146,0.014200,1186.84,1,176,'2025-04-16 06:42:11','2025-04-16 06:42:11'),
(147,0.011359,949.47,12,176,'2025-04-16 06:42:11','2025-04-16 06:42:11'),
(148,0.014705,1230.64,1,177,'2025-04-16 06:57:53','2025-04-16 06:57:53'),
(149,0.011764,984.51,12,177,'2025-04-16 06:57:53','2025-04-16 06:57:53'),
(150,0.014705,1228.67,1,181,'2025-04-16 07:06:37','2025-04-16 07:06:37'),
(151,0.011764,982.93,12,181,'2025-04-16 07:06:37','2025-04-16 07:06:37'),
(152,0.014705,1228.65,1,182,'2025-04-16 07:07:00','2025-04-16 07:07:00'),
(153,0.011764,982.92,12,182,'2025-04-16 07:07:00','2025-04-16 07:07:00'),
(154,0.014705,1228.74,1,183,'2025-04-16 07:07:18','2025-04-16 07:07:18'),
(155,0.011764,982.99,12,183,'2025-04-16 07:07:18','2025-04-16 07:07:18'),
(156,0.014700,1227.55,1,188,'2025-04-16 07:17:32','2025-04-16 07:17:32'),
(157,0.011760,982.04,12,188,'2025-04-16 07:17:32','2025-04-16 07:17:32'),
(158,0.013884,1157.78,1,192,'2025-04-16 07:36:27','2025-04-16 07:36:27'),
(159,0.011107,926.23,12,192,'2025-04-16 07:36:27','2025-04-16 07:36:27'),
(160,0.017849,0.00,1,193,'2025-04-16 07:37:50','2025-04-16 07:37:50'),
(161,0.014279,0.00,12,193,'2025-04-16 07:37:50','2025-04-16 07:37:50'),
(162,0.014195,1189.67,1,194,'2025-04-16 10:16:51','2025-04-16 10:16:51'),
(163,0.011356,951.74,12,194,'2025-04-16 10:16:51','2025-04-16 10:16:51');
/*!40000 ALTER TABLE `member_sinyal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proxies`
--

DROP TABLE IF EXISTS `proxies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proxies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `port` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proxies`
--

LOCK TABLES `proxies` WRITE;
/*!40000 ALTER TABLE `proxies` DISABLE KEYS */;
INSERT INTO `proxies` VALUES
(1,'38.154.227.167',5868,'brwbdrjy','4mqmjsgb0t3i',NULL),
(2,'38.153.152.244',9594,'brwbdrjy','4mqmjsgb0t3i',NULL),
(3,'173.211.0.148',6641,'brwbdrjy','4mqmjsgb0t3i',NULL),
(4,'86.38.234.176',6630,'brwbdrjy','4mqmjsgb0t3i',NULL),
(5,'161.123.152.115',6360,'brwbdrjy','4mqmjsgb0t3i',NULL),
(6,'23.94.138.75',6349,'brwbdrjy','4mqmjsgb0t3i',NULL),
(7,'64.64.118.149',6732,'brwbdrjy','4mqmjsgb0t3i',NULL),
(8,'198.105.101.92',5721,'brwbdrjy','4mqmjsgb0t3i',NULL),
(9,'166.88.58.10',5735,'brwbdrjy','4mqmjsgb0t3i',NULL),
(10,'45.151.162.198',6600,'brwbdrjy','4mqmjsgb0t3i',NULL);
/*!40000 ALTER TABLE `proxies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES
(3,'price','2000'),
(4,'cost','0.005'),
(5,'referral_fee','0.01');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sinyal`
--

DROP TABLE IF EXISTS `sinyal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sinyal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) DEFAULT NULL,
  `type` enum('Buy A','Buy B','Buy C','Buy D','Sell A','Sell B','Sell C','Sell D') NOT NULL DEFAULT 'Buy A',
  `entry_price` decimal(10,2) NOT NULL,
  `pair_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `ip_addr` varchar(45) DEFAULT NULL,
  `is_deleted` enum('no','yes') NOT NULL DEFAULT 'no',
  `status` enum('pending','filled','canceled') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `fk_admin` (`admin_id`),
  CONSTRAINT `fk_admin` FOREIGN KEY (`admin_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sinyal`
--

LOCK TABLES `sinyal` WRITE;
/*!40000 ALTER TABLE `sinyal` DISABLE KEYS */;
INSERT INTO `sinyal` VALUES
(171,3828990,'Buy A',70000.00,NULL,12,'::1','yes','canceled','2025-04-16 11:41:56','2025-04-16 11:47:25'),
(172,3829747,'Buy A',70000.00,NULL,12,'::1','yes','canceled','2025-04-16 11:47:41','2025-04-16 11:56:35'),
(173,3831107,'Buy A',70000.00,NULL,12,'::1','yes','canceled','2025-04-16 11:56:42','2025-04-16 12:05:41'),
(174,3833075,'Buy A',70000.00,NULL,12,'::1','yes','canceled','2025-04-16 12:05:52','2025-04-16 12:07:44'),
(175,3833495,'Buy A',75000.00,NULL,12,'::1','yes','canceled','2025-04-16 12:07:51','2025-04-16 13:01:27'),
(176,3850615,'Buy A',88000.00,176,12,'::1','no','filled','2025-04-16 13:42:11','2025-04-16 13:44:16'),
(177,3854340,'Buy A',85000.00,177,12,'::1','no','filled','2025-04-16 13:57:53','2025-04-16 14:08:55'),
(178,3854673,'Sell A',90000.00,177,12,'::1','yes','canceled','2025-04-16 13:59:48','2025-04-16 14:03:36'),
(179,3855256,'Sell A',90000.00,177,12,'::1','yes','canceled','2025-04-16 14:03:45','2025-04-16 14:04:26'),
(180,3855424,'Sell A',90000.00,177,12,'::1','yes','canceled','2025-04-16 14:04:44','2025-04-16 14:05:08'),
(181,3855609,'Buy B',85000.00,181,12,'::1','no','filled','2025-04-16 14:06:37','2025-04-16 14:08:55'),
(182,3855701,'Buy C',85000.00,182,12,'::1','no','filled','2025-04-16 14:07:00','2025-04-16 14:08:55'),
(183,3855773,'Buy D',85000.00,183,12,'::1','no','filled','2025-04-16 14:07:18','2025-04-16 14:08:55'),
(184,3855929,'Sell A',80000.00,177,12,'::1','no','filled','2025-04-16 14:07:46','2025-04-16 14:08:55'),
(185,3855947,'Sell B',80000.00,181,12,'::1','no','filled','2025-04-16 14:07:52','2025-04-16 14:08:55'),
(186,3855964,'Sell C',80000.00,182,12,'::1','no','filled','2025-04-16 14:07:56','2025-04-16 14:08:55'),
(187,3855980,'Sell D',80000.00,183,12,'::1','no','filled','2025-04-16 14:08:01','2025-04-16 14:08:55'),
(188,3858266,'Buy A',85000.00,NULL,12,'::1','no','filled','2025-04-16 14:17:32','2025-04-16 14:17:53'),
(189,3858349,'Sell A',90000.00,188,12,'::1','yes','canceled','2025-04-16 14:18:09','2025-04-16 14:19:23'),
(190,3860339,'Sell A',90000.00,188,12,'::1','yes','canceled','2025-04-16 14:31:38','2025-04-16 14:32:07'),
(191,3860696,'Sell A',90000.00,188,12,'::1','yes','canceled','2025-04-16 14:35:01','2025-04-16 14:35:19'),
(192,3860926,'Buy B',90000.00,NULL,12,'::1','no','filled','2025-04-16 14:36:27','2025-04-16 14:37:15'),
(193,3861009,'Buy C',70000.00,NULL,12,'::1','yes','canceled','2025-04-16 14:37:50','2025-04-16 14:39:10'),
(194,3891068,'Buy C',88000.00,NULL,12,'::1','no','filled','2025-04-16 17:16:51','2025-04-16 17:32:39');
/*!40000 ALTER TABLE `sinyal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wallet`
--

DROP TABLE IF EXISTS `wallet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `master_wallet` decimal(20,2) NOT NULL DEFAULT 0.00,
  `client_wallet` decimal(20,2) NOT NULL DEFAULT 0.00,
  `member_id` int(11) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `wallet_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`),
  CONSTRAINT `wallet_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `sinyal` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wallet`
--

LOCK TABLES `wallet` WRITE;
/*!40000 ALTER TABLE `wallet` DISABLE KEYS */;
INSERT INTO `wallet` VALUES
(60,-1.06,-1.06,1,3855929),
(61,-0.85,-0.85,12,3855929),
(62,-0.18,-0.18,1,3855947),
(63,-0.14,-0.14,12,3855947),
(64,-0.17,-0.17,1,3855964),
(65,-0.14,-0.14,12,3855964),
(66,-0.21,-0.21,1,3855980),
(67,-0.17,-0.17,12,3855980);
/*!40000 ALTER TABLE `wallet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `withdraw`
--

DROP TABLE IF EXISTS `withdraw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `withdraw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `withdraw_type` enum('fiat','usdt','btc','usdc') NOT NULL DEFAULT 'fiat',
  `amount` decimal(18,2) NOT NULL,
  `payment_details` text DEFAULT NULL,
  `wallet_address` varchar(255) DEFAULT NULL,
  `status` enum('pending','rejected','completed') NOT NULL DEFAULT 'pending',
  `jenis` enum('trade','withdraw','balance') NOT NULL DEFAULT 'withdraw',
  `requested_at` datetime NOT NULL DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `withdraw_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdraw`
--

LOCK TABLES `withdraw` WRITE;
/*!40000 ALTER TABLE `withdraw` DISABLE KEYS */;
INSERT INTO `withdraw` VALUES
(97,12,'usdt',1500.00,NULL,NULL,'pending','trade','2025-04-10 17:09:46',NULL,NULL),
(98,12,'fiat',500.00,NULL,NULL,'pending','balance','2025-04-10 17:09:59',NULL,NULL),
(99,12,'fiat',500.00,NULL,NULL,'pending','balance','2025-04-10 17:32:43',NULL,NULL),
(100,12,'fiat',500.00,NULL,NULL,'pending','balance','2025-04-11 09:10:29',NULL,NULL),
(101,12,'usdt',1000.00,NULL,NULL,'pending','trade','2025-04-11 09:10:43',NULL,NULL),
(103,12,'fiat',1000.00,NULL,NULL,'pending','balance','2025-04-11 09:26:12',NULL,NULL),
(104,12,'usdt',20.00,NULL,NULL,'pending','trade','2025-04-11 09:28:25',NULL,NULL),
(106,12,'fiat',20.00,NULL,NULL,'pending','balance','2025-04-11 09:28:53',NULL,NULL),
(107,12,'usdt',500.00,NULL,NULL,'pending','trade','2025-04-11 09:46:52',NULL,NULL),
(121,12,'usdc',500.00,'{\"recipient\":null,\"routing_number\":null,\"account_type\":null,\"swift_code\":null,\"address\":null,\"network\":\"BLOCKCHAIN\"}','lorem9999999999','pending','withdraw','2025-04-11 15:15:37',NULL,NULL);
/*!40000 ALTER TABLE `withdraw` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-04-16 18:12:49

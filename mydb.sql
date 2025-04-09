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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` VALUES
(1,'yisayi7090@macho3.com','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-04-01 03:03:34','2025-04-04 23:48:54','0mfk32m4',NULL,'active','Asia/Singapore',NULL,'member','180.254.224.15',0),
(2,'a@a.a','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-04-01 03:03:34','2025-04-09 02:49:40','0mfk32m3',1,'referral','Asia/Singapore',NULL,'member','180.254.224.15',0);
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
  `upline_id` int(11) NOT NULL,
  `amount` decimal(10,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `downline_id` (`upline_id`),
  CONSTRAINT `member_commission_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  CONSTRAINT `member_commission_ibfk_2` FOREIGN KEY (`upline_id`) REFERENCES `member` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_commission`
--

LOCK TABLES `member_commission` WRITE;
/*!40000 ALTER TABLE `member_commission` DISABLE KEYS */;
INSERT INTO `member_commission` VALUES
(1,2,1,900.0000,'2025-04-07 03:03:34');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_deposit`
--

LOCK TABLES `member_deposit` WRITE;
/*!40000 ALTER TABLE `member_deposit` DISABLE KEYS */;
INSERT INTO `member_deposit` VALUES
(1,'INV-123919',1,10000.00,100.00,'2025-04-02 15:35:46','complete'),
(2,'INV-CED750AC',2,2000.00,0.00,'2025-04-06 15:07:14','complete');
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
  `member_id` int(11) DEFAULT NULL,
  `sinyal_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_member_sinyal_member` (`member_id`),
  KEY `fk_member_sinyal_sinyal` (`sinyal_id`),
  CONSTRAINT `fk_member_sinyal_member` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_member_sinyal_sinyal` FOREIGN KEY (`sinyal_id`) REFERENCES `sinyal` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_sinyal`
--

LOCK TABLES `member_sinyal` WRITE;
/*!40000 ALTER TABLE `member_sinyal` DISABLE KEYS */;
INSERT INTO `member_sinyal` VALUES
(26,0.000290,143,50,'2025-03-28 09:16:08','2025-03-28 09:16:08'),
(27,0.000725,144,50,'2025-03-28 09:16:08','2025-03-28 09:16:08'),
(28,0.000243,143,52,'2025-03-28 15:05:53','2025-03-28 15:05:53'),
(29,0.000606,144,52,'2025-03-28 15:05:53','2025-03-28 15:05:53'),
(30,0.000243,143,53,'2025-03-28 15:12:09','2025-03-28 15:12:09'),
(31,0.000606,144,53,'2025-03-28 15:12:09','2025-03-28 15:12:09');
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
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sinyal`
--

LOCK TABLES `sinyal` WRITE;
/*!40000 ALTER TABLE `sinyal` DISABLE KEYS */;
INSERT INTO `sinyal` VALUES
(50,11016135,'Buy A',75000.00,NULL,143,'127.0.0.1','yes','pending','2025-03-28 16:16:08','2025-03-28 22:05:34'),
(52,11125241,'Buy A',90000.00,NULL,143,'127.0.0.1','no','filled','2025-03-28 22:05:53','2025-03-28 22:56:28'),
(53,11127872,'Buy B',90000.00,NULL,143,'127.0.0.1','no','filled','2025-03-28 22:12:09','2025-03-30 15:59:11'),
(57,11418901,'Sell A',80000.00,52,143,'127.0.0.1','no','filled','2025-03-29 13:39:06','2025-03-30 16:02:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wallet`
--

LOCK TABLES `wallet` WRITE;
/*!40000 ALTER TABLE `wallet` DISABLE KEYS */;
INSERT INTO `wallet` VALUES
(1,7.92,7.92,1,11418901),
(2,19.79,19.79,144,11418901);
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
  `withdraw_type` enum('fiat','usdt','btc') NOT NULL DEFAULT 'fiat',
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
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdraw`
--

LOCK TABLES `withdraw` WRITE;
/*!40000 ALTER TABLE `withdraw` DISABLE KEYS */;
INSERT INTO `withdraw` VALUES
(41,1,'usdt',10.00,NULL,NULL,'pending','withdraw','2025-04-02 23:36:41',NULL,NULL),
(66,1,'usdt',990.00,NULL,NULL,'pending','balance','2025-04-08 09:43:38',NULL,NULL);
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

-- Dump completed on 2025-04-09 16:40:45

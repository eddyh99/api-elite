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
  `status` enum('new','active','disabled') NOT NULL DEFAULT 'new',
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
(143,'member@gmail.com','d033e22ae348aeb5660fc2140aec35850c4da997','2025-03-27 15:37:11','2025-03-27 08:37:32','xrwh4zyj',NULL,'active','Asia/Jakarta',NULL,'member','127.0.0.1',0);
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_role`
--

DROP TABLE IF EXISTS `member_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `access` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`access`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_member_id` (`member_id`),
  UNIQUE KEY `unique_alias` (`alias`),
  CONSTRAINT `member_role_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_role`
--

LOCK TABLES `member_role` WRITE;
/*!40000 ALTER TABLE `member_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `member_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_sinyal`
--

DROP TABLE IF EXISTS `member_sinyal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_sinyal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_sinyal`
--

LOCK TABLES `member_sinyal` WRITE;
/*!40000 ALTER TABLE `member_sinyal` DISABLE KEYS */;
/*!40000 ALTER TABLE `member_sinyal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter`
--

DROP TABLE IF EXISTS `newsletter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter`
--

LOCK TABLES `newsletter` WRITE;
/*!40000 ALTER TABLE `newsletter` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletter` ENABLE KEYS */;
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
  `port` int(11) NOT NULL CHECK (`port` between 1 and 65535),
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
(3,'price','5000'),
(4,'commission','0.7'),
(5,'referral_fee','0.1');
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
  KEY `fk_admin` (`admin_id`),
  CONSTRAINT `fk_admin` FOREIGN KEY (`admin_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sinyal`
--

LOCK TABLES `sinyal` WRITE;
/*!40000 ALTER TABLE `sinyal` DISABLE KEYS */;
/*!40000 ALTER TABLE `sinyal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription`
--

DROP TABLE IF EXISTS `subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('active','expired','free','pending') NOT NULL,
  `is_admin_granted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `initial_capital` int(11) NOT NULL,
  `commission` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscription_ibfk_1` (`member_id`),
  CONSTRAINT `subscription_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription`
--

LOCK TABLES `subscription` WRITE;
/*!40000 ALTER TABLE `subscription` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscription` ENABLE KEYS */;
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
  `withdraw_type` enum('fiat','usdt') NOT NULL DEFAULT 'fiat',
  `amount` decimal(18,2) NOT NULL,
  `payment_details` text DEFAULT NULL,
  `wallet_address` varchar(255) DEFAULT NULL,
  `status` enum('pending','rejected','completed') NOT NULL DEFAULT 'pending',
  `requested_at` datetime NOT NULL DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `withdraw_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdraw`
--

LOCK TABLES `withdraw` WRITE;
/*!40000 ALTER TABLE `withdraw` DISABLE KEYS */;
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

-- Dump completed on 2025-03-27 22:38:18

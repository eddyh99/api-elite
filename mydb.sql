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
  `position_a` decimal(20,4) NOT NULL DEFAULT 0.0000,
  `position_b` decimal(20,4) NOT NULL DEFAULT 0.0000,
  `position_c` decimal(20,4) NOT NULL DEFAULT 0.0000,
  `position_d` decimal(20,4) NOT NULL DEFAULT 0.0000,
  `ip_addr` varchar(45) DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `refcode` (`refcode`),
  KEY `id_referral` (`id_referral`),
  CONSTRAINT `member_ibfk_1` FOREIGN KEY (`id_referral`) REFERENCES `member` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` VALUES
(1,'a@a.a','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-05-16 15:34:43','2025-06-30 03:53:59',NULL,NULL,'active','',NULL,'superadmin',0.0000,0.0000,0.0000,0.0000,NULL,0),
(2,'dilame3476@deusa7.com','7c222fb2927d828af22f592134e8932480637c0d','2025-05-16 16:02:38','2025-05-16 08:04:08',NULL,NULL,'active','Asia/Makassar',NULL,'member',0.0000,0.0000,0.0000,0.0000,'182.253.116.2',0),
(3,'hafidelamranijoutey2@gmail.com','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-05-18 10:09:19','2025-06-26 14:41:58','h4f1d',NULL,'active','Asia/Makassar',NULL,'referral',0.0000,2.8125,0.0000,0.0000,'103.24.150.159',0),
(4,'samuelegranocchia@gmail.com_2025-05-23','2b8b0868102df8d9bc9bea937c3bd96a5a4e7146','2025-05-19 09:13:04','2025-05-26 02:18:57',NULL,NULL,'new','Europe/Rome','2047','member',0.0000,0.0000,0.0000,0.0000,'62.211.27.92',1),
(6,'brio21569@gmail.com','a338fc407b2199b042afe64bcdb0d8e419822c29','2025-05-19 09:16:20','2025-06-25 04:29:05',NULL,3,'active','Europe/Rome',NULL,'member',500.0000,504.9367,0.0000,0.0000,'62.211.27.92',0),
(7,'danieldocooh@gmail.com','5d9a94b24b414bec2225463d03fd04c04f1aa466','2025-05-19 09:25:24','2025-06-25 04:29:05','r3b3cc4',13,'active','Asia/Shanghai',NULL,'referral',625.0000,655.5325,0.0000,0.0000,'103.175.212.66',0),
(8,'ssilenziog@gmail.com','78f2d37fb951d3456c35b096ba5511eeaa0f73fe','2025-05-19 10:38:12','2025-06-30 03:53:17',NULL,3,'active','Europe/Rome',NULL,'superadmin',125.0000,126.0467,0.0000,0.0000,'217.202.8.52',0),
(9,'3a3aj4g4@gmail.com','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-05-20 00:53:33','2025-06-26 14:41:19',NULL,13,'active','Asia/Shanghai',NULL,'member',2500.0000,2525.5500,0.0000,0.0000,'103.175.212.89',0),
(10,'lisette.paula8899@gmail.com','7c222fb2927d828af22f592134e8932480637c0d','2025-05-20 05:25:54','2025-06-02 04:27:01','p4ul4',NULL,'active','Asia/Makassar',NULL,'referral',0.0000,0.0000,0.0000,0.0000,'110.139.176.94',0),
(11,'maci81x@hotmail.it','0d296436b80bc54f847035d231af30e72624530d','2025-05-23 12:20:09','2025-06-25 04:29:05','zzhr34o5',3,'active','Europe/Rome',NULL,'member',250.0000,252.3567,0.0000,0.0000,'213.243.250.56',0),
(12,'stefano.giovagnoli1234@gmail.com','7f838487959c746237accb0dc2b5848679221fab','2025-05-23 15:11:23','2025-05-23 07:13:06','poi6v814',NULL,'active','Europe/Rome',NULL,'member',0.0000,0.0000,0.0000,0.0000,'128.116.239.58',0),
(13,'principe.nerini@gmail.com','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-05-26 01:08:02','2025-06-26 12:07:01','69spoj50',NULL,'active','Asia/Singapore',NULL,'referral',2500.0000,2522.7675,0.0000,0.0000,'59.153.130.103',0),
(14,'aymanezza44@gmail.com_2025-06-21','9ded1e71d9a28c6ac2ad51b229d9df8a4c92b2e8','2025-06-06 09:40:49','2025-06-20 22:52:04',NULL,NULL,'new','Europe/Rome','7953','member',0.0000,0.0000,0.0000,0.0000,'46.149.102.19',1),
(16,'ezzuzzu100@gmail.com_2025-06-21','9ded1e71d9a28c6ac2ad51b229d9df8a4c92b2e8','2025-06-06 10:10:44','2025-06-20 22:52:15',NULL,NULL,'new','Europe/Rome','3645','member',0.0000,0.0000,0.0000,0.0000,'46.149.102.19',1),
(17,'hafid.elamrani@icloud.com_2025-06-21','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-06-06 10:13:36','2025-06-26 15:33:15',NULL,NULL,'new','Asia/Makassar','6541','member',0.0000,0.0000,0.0000,0.0000,'103.24.150.159',1),
(18,'hafidelamranijoutey@gmail.com_2025-06-21','cb7710a473de9120005f6049137520fbe42b30b6','2025-06-06 10:18:19','2025-06-20 22:52:38',NULL,NULL,'new','Asia/Makassar','8616','member',0.0000,0.0000,0.0000,0.0000,'103.24.150.159',1),
(20,'pippobaudo376@gmail.com_2025-06-21','9ded1e71d9a28c6ac2ad51b229d9df8a4c92b2e8','2025-06-06 11:01:03','2025-06-20 22:52:46',NULL,NULL,'new','Europe/Rome','2532','member',0.0000,0.0000,0.0000,0.0000,'46.149.102.19',1),
(31,'fabio.guerra1975@gmail.com','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-06-06 12:40:16','2025-06-25 04:29:05',NULL,7,'active','Asia/Makassar',NULL,'member',0.0000,1251.9650,0.0000,0.0000,'110.136.212.108',0),
(40,'nevertouchme21@gmail.com','fbdef424b8d10220b478b5656aa73913439fcb2f','2025-06-12 08:34:36','2025-06-26 02:30:19',NULL,13,'active','Europe/Madrid',NULL,'member',0.0000,0.0000,0.0000,0.0000,'81.38.78.146',0),
(41,'baruhbiton@delightmoney.com','a9dd8ac7aa806116b656e82ceb48549c8b103d9b','2025-06-12 14:35:56','2025-06-12 15:37:24',NULL,13,'active','Europe/Rome',NULL,'member',0.0000,0.0000,0.0000,0.0000,'149.34.244.175',0),
(42,'eddy_h99@yahoo.com_2025-06-24','7c222fb2927d828af22f592134e8932480637c0d','2025-06-19 11:41:27','2025-06-26 02:20:40','eddyh99',NULL,'active','Asia/Makassar','9334','referral',0.0000,0.0000,0.0000,0.0000,'180.254.225.144',1),
(43,'dcatacchio@gmail.com','fca0336f1973c2d494b23837a242b237d19fd3b2','2025-06-22 10:25:35','2025-06-25 04:35:29',NULL,3,'active','Europe/Rome','4371','member',0.0000,250.0000,0.0000,0.0000,'93.42.33.25',0),
(44,'rillino@yahoo.it','c5c2c819e888c0f82c00c5b3092650c58f7b0ebd','2025-06-23 04:01:38','2025-06-22 20:03:03',NULL,NULL,'active','Europe/Rome',NULL,'member',0.0000,0.0000,0.0000,0.0000,'37.159.45.44',0),
(45,'profitdelights@gmail.com_2025-06-25','67722d5df937c7682aa8b14a63dc150bcc61390c','2025-06-23 04:03:36','2025-06-24 20:57:32',NULL,NULL,'new','Asia/Singapore','7102','member',0.0000,0.0000,0.0000,0.0000,'59.153.130.103',1),
(46,'eddy_h99@yahoo.com_2025-06-25_2025-06-25','7c222fb2927d828af22f592134e8932480637c0d','2025-06-24 02:13:45','2025-06-24 20:57:14',NULL,NULL,'new','Asia/Makassar','9400','member',0.0000,0.0000,0.0000,0.0000,'180.254.226.194',1),
(47,'armidaneglia27@gmail.com','cb7710a473de9120005f6049137520fbe42b30b6','2025-06-24 13:11:24','2025-06-24 20:57:24',NULL,NULL,'active','Asia/Makassar','2455','member',0.0000,0.0000,0.0000,0.0000,'103.24.150.159',0),
(99,'a@a.a00','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-05-16 15:34:43','2025-06-30 03:53:48',NULL,NULL,'active','',NULL,'superadmin',0.0000,0.0000,0.0000,0.0000,NULL,0);
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
  `order_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `downline_id` (`downline_id`),
  KEY `member_commission_ibfk_3` (`order_id`),
  CONSTRAINT `member_commission_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  CONSTRAINT `member_commission_ibfk_2` FOREIGN KEY (`downline_id`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  CONSTRAINT `member_commission_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `sinyal` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_commission`
--

LOCK TABLES `member_commission` WRITE;
/*!40000 ALTER TABLE `member_commission` DISABLE KEYS */;
INSERT INTO `member_commission` VALUES
(1,3,6,0.7158,44472110802,'2025-06-10 09:03:01'),
(2,3,8,0.1344,44472110802,'2025-06-10 09:03:01'),
(3,3,11,0.3282,44472110802,'2025-06-10 09:03:01'),
(4,13,7,0.9583,44472110802,'2025-06-10 09:03:01'),
(5,13,9,3.8007,44472110802,'2025-06-10 09:03:01'),
(6,3,6,0.3948,44975098855,'2025-06-20 10:40:01'),
(7,3,8,0.0978,44975098855,'2025-06-20 10:40:01'),
(8,3,11,0.1985,44975098855,'2025-06-20 10:40:01'),
(9,13,7,0.6591,44975098855,'2025-06-20 10:40:01'),
(10,13,9,1.9826,44975098855,'2025-06-20 10:40:01'),
(11,3,6,0.2165,45209637694,'2025-06-24 07:37:02'),
(12,3,8,0.0501,45209637694,'2025-06-24 07:37:02'),
(13,3,11,0.1071,45209637694,'2025-06-24 07:37:02'),
(14,7,31,0.7138,45209637694,'2025-06-24 07:37:02'),
(15,13,7,0.3756,45209637694,'2025-06-24 07:37:02'),
(16,13,9,1.0819,45209637694,'2025-06-24 07:37:02'),
(17,3,6,0.5120,45259531513,'2025-06-25 09:03:01'),
(18,3,8,0.1197,45259531513,'2025-06-25 09:03:01'),
(19,3,11,0.2504,45259531513,'2025-06-25 09:03:01'),
(20,13,9,2.4600,45259531513,'2025-06-25 09:03:01');
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
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_deposit`
--

LOCK TABLES `member_deposit` WRITE;
/*!40000 ALTER TABLE `member_deposit` DISABLE KEYS */;
INSERT INTO `member_deposit` VALUES
(1,'INV-F8BFB5CF',2,500.00,0.00,'2025-05-16 16:06:07','pending'),
(2,'INV-5214A5D5',3,2700.00,0.00,'2025-05-18 10:10:48','pending'),
(3,'INV-E967A6A0',6,2000.00,40.00,'2025-05-19 09:18:12','complete'),
(4,'INV-182E29BE',3,500.00,0.00,'2025-05-19 09:19:30','pending'),
(5,'INV-67DAA062',7,1000.00,0.00,'2025-05-19 09:35:08','pending'),
(6,'INV-75632121',7,1000.00,0.00,'2025-05-19 09:35:12','pending'),
(7,'INV-BCC9B83F',7,1000.00,0.00,'2025-05-19 09:35:13','pending'),
(8,'INV-298C9E8C',7,1000.00,0.00,'2025-05-19 09:35:14','pending'),
(9,'INV-66ADD1B0',7,1000.00,0.00,'2025-05-19 09:35:15','pending'),
(10,'INV-BB501E97',7,1000.00,0.00,'2025-05-19 09:35:15','pending'),
(11,'INV-A901054F',7,1000.00,0.00,'2025-05-19 09:35:16','pending'),
(12,'INV-F6E714FA',7,1000.00,0.00,'2025-05-19 09:35:17','pending'),
(13,'INV-F1DCEF06',9,10000.00,0.00,'2025-05-20 00:57:19','pending'),
(14,'INV-27D07739',9,10000.00,0.00,'2025-05-20 00:57:36','complete'),
(15,'INV-63FEA1DF',10,500.00,0.00,'2025-05-20 05:36:36','pending'),
(16,'INV-0E6263B1',9,500.00,0.00,'2025-05-22 08:25:14','pending'),
(17,'INV-1A50E271',8,500.00,10.00,'2025-05-22 08:29:06','complete'),
(18,'INV-17A3BF8C',9,500.00,0.00,'2025-05-22 08:34:08','pending'),
(19,'INV-5FBC14AC',11,1000.00,20.00,'2025-05-23 12:28:55','complete'),
(30,'INV-7CE3476A',7,2500.00,50.00,'2025-05-27 05:25:10','complete'),
(31,'INV-7693428E',3,2300.00,46.00,'2025-05-27 08:05:18','pending'),
(32,'INV-27D07739',13,10000.00,0.00,'2025-05-20 00:57:36','complete'),
(47,'INV-A7E76787',3,500.00,10.00,'2025-06-10 12:07:43','pending'),
(48,'INV-C94808E4',31,5000.00,100.00,'2025-06-12 08:22:08','complete'),
(49,'INV-3F78EE19',3,500.00,10.00,'2025-06-12 12:24:28','pending'),
(50,'INV-03B28497',3,500.00,10.00,'2025-06-12 12:24:43','pending'),
(51,'INV-5144010B',41,600.00,12.00,'2025-06-12 15:14:14','pending'),
(52,'INV-B50CC5E1',41,59500.00,1190.00,'2025-06-12 15:21:22','pending'),
(53,'INV-88C77C93',44,2400.00,48.00,'2025-06-23 04:03:58','pending'),
(54,'INV-28E4D4A1',43,1000.00,20.00,'2025-06-23 20:54:12','pending'),
(55,'INV-137AA9DB',3,500.00,10.00,'2025-06-23 20:57:54','pending'),
(56,'INV-FDB185C4',3,500.00,10.00,'2025-06-23 20:59:29','pending'),
(57,'INV-3A8C6E52',3,500.00,10.00,'2025-06-23 20:59:46','pending'),
(58,'INV-B92CD121',3,500.00,10.00,'2025-06-23 20:59:47','pending'),
(59,'INV-BF4293E2',43,1000.00,20.00,'2025-06-23 21:18:12','complete'),
(60,'INV-137D0551',3,500.00,10.00,'2025-06-24 13:34:31','pending'),
(61,'INV-37A99288',7,1000.00,20.00,'2025-06-25 10:39:17','pending'),
(62,'INV-D796041F',7,1000.00,20.00,'2025-06-25 10:40:45','pending'),
(63,'INV-CDFA99F6',40,20000.00,400.00,'2025-06-25 16:55:03','complete'),
(66,'INV1751254309',1,100.00,0.00,'2025-06-29 20:31:49','complete'),
(68,'INV1751254495',1,100.00,0.00,'2025-06-29 20:34:55','complete'),
(69,'INV1751254575',1,100.00,0.00,'2025-06-29 20:36:15','complete'),
(70,'INV1751254584',1,100.00,0.00,'2025-06-29 20:36:24','complete'),
(71,'INV1751254597',1,100.00,0.00,'2025-06-29 20:36:37','complete');
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
  `amount_usdt` decimal(16,6) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `sinyal_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_id` (`member_id`,`sinyal_id`),
  KEY `fk_member_sinyal_member` (`member_id`),
  KEY `fk_member_sinyal_sinyal` (`sinyal_id`),
  CONSTRAINT `fk_member_sinyal_member` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_member_sinyal_sinyal` FOREIGN KEY (`sinyal_id`) REFERENCES `sinyal` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_sinyal`
--

LOCK TABLES `member_sinyal` WRITE;
/*!40000 ALTER TABLE `member_sinyal` DISABLE KEYS */;
INSERT INTO `member_sinyal` VALUES
(2,0.022732,2499.696296,9,1,'2025-05-26 01:49:26','2025-05-26 09:14:45'),
(3,0.004550,499.939259,6,1,'2025-05-26 01:49:26','2025-05-26 09:11:06'),
(4,0.001136,124.984815,8,1,'2025-05-26 01:49:26','2025-05-26 09:11:11'),
(5,0.002274,249.969630,11,1,'2025-05-26 01:49:26','2025-05-26 09:11:15'),
(6,0.000165,17.498697,3,4,'2025-05-30 00:30:03','2025-05-30 01:15:35'),
(7,0.000118,12.499069,13,4,'2025-05-30 00:30:03','2025-05-30 01:15:45'),
(8,0.004713,499.962779,6,4,'2025-05-30 00:30:03','2025-05-30 01:42:53'),
(9,0.001170,124.990695,8,4,'2025-05-30 00:30:03','2025-05-30 01:42:48'),
(10,0.002351,249.981390,11,4,'2025-05-30 00:30:03','2025-05-30 01:42:43'),
(11,0.005903,624.953474,7,4,'2025-05-30 00:30:03','2025-05-30 01:40:58'),
(12,0.023606,2499.813896,9,4,'2025-05-30 00:30:03','2025-05-30 01:41:22'),
(13,0.000000,17.498124,3,7,'2025-05-31 02:07:57','2025-05-31 02:07:57'),
(14,0.000002,2512.230666,13,7,'2025-05-31 02:07:57','2025-05-31 02:07:57'),
(15,0.000000,499.946401,6,7,'2025-05-31 02:07:57','2025-05-31 02:07:57'),
(16,0.000000,124.986600,8,7,'2025-05-31 02:07:57','2025-05-31 02:07:57'),
(17,0.000000,249.973201,11,7,'2025-05-31 02:07:57','2025-05-31 02:07:57'),
(18,0.000000,624.933002,7,7,'2025-05-31 02:07:57','2025-05-31 02:07:57'),
(19,0.000002,2499.732006,9,7,'2025-05-31 02:07:57','2025-05-31 02:07:57'),
(20,0.000165,18.009750,3,8,'2025-06-10 09:03:01','2025-06-10 09:03:01'),
(21,0.000118,12.879700,13,8,'2025-06-10 09:03:01','2025-06-10 09:03:01'),
(22,0.004713,514.423950,6,8,'2025-06-10 09:03:01','2025-06-10 09:03:01'),
(23,0.001170,127.705500,8,8,'2025-06-10 09:03:01','2025-06-10 09:03:01'),
(24,0.002351,256.611650,11,8,'2025-06-10 09:03:01','2025-06-10 09:03:01'),
(25,0.005903,644.312450,7,8,'2025-06-10 09:03:01','2025-06-10 09:03:01'),
(26,0.023606,2576.594900,9,8,'2025-06-10 09:03:01','2025-06-10 09:03:01'),
(27,0.024172,2512.402733,13,10,'2025-06-14 20:07:57','2025-06-18 12:00:02'),
(28,0.003625,376.818287,6,10,'2025-06-14 20:07:57','2025-06-18 12:00:02'),
(29,0.000905,94.089578,8,10,'2025-06-14 20:07:57','2025-06-18 12:00:02'),
(30,0.001812,188.334148,11,10,'2025-06-14 20:07:57','2025-06-18 12:00:02'),
(31,0.006036,627.406348,7,10,'2025-06-14 20:07:57','2025-06-18 12:00:02'),
(32,0.018132,1884.658904,9,10,'2025-06-14 20:07:57','2025-06-18 12:00:02'),
(39,0.024172,2565.857800,13,14,'2025-06-20 10:40:01','2025-06-20 10:40:01'),
(40,0.003625,384.793750,6,14,'2025-06-20 10:40:01','2025-06-20 10:40:01'),
(41,0.000905,96.065750,8,14,'2025-06-20 10:40:01','2025-06-20 10:40:01'),
(42,0.001812,192.343800,11,14,'2025-06-20 10:40:01','2025-06-20 10:40:01'),
(43,0.006036,640.721400,7,14,'2025-06-20 10:40:01','2025-06-20 10:40:01'),
(44,0.018132,1924.711800,9,14,'2025-06-20 10:40:01','2025-06-20 10:40:01'),
(45,0.024225,2518.321380,13,15,'2025-06-21 08:59:45','2025-06-21 09:00:02'),
(46,0.003636,377.981629,6,15,'2025-06-21 08:59:45','2025-06-21 09:00:02'),
(47,0.000907,94.366075,8,15,'2025-06-21 08:59:45','2025-06-21 09:00:02'),
(48,0.001817,188.909592,11,15,'2025-06-21 08:59:45','2025-06-21 09:00:02'),
(49,0.012020,1249.583879,31,15,'2025-06-21 08:59:45','2025-06-21 09:00:02'),
(50,0.006294,654.279620,7,15,'2025-06-21 08:59:45','2025-06-21 09:00:02'),
(51,0.018186,1890.552932,9,15,'2025-06-21 08:59:45','2025-06-21 09:00:02'),
(59,0.000000,1889.294730,13,17,'2025-06-21 23:39:29','2025-06-21 23:39:29'),
(60,0.000000,283.569683,6,17,'2025-06-21 23:39:29','2025-06-21 23:39:29'),
(61,0.000000,70.796810,8,17,'2025-06-21 23:39:29','2025-06-21 23:39:29'),
(62,0.000000,141.723601,11,17,'2025-06-21 23:39:29','2025-06-21 23:39:29'),
(63,0.000000,937.460950,31,17,'2025-06-21 23:39:29','2025-06-21 23:39:29'),
(64,0.000000,490.855884,7,17,'2025-06-21 23:39:29','2025-06-21 23:39:29'),
(65,0.000000,1418.328342,9,17,'2025-06-21 23:39:29','2025-06-21 23:39:29'),
(66,0.000000,1889.294730,13,18,'2025-06-21 23:48:24','2025-06-21 23:48:24'),
(67,0.000000,283.569683,6,18,'2025-06-21 23:48:24','2025-06-21 23:48:24'),
(68,0.000000,70.796810,8,18,'2025-06-21 23:48:24','2025-06-21 23:48:24'),
(69,0.000000,141.723601,11,18,'2025-06-21 23:48:24','2025-06-21 23:48:24'),
(70,0.000000,937.460950,31,18,'2025-06-21 23:48:24','2025-06-21 23:48:24'),
(71,0.000000,490.855884,7,18,'2025-06-21 23:48:24','2025-06-21 23:48:24'),
(72,0.000000,1418.328342,9,18,'2025-06-21 23:48:24','2025-06-21 23:48:24'),
(73,0.024225,2547.461756,13,19,'2025-06-24 07:37:02','2025-06-24 07:37:02'),
(74,0.003636,382.355870,6,19,'2025-06-24 07:37:02','2025-06-24 07:37:02'),
(75,0.000907,95.378651,8,19,'2025-06-24 07:37:02','2025-06-24 07:37:02'),
(76,0.001817,191.072776,11,19,'2025-06-24 07:37:02','2025-06-24 07:37:02'),
(77,0.012020,1264.003728,31,19,'2025-06-24 07:37:02','2025-06-24 07:37:02'),
(78,0.006294,661.866844,7,19,'2025-06-24 07:37:02','2025-06-24 07:37:02'),
(79,0.018186,1912.410299,9,19,'2025-06-24 07:37:02','2025-06-24 07:37:02'),
(80,0.000000,2.812500,3,20,'2025-06-25 04:29:05','2025-06-25 04:29:05'),
(81,0.000000,504.936700,6,20,'2025-06-25 04:29:05','2025-06-25 04:29:05'),
(82,0.000000,655.532500,7,20,'2025-06-25 04:29:05','2025-06-25 04:29:05'),
(83,0.000000,126.046700,8,20,'2025-06-25 04:29:05','2025-06-25 04:29:05'),
(84,0.000000,2525.550000,9,20,'2025-06-25 04:29:05','2025-06-25 04:29:05'),
(85,0.000000,252.356700,11,20,'2025-06-25 04:29:05','2025-06-25 04:29:05'),
(86,0.000000,2522.767500,13,20,'2025-06-25 04:29:05','2025-06-25 04:29:05'),
(87,0.000000,1251.965000,31,20,'2025-06-25 04:29:05','2025-06-25 04:29:05'),
(88,0.000000,2.812500,3,21,'2025-06-25 04:35:29','2025-06-25 04:35:29'),
(89,0.000000,504.936700,6,21,'2025-06-25 04:35:29','2025-06-25 04:35:29'),
(90,0.000000,655.532500,7,21,'2025-06-25 04:35:29','2025-06-25 04:35:29'),
(91,0.000000,126.046700,8,21,'2025-06-25 04:35:29','2025-06-25 04:35:29'),
(92,0.000000,2525.550000,9,21,'2025-06-25 04:35:29','2025-06-25 04:35:29'),
(93,0.000000,252.356700,11,21,'2025-06-25 04:35:29','2025-06-25 04:35:29'),
(94,0.000000,2522.767500,13,21,'2025-06-25 04:35:29','2025-06-25 04:35:29'),
(95,0.000000,1251.965000,31,21,'2025-06-25 04:35:29','2025-06-25 04:35:29'),
(96,0.000000,250.000000,43,21,'2025-06-25 04:35:29','2025-06-25 04:35:29'),
(102,0.022732,2549.393800,9,23,'2025-05-27 01:49:26','2025-05-27 09:14:45'),
(103,0.004550,510.282500,6,23,'2025-05-27 01:49:26','2025-05-27 09:11:06'),
(104,0.001136,127.402400,8,23,'2025-05-27 01:49:26','2025-05-27 09:11:11'),
(105,0.002274,255.029100,11,23,'2025-05-27 01:49:26','2025-05-27 09:11:15');
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES
(3,'price','500'),
(4,'cost','0.01'),
(5,'referral_fee','0.02'),
(6,'cost_trade','0.01'),
(7,'asset_btc','0');
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sinyal`
--

LOCK TABLES `sinyal` WRITE;
/*!40000 ALTER TABLE `sinyal` DISABLE KEYS */;
INSERT INTO `sinyal` VALUES
(1,43723863145,'Buy A',109850.00,1,1,'59.153.130.103','no','filled','2025-05-26 09:49:26','2025-06-25 20:38:10'),
(2,43736432569,'Sell A',115150.00,1,1,'59.153.130.103','yes','canceled','2025-05-26 17:35:56','2025-05-26 17:36:18'),
(3,43736444770,'Sell A',115150.00,1,1,'59.153.130.103','yes','canceled','2025-05-26 17:36:40','2025-06-10 16:55:56'),
(4,43944487214,'Buy B',105850.00,4,1,'59.153.130.103','no','filled','2025-05-30 08:30:02','2025-06-10 17:03:01'),
(6,43950400339,'Sell B',115150.00,4,1,'125.162.134.38','yes','canceled','2025-05-30 09:43:02','2025-06-10 16:55:50'),
(7,44023420586,'Buy C',95850.00,NULL,1,'103.175.212.86','yes','canceled','2025-05-31 10:07:57','2025-06-10 16:55:28'),
(8,44472110802,'Sell B',109150.00,4,1,'90.166.195.102','no','filled','2025-06-10 16:57:01','2025-06-10 17:03:01'),
(9,44476066373,'Sell A',112150.00,1,1,'90.166.195.102','yes','canceled','2025-06-10 19:30:47','2025-06-25 15:15:25'),
(10,44710212457,'Buy B',103850.00,10,1,'90.166.195.75','no','filled','2025-06-15 04:07:56','2025-06-20 18:40:01'),
(11,44964533927,'Sell B',112150.00,10,1,'59.153.130.103','yes','canceled','2025-06-20 10:27:14','2025-06-20 16:58:06'),
(12,44973400154,'Sell B',106150.00,10,1,'103.175.212.54','yes','canceled','2025-06-20 16:59:15','2025-06-20 17:18:49'),
(13,44974763787,'Sell B',106150.00,10,1,'103.175.212.72','yes','canceled','2025-06-20 17:54:51','2025-06-20 18:07:06'),
(14,44975098855,'Sell B',106150.00,10,1,'103.175.212.58','no','filled','2025-06-20 18:12:55','2025-06-20 18:40:01'),
(15,45019622532,'Buy B',103850.00,15,1,'103.175.212.61','no','filled','2025-06-21 16:59:45','2025-06-24 15:37:02'),
(16,45019653752,'Sell B',112150.00,15,1,'103.175.212.75','yes','canceled','2025-06-21 17:00:44','2025-06-24 15:33:41'),
(17,45047605282,'Buy C',97850.00,NULL,1,'103.175.212.80','yes','canceled','2025-06-22 07:39:29','2025-06-22 07:44:26'),
(18,45048864955,'Buy C',97850.00,NULL,1,'103.175.212.88','yes','canceled','2025-06-22 07:48:24','2025-06-22 07:56:49'),
(19,45209637694,'Sell B',105150.00,15,1,'59.153.130.103','no','filled','2025-06-24 15:36:23','2025-06-24 15:37:02'),
(20,45253281965,'Buy B',100000.00,NULL,1,'103.175.212.99','yes','canceled','2025-06-25 12:29:05','2025-06-25 12:34:51'),
(21,45253381351,'Buy B',100000.00,NULL,1,'103.175.212.72','yes','canceled','2025-06-25 12:35:29','2025-06-25 12:36:53'),
(22,45256114800,'Sell A',110150.00,1,1,'103.175.212.98','yes','canceled','2025-06-25 15:15:45','2025-06-25 17:45:34'),
(23,45259531513,'Sell A',112150.00,1,1,'103.175.212.60','no','filled','2025-06-25 17:45:56','2025-06-25 20:38:24');
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
  `master_wallet` decimal(20,4) NOT NULL DEFAULT 0.0000,
  `client_wallet` decimal(20,4) NOT NULL DEFAULT 0.0000,
  `member_id` int(11) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `wallet_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`),
  CONSTRAINT `wallet_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `sinyal` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wallet`
--

LOCK TABLES `wallet` WRITE;
/*!40000 ALTER TABLE `wallet` DISABLE KEYS */;
INSERT INTO `wallet` VALUES
(1,0.2581,0.2530,3,44472110802,'2025-06-30 09:43:21'),
(2,0.1922,0.1884,13,44472110802,'2025-06-30 09:43:21'),
(3,6.5871,7.1583,6,44472110802,'2025-06-30 09:43:21'),
(4,1.2366,1.3438,8,44472110802,'2025-06-30 09:43:21'),
(5,3.0201,3.2820,11,44472110802,'2025-06-30 09:43:21'),
(6,8.8180,9.5827,7,44472110802,'2025-06-30 09:43:21'),
(7,34.9737,38.0066,9,44472110802,'2025-06-30 09:43:21'),
(8,26.9948,26.4603,13,44975098855,'2025-06-30 09:43:21'),
(9,3.6328,3.9479,6,44975098855,'2025-06-30 09:43:21'),
(10,0.9001,0.9782,8,44975098855,'2025-06-30 09:43:21'),
(11,1.8264,1.9848,11,44975098855,'2025-06-30 09:43:21'),
(12,6.0650,6.5910,7,44975098855,'2025-06-30 09:43:21'),
(13,18.2441,19.8262,9,44975098855,'2025-06-30 09:43:21'),
(14,14.7159,14.4245,13,45209637694,'2025-06-30 09:43:21'),
(15,1.9925,2.1652,6,45209637694,'2025-06-30 09:43:21'),
(16,0.4612,0.5012,8,45209637694,'2025-06-30 09:43:21'),
(17,0.9853,1.0708,11,45209637694,'2025-06-30 09:43:21'),
(18,6.5682,7.1378,31,45209637694,'2025-06-30 09:43:21'),
(19,3.4560,3.7557,7,45209637694,'2025-06-30 09:43:21'),
(20,9.9560,10.8194,9,45209637694,'2025-06-30 09:43:21'),
(21,4.7113,5.1199,6,45259531513,'2025-06-30 09:43:21'),
(22,1.1012,1.1967,8,45259531513,'2025-06-30 09:43:21'),
(23,2.3046,2.5044,11,45259531513,'2025-06-30 09:43:21'),
(24,22.6372,24.6003,9,45259531513,'2025-06-30 09:43:21');
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
  `amount` decimal(18,6) NOT NULL,
  `payment_details` text DEFAULT NULL,
  `wallet_address` varchar(255) DEFAULT NULL,
  `status` enum('pending','rejected','completed') NOT NULL DEFAULT 'pending',
  `jenis` enum('trade','withdraw','balance','comission') NOT NULL DEFAULT 'withdraw',
  `requested_at` datetime NOT NULL DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `withdraw_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdraw`
--

LOCK TABLES `withdraw` WRITE;
/*!40000 ALTER TABLE `withdraw` DISABLE KEYS */;
INSERT INTO `withdraw` VALUES
(1,9,'usdt',10000.000000,NULL,NULL,'pending','trade','2025-05-24 16:36:19',NULL,NULL),
(3,6,'usdt',2000.000000,NULL,NULL,'pending','trade','2025-05-24 16:36:19',NULL,NULL),
(4,11,'usdt',1000.000000,NULL,NULL,'pending','trade','2025-05-24 16:36:19',NULL,NULL),
(5,8,'usdt',500.000000,NULL,NULL,'pending','trade','2025-05-24 16:36:19',NULL,NULL),
(6,7,'usdt',2500.000000,NULL,NULL,'pending','trade','2025-05-27 14:49:46',NULL,NULL),
(7,13,'usdt',50.000000,NULL,NULL,'pending','comission','2025-05-27 15:08:19',NULL,NULL),
(9,13,'usdt',50.000000,NULL,NULL,'pending','trade','2025-05-27 15:24:56',NULL,NULL),
(11,3,'usdt',70.000000,NULL,NULL,'pending','comission','2025-05-27 15:58:16',NULL,NULL),
(12,3,'usdt',70.000000,NULL,NULL,'pending','trade','2025-05-27 15:58:33',NULL,NULL),
(14,13,'usdt',10000.000000,NULL,NULL,'pending','trade','2025-05-30 12:31:26',NULL,NULL),
(15,3,'usdt',50.000000,NULL,NULL,'pending','balance','2025-06-10 17:02:54',NULL,NULL),
(16,3,'usdt',1.000000,NULL,NULL,'pending','comission','2025-06-10 17:15:08',NULL,NULL),
(17,3,'usdt',50.000000,NULL,NULL,'pending','trade','2025-06-10 17:15:26',NULL,NULL),
(18,3,'usdt',70.000000,NULL,NULL,'pending','balance','2025-06-14 19:56:21',NULL,NULL),
(19,31,'usdt',5000.000000,NULL,NULL,'pending','trade','2025-06-20 17:05:15',NULL,NULL),
(20,7,'usdt',100.000000,NULL,NULL,'pending','comission','2025-06-20 17:09:33',NULL,NULL),
(21,7,'usdt',100.000000,NULL,NULL,'pending','trade','2025-06-20 17:09:33',NULL,NULL),
(22,3,'usdt',71.000000,NULL,NULL,'pending','trade','2025-06-23 10:48:36',NULL,NULL),
(23,3,'usdt',60.000000,NULL,NULL,'pending','balance','2025-06-25 01:59:02',NULL,NULL),
(24,3,'btc',0.000000,'{\"recipient\":null,\"routing_number\":null,\"account_type\":null,\"swift_code\":null,\"address\":null,\"network\":\"Bitcoin\"}','bc1qj632xplhdtzesjggd5ejzw35cnw0ztk5gc9p6l','completed','withdraw','2025-06-25 02:00:13',NULL,NULL),
(25,43,'usdt',1000.000000,NULL,NULL,'pending','trade','2025-06-25 12:30:17',NULL,NULL),
(26,3,'usdt',21.264700,NULL,NULL,'pending','comission','2025-06-25 12:48:34',NULL,NULL),
(27,3,'usdt',80.000000,NULL,NULL,'pending','trade','2025-06-26 00:48:35',NULL,NULL),
(28,40,'usdt',20000.000000,NULL,NULL,'pending','trade','2025-06-26 10:29:37',NULL,NULL),
(29,13,'usdt',411.318200,NULL,NULL,'pending','comission','2025-06-26 10:39:20',NULL,NULL),
(30,13,'usdt',411.318200,NULL,NULL,'pending','trade','2025-06-26 10:39:20',NULL,NULL),
(31,7,'usdt',0.713800,NULL,NULL,'pending','comission','2025-06-26 10:42:27',NULL,NULL),
(32,7,'usdt',0.713800,NULL,NULL,'pending','trade','2025-06-26 10:43:50',NULL,NULL),
(33,3,'usdt',1.260000,NULL,NULL,'pending','trade','2025-06-26 12:02:20',NULL,NULL),
(34,3,'usdt',0.100000,NULL,NULL,'pending','comission','2025-06-26 22:38:49',NULL,NULL),
(35,3,'usdt',0.100000,NULL,NULL,'pending','trade','2025-06-26 22:38:49',NULL,NULL),
(36,3,'usdt',0.300000,NULL,NULL,'pending','comission','2025-06-26 22:49:33',NULL,NULL),
(39,1,'usdt',100.000000,NULL,NULL,'pending','trade','2025-06-30 10:54:26',NULL,NULL),
(40,1,'usdt',100.000000,NULL,NULL,'pending','trade','2025-06-30 10:55:47',NULL,NULL),
(41,1,'usdt',200.000000,NULL,NULL,'pending','balance','2025-06-30 10:55:56',NULL,NULL);
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

-- Dump completed on 2025-06-30 10:57:08

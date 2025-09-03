-- db_api_elite2.member_o2o definition

CREATE TABLE `member_o2o` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `api_key` varchar(255) DEFAULT NULL,
  `api_secret` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `refcode` (`refcode`),
  KEY `id_referral` (`id_referral`),
  CONSTRAINT `member_o2o_ibfk_1` FOREIGN KEY (`id_referral`) REFERENCES `member_o2o` (`id`) ON DELETE SET NULL
)


-- db_api_elite2.subscription definition

CREATE TABLE `subscription` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_o2o_id` int NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('active','expired','free','pending') NOT NULL,
  `is_admin_granted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `initial_capital` int NOT NULL,
  `commission` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscription_ibfk_1` (`member_o2o_id`),
  CONSTRAINT `subscription_ibfk_1` FOREIGN KEY (`member_o2o_id`) REFERENCES `member_o2o` (`id`) ON DELETE CASCADE
)


-- db_api_elite2.member_o2o_role definition

CREATE TABLE `member_o2o_role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_o2o_id` int NOT NULL,
  `alias` varchar(255) NOT NULL,
  `access` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_member_o2o_id` (`member_o2o_id`),
  UNIQUE KEY `unique_alias` (`alias`),
  CONSTRAINT `member_o2o_role_ibfk_1` FOREIGN KEY (`member_o2o_id`) REFERENCES `member_o2o` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `member_o2o_role_chk_1` CHECK (json_valid(`access`))
)

INSERT INTO db_api_elite2.`member_o2o`
(email, passwd, created_at, updated_at, refcode, id_referral, status, timezone, otp, `role`, ip_addr, is_delete, api_key, api_secret) 
VALUES
('a@a.a','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-02-17 16:26:58','2025-04-28 10:33:11','xxxxxx',NULL,'active','Asia/Jakarta','5919','superadmin','127.0.0.1',0,'7VmlkBY8gtWIXl421uEXp65wXtIi7gaKsHwI2d00mRFleRdOA3dzIjjXxOrwRox8','IneWnn5Szy8mIxFuXoNLxWSRJGzKufHE3Cp07wOAvYozjBk2eTpSQsE0tbVfDuIk'),
('user@gmail.com','7af2d10b73ab7cd8f603937f7697cb5fe432c7ff','2025-02-12 07:09:10','2025-07-16 12:29:45',NULL,NULL,'active','',NULL,'member',NULL,0,'7VmlkBY8gtWIXl421uEXp65wXtIi7gaKsHwI2d00mRFleRdOA3dzIjjXxOrwRox8','IneWnn5Szy8mIxFuXoNLxWSRJGzKufHE3Cp07wOAvYozjBk2eTpSQsE0tbVfDuIk'),
('user1@gmail.com','xyz','2025-02-12 07:28:27','2025-03-06 12:38:50',NULL,NULL,'new','Asia/Jakarta',NULL,'member',NULL,0,'7VmlkBY8gtWIXl421uEXp65wXtIi7gaKsHwI2d00mRFleRdOA3dzIjjXxOrwRox8','IneWnn5Szy8mIxFuXoNLxWSRJGzKufHE3Cp07wOAvYozjBk2eTpSQsE0tbVfDuIk'),
('miftahus@gmail.com','7b902e6ff1db9f560443f2048974fd7d386975b0','2025-02-13 05:33:56','2025-03-16 15:50:20','slotgcr',NULL,'active','Asia/Jakarta','5040','member',NULL,0,'7VmlkBY8gtWIXl421uEXp65wXtIi7gaKsHwI2d00mRFleRdOA3dzIjjXxOrwRox8','IneWnn5Szy8mIxFuXoNLxWSRJGzKufHE3Cp07wOAvYozjBk2eTpSQsE0tbVfDuIk'),
('user99@gmail.com','user1234','2025-02-13 09:58:59','2025-04-16 12:11:13',NULL,NULL,'active','Asia/Jakarta',NULL,'member','127.0.0.1',0,'7VmlkBY8gtWIXl421uEXp65wXtIi7gaKsHwI2d00mRFleRdOA3dzIjjXxOrwRox8','IneWnn5Szy8mIxFuXoNLxWSRJGzKufHE3Cp07wOAvYozjBk2eTpSQsE0tbVfDuIk'),
('miftahus@my.id_2025-02-16','','2025-02-14 18:22:04','2025-03-06 12:38:50',NULL,NULL,'disabled','',NULL,'member',NULL,1,'7VmlkBY8gtWIXl421uEXp65wXtIi7gaKsHwI2d00mRFleRdOA3dzIjjXxOrwRox8','IneWnn5Szy8mIxFuXoNLxWSRJGzKufHE3Cp07wOAvYozjBk2eTpSQsE0tbVfDuIk'),
('kucing@gmail.com','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-02-20 10:19:58','2025-08-18 15:44:19','7wmuz5pl',NULL,'active','',NULL,'admin',NULL,0,'7VmlkBY8gtWIXl421uEXp65wXtIi7gaKsHwI2d00mRFleRdOA3dzIjjXxOrwRox8','IneWnn5Szy8mIxFuXoNLxWSRJGzKufHE3Cp07wOAvYozjBk2eTpSQsE0tbVfDuIk'),
('harimaumalaya@gmail.com','7b902e6ff1db9f560443f2048974fd7d386975b0','2025-02-20 10:33:43','2025-03-06 12:38:50','20psk4no',NULL,'active','Asia/Jakarta',NULL,'member','127.0.0.1',0,'7VmlkBY8gtWIXl421uEXp65wXtIi7gaKsHwI2d00mRFleRdOA3dzIjjXxOrwRox8','IneWnn5Szy8mIxFuXoNLxWSRJGzKufHE3Cp07wOAvYozjBk2eTpSQsE0tbVfDuIk'),
('XCV@gmail.com','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-02-25 13:23:00','2025-03-06 12:38:50',NULL,NULL,'active','Asia/Jakarta',NULL,'admin','127.0.0.1',0,'7VmlkBY8gtWIXl421uEXp65wXtIi7gaKsHwI2d00mRFleRdOA3dzIjjXxOrwRox8','IneWnn5Szy8mIxFuXoNLxWSRJGzKufHE3Cp07wOAvYozjBk2eTpSQsE0tbVfDuIk'),
('andi@gmail.com','40bd001563085fc35165329ea1ff5c5ecbdbbeef','2025-02-27 17:51:35','2025-09-02 10:36:44',NULL,NULL,'active','Asia/Jakarta',NULL,'member','127.0.0.1',0,'7VmlkBY8gtWIXl421uEXp65wXtIi7gaKsHwI2d00mRFleRdOA3dzIjjXxOrwRox8','IneWnn5Szy8mIxFuXoNLxWSRJGzKufHE3Cp07wOAvYozjBk2eTpSQsE0tbVfDuIk');


-- db_api_elite.tb_member_onetone definition

CREATE TABLE `tb_member_onetone` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- db_api_elite.tb_payment_onetoone definition

CREATE TABLE `tb_payment_onetoone` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_member_onetoone` int unsigned NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `status_invoice` enum('paid','unpaid') NOT NULL DEFAULT 'unpaid',
  `link_invoice` varchar(255) NOT NULL,
  `invoice_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payment_member` (`id_member_onetoone`),
  CONSTRAINT `fk_payment_member` FOREIGN KEY (`id_member_onetoone`) REFERENCES `tb_member_onetone` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
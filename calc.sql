-- db_api_elite2.calculator_otc definition

CREATE TABLE `calculator_otc` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `amount_btc` decimal(18,8) NOT NULL,
  `lock_amount_btc` tinyint(1) NOT NULL DEFAULT '0',
  `buy_price` decimal(18,2) DEFAULT NULL,
  `lock_buy_price` tinyint(1) NOT NULL DEFAULT '0',
  `sell_price` decimal(18,2) DEFAULT NULL,
  `lock_sell_price` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- db_api_elite2.calculator_mediation definition

CREATE TABLE `calculator_mediation` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `prezzo_buy1` decimal(15,2) DEFAULT NULL,
  `lock_buy1` tinyint(1) DEFAULT '0',
  `prezzo_buy2` decimal(15,2) DEFAULT NULL,
  `lock_buy2` tinyint(1) DEFAULT '0',
  `prezzo_buy3` decimal(15,2) DEFAULT NULL,
  `lock_buy3` tinyint(1) DEFAULT '0',
  `prezzo_buy4` decimal(15,2) DEFAULT NULL,
  `lock_buy4` tinyint(1) DEFAULT '0',
  `prezzo_sell1` decimal(15,2) DEFAULT NULL,
  `lock_sell1` tinyint(1) DEFAULT '0',
  `prezzo_sell2` decimal(15,2) DEFAULT NULL,
  `lock_sell2` tinyint(1) DEFAULT '0',
  `prezzo_sell3` decimal(15,2) DEFAULT NULL,
  `lock_sell3` tinyint(1) DEFAULT '0',
  `prezzo_sell4` decimal(15,2) DEFAULT NULL,
  `lock_sell4` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- db_api_elite2.calculator_interest definition

CREATE TABLE `calculator_interest` (
  `id` int NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `lock_amount` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
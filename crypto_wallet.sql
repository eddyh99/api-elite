CREATE TABLE `crypto_wallet` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `member_id` INT NOT NULL,
  `type` ENUM('hedgefund','onetoone') NOT NULL,              -- tipe akun
  `network` ENUM('erc20','bep20','polygon','trc20') NOT NULL, -- tipe jaringan
  `address` VARCHAR(128) NOT NULL,                            -- alamat wallet
  `public_key` TEXT NOT NULL,                                 -- public key
  `private_key` TEXT NOT NULL,                            -- private key terenkripsi AES
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_member_network_type` (`member_id`,`type`,`network`),
  CONSTRAINT `fk_crypto_wallet_member`
    FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

ALTER TABLE `crypto_wallet`
MODIFY COLUMN `network` ENUM('erc20','bep20','polygon','trc20','base','solana') NOT NULL;

ALTER TABLE `member_deposit`
ADD COLUMN `payment_type` ENUM(
    'us_bank',
    'international_bank',
    'usdt_bep20',
    'usdt_trc20',
    'usdt_erc20',
    'usdt_polygon',
    'usdc_bep20',
    'usdc_trc20',
    'usdc_erc20',
    'usdc_polygon',
    'usdc_base',
    'usdc_solana'
) NULL AFTER `invoice`;

ALTER TABLE crypto_wallet ADD COLUMN balance_usdc DECIMAL(30, 6) DEFAULT 0, ADD COLUMN balance_usdt DECIMAL(30, 6) DEFAULT 0;
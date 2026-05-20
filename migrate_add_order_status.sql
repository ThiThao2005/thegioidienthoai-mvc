-- Optional manual migration. The app also tries to create these columns/tables automatically.

ALTER TABLE `orders`
ADD COLUMN IF NOT EXISTS `user_id` INT NULL,
ADD COLUMN IF NOT EXISTS `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
ADD COLUMN IF NOT EXISTS `payment_method` VARCHAR(30) NOT NULL DEFAULT 'COD',
ADD COLUMN IF NOT EXISTS `payment_status` VARCHAR(30) NOT NULL DEFAULT 'unpaid',
ADD COLUMN IF NOT EXISTS `transaction_code` VARCHAR(120) NULL,
ADD COLUMN IF NOT EXISTS `email` VARCHAR(160) NULL,
ADD COLUMN IF NOT EXISTS `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `rating` TINYINT NOT NULL,
  `comment` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_product_user_review` (`product_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wishlists` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_user_product_wishlist` (`user_id`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

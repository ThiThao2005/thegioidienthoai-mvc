-- =====================================================================
-- Schema gốc cho my_store (Thế Giới Điện Thoại)
-- Docker tự chạy file này khi tạo DB lần đầu.
-- Trên InfinityFree: mở phpMyAdmin -> chọn DB -> tab Import -> chọn file này.
-- Các bảng phụ (brands, product_images, product_variants, banners, vouchers,
-- product_reviews, wishlists...) được ProductModel tự tạo khi web chạy lần đầu.
-- =====================================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `account` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(160) NOT NULL UNIQUE,
  `fullname` VARCHAR(160) NULL,
  `email` VARCHAR(160) NULL,
  `phone` VARCHAR(30) NULL,
  `address` VARCHAR(255) NULL,
  `avatar` VARCHAR(255) NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) NOT NULL DEFAULT 'user',
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `email_verify_token` VARCHAR(255) NULL,
  `reset_token` VARCHAR(255) NULL,
  `reset_token_expire` DATETIME NULL,
  `remember_token` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `category` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `description` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `price` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `image` VARCHAR(255) NULL,
  `category_id` INT NULL,
  `brand_id` INT NULL,
  `warranty_months` INT NOT NULL DEFAULT 12,
  `sale_percent` INT NOT NULL DEFAULT 0,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `slug` VARCHAR(180) NULL,
  `sold_count` INT NOT NULL DEFAULT 0,
  INDEX `idx_product_category` (`category_id`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `category`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `name` VARCHAR(160) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `email` VARCHAR(160) NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `payment_method` VARCHAR(30) NOT NULL DEFAULT 'COD',
  `payment_status` VARCHAR(30) NOT NULL DEFAULT 'unpaid',
  `transaction_code` VARCHAR(120) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_orders_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_details` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(15,2) NOT NULL DEFAULT 0,
  INDEX `idx_order_details_order` (`order_id`),
  CONSTRAINT `fk_order_details_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Danh mục mặc định (id 1-4 khớp với seed_products.php và link trang chủ)
INSERT INTO `category` (`id`, `name`) VALUES
  (1, 'Điện thoại'),
  (2, 'Laptop'),
  (3, 'Máy tính bảng'),
  (4, 'Phụ kiện')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

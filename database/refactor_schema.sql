-- Create brands table
CREATE TABLE `brands` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create series table
CREATE TABLE `series` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Rename categories to models and add series_id
ALTER TABLE `categories` RENAME TO `models`;
ALTER TABLE `models` ADD `series_id` INT(30) NOT NULL AFTER `id`;

-- Drop sub_categories table
DROP TABLE `sub_categories`;

-- Update products table
ALTER TABLE `products` DROP COLUMN `category_id`;
ALTER TABLE `products` DROP COLUMN `sub_category_id`;
ALTER TABLE `products` ADD `model_id` INT(30) NOT NULL AFTER `id`;

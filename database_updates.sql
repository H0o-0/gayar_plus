-- تحديثات قاعدة البيانات للمتجر

-- إضافة عمود has_colors لجدول products
ALTER TABLE `products` ADD COLUMN `has_colors` TINYINT(1) DEFAULT 0 AFTER `status`;

-- إنشاء جدول product_colors لحفظ ألوان المنتجات
CREATE TABLE IF NOT EXISTS `product_colors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `color_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- تحديث جدول inventory لجعل الحقول اختيارية
ALTER TABLE `inventory` MODIFY `price` DECIMAL(10,2) NULL;
ALTER TABLE `inventory` MODIFY `quantity` INT(11) NULL;

-- إضافة فهرس للبحث السريع
ALTER TABLE `product_colors` ADD INDEX `idx_product_color` (`product_id`, `color_name`);

-- ===================================
-- سكريبت إنشاء الهيكلية الجديدة لنظام التصنيف الثلاثي
-- الشركة -> الفئة -> الجهاز -> المنتج
-- ===================================

-- جدول الشركات (brands)
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `name_ar` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الفئات/السلاسل (series)  
CREATE TABLE IF NOT EXISTS `series` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_ar` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`),
  KEY `status` (`status`),
  UNIQUE KEY `brand_series` (`brand_id`, `name`),
  FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الأجهزة/الموديلات (models)
CREATE TABLE IF NOT EXISTS `models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `series_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL, -- للوصول المباشر
  `name` varchar(100) NOT NULL,
  `name_ar` varchar(100) DEFAULT NULL,
  `model_number` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `release_year` year DEFAULT NULL,
  `specifications` longtext DEFAULT NULL, -- JSON format
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `series_id` (`series_id`),
  KEY `brand_id` (`brand_id`),
  KEY `status` (`status`),
  UNIQUE KEY `series_model` (`series_id`, `name`),
  FOREIGN KEY (`series_id`) REFERENCES `series`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- تحديث جدول المنتجات لإضافة عمود model_id
ALTER TABLE `products` 
ADD COLUMN `model_id` int(11) DEFAULT NULL AFTER `sub_category_id`,
ADD KEY `model_id` (`model_id`);

-- إضافة foreign key للربط مع جدول models (بعد ترحيل البيانات)
-- ALTER TABLE `products` 
-- ADD FOREIGN KEY (`model_id`) REFERENCES `models`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- جدول مساعد لمطابقة البيانات القديمة مع الجديدة
CREATE TABLE IF NOT EXISTS `migration_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_category_id` int(11) NOT NULL,
  `old_sub_category_id` int(11) NOT NULL,
  `new_brand_id` int(11) DEFAULT NULL,
  `new_series_id` int(11) DEFAULT NULL,
  `new_model_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `old_mapping` (`old_category_id`, `old_sub_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إدراج بيانات أساسية للشركات الشائعة
INSERT INTO `brands` (`name`, `name_ar`, `description`, `sort_order`) VALUES
('Apple', 'أبل', 'شركة آبل الأمريكية', 1),
('Samsung', 'سامسونج', 'شركة سامسونج الكورية الجنوبية', 2),
('Huawei', 'هواوي', 'شركة هواوي الصينية', 3),
('Xiaomi', 'شاومي', 'شركة شاومي الصينية', 4),
('OPPO', 'أوبو', 'شركة أوبو الصينية', 5),
('Vivo', 'فيفو', 'شركة فيفو الصينية', 6),
('OnePlus', 'ون بلس', 'شركة ون بلس الصينية', 7),
('Google', 'جوجل', 'شركة جوجل الأمريكية', 8),
('Sony', 'سوني', 'شركة سوني اليابانية', 9),
('LG', 'إل جي', 'شركة إل جي الكورية الجنوبية', 10),
('Nokia', 'نوكيا', 'شركة نوكيا الفنلندية', 11),
('Honor', 'هونر', 'شركة هونر الصينية', 12),
('Realme', 'ريلمي', 'شركة ريلمي الصينية', 13),
('Tecno', 'تكنو', 'شركة تكنو الصينية', 14),
('Infinix', 'إنفينكس', 'شركة إنفينكس الصينية', 15),
('Other', 'أخرى', 'شركات أخرى', 99);

-- إدراج بيانات أساسية لفئات آبل
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `sort_order`) VALUES
(1, 'iPhone', 'آيفون', 'سلسلة هواتف آيفون الذكية', 1),
(1, 'iPad', 'آيباد', 'سلسلة أجهزة آيباد اللوحية', 2),
(1, 'MacBook', 'ماك بوك', 'سلسلة أجهزة ماك بوك المحمولة', 3),
(1, 'iMac', 'آي ماك', 'سلسلة أجهزة آي ماك المكتبية', 4),
(1, 'Apple Watch', 'أبل ووتش', 'سلسلة ساعات أبل الذكية', 5),
(1, 'AirPods', 'إير بودز', 'سلسلة سماعات أبل اللاسلكية', 6);

-- إدراج بيانات أساسية لفئات سامسونج
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `sort_order`) VALUES
(2, 'Galaxy S', 'جالاكسي إس', 'سلسلة هواتف جالاكسي إس الرائدة', 1),
(2, 'Galaxy Note', 'جالاكسي نوت', 'سلسلة هواتف جالاكسي نوت', 2),
(2, 'Galaxy A', 'جالاكسي إيه', 'سلسلة هواتف جالاكسي إيه المتوسطة', 3),
(2, 'Galaxy M', 'جالاكسي إم', 'سلسلة هواتف جالاكسي إم الاقتصادية', 4),
(2, 'Galaxy Tab', 'جالاكسي تاب', 'سلسلة أجهزة جالاكسي تاب اللوحية', 5),
(2, 'Galaxy Watch', 'جالاكسي ووتش', 'سلسلة ساعات جالاكسي الذكية', 6),
(2, 'Galaxy Buds', 'جالاكسي بودز', 'سلسلة سماعات جالاكسي اللاسلكية', 7);

-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 29, 2025 at 06:26 AM
-- Server version: 8.0.21
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gayar_plus`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `name_ar`, `description`, `logo`, `status`, `sort_order`, `date_created`, `date_updated`) VALUES
(10, 'Apple', 'ابل', '', NULL, 1, 0, '2025-08-29 08:30:02', NULL),
(11, 'Samsung', 'سامسونج', '', NULL, 1, 0, '2025-08-29 08:30:23', NULL),
(12, 'Xiaomi', 'شاومي', '', NULL, 1, 0, '2025-08-29 08:30:39', NULL),
(13, 'Huawei', 'هواوي', '', NULL, 1, 0, '2025-08-29 08:31:04', NULL),
(14, 'google ', 'جوجل', '', NULL, 1, 0, '2025-08-29 08:31:14', NULL),
(15, 'Oppo', 'اوبو', '', NULL, 1, 0, '2025-08-29 08:31:38', NULL),
(16, 'Vivo', 'فيفو', '', NULL, 1, 0, '2025-08-29 08:31:48', NULL),
(17, 'Realme', 'ريلمي', '', NULL, 1, 0, '2025-08-29 08:32:13', NULL),
(18, 'Oneplus', 'وان بلس', '', NULL, 1, 0, '2025-08-29 08:32:35', NULL),
(19, 'Infinix', 'انفنكس', '', NULL, 1, 0, '2025-08-29 08:33:09', NULL),
(20, 'Tecno', 'تكنو', '', NULL, 1, 0, '2025-08-29 08:33:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `inventory_id` int NOT NULL,
  `price` double NOT NULL,
  `quantity` int NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(250) NOT NULL,
  `description` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category`, `description`, `status`, `date_created`) VALUES
(1, 'Food', 'Sample Description', 1, '2021-06-21 10:17:41'),
(4, 'Accessories', '&lt;p&gt;Sample Category&lt;/p&gt;', 1, '2021-06-21 16:34:04');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` text NOT NULL,
  `default_delivery_address` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `firstname`, `lastname`, `gender`, `contact`, `email`, `password`, `default_delivery_address`, `date_created`) VALUES
(1, 'John', 'Smith', 'Male', '09123456789', 'jsmith@sample.com', '1254737c076cf867dc53d60a0364f38e', 'Sample Address', '2021-06-21 16:00:23');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `quantity` double NOT NULL,
  `unit` varchar(100) NOT NULL,
  `price` float NOT NULL,
  `size` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `quantity`, `unit`, `price`, `size`, `date_created`, `date_updated`) VALUES
(1, 1, 50, 'pcs', 250, 'M', '2021-06-21 13:01:30', '2021-06-21 13:05:23'),
(2, 1, 20, 'Sample', 300, 'L', '2021-06-21 13:07:00', NULL),
(3, 4, 150, 'pcs', 500, 'M', '2021-06-21 16:50:37', NULL),
(4, 3, 50, 'pack', 150, 'M', '2021-06-21 16:51:12', NULL),
(5, 5, 30, 'pcs', 50, 'M', '2021-06-21 16:51:35', NULL),
(6, 4, 10, 'pcs', 550, 'L', '2021-06-21 16:51:54', NULL),
(7, 6, 100, 'pcs', 150, 'S', '2021-06-22 15:50:47', NULL),
(8, 6, 150, 'pcs', 180, 'M', '2021-06-22 15:51:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `models`
--

DROP TABLE IF EXISTS `models`;
CREATE TABLE IF NOT EXISTS `models` (
  `id` int NOT NULL AUTO_INCREMENT,
  `series_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `specifications` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_series_id` (`series_id`),
  KEY `idx_status` (`status`),
  KEY `idx_name` (`name`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `models`
--

INSERT INTO `models` (`id`, `series_id`, `name`, `name_ar`, `category`, `description`, `specifications`, `status`, `sort_order`, `date_created`, `date_updated`) VALUES
(1, 1, 'oiaش', NULL, NULL, '', NULL, 1, 0, '2025-08-28 17:14:43', '2025-08-28 17:17:38'),
(2, 10, 'طيز', NULL, NULL, '', NULL, 1, 0, '2025-08-28 17:22:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `delivery_address` text NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `amount` double NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `client_id`, `delivery_address`, `payment_method`, `amount`, `status`, `paid`, `date_created`, `date_updated`) VALUES
(1, 1, 'Sample Address', 'Online Payment', 1100, 2, 1, '2021-06-22 13:48:54', '2021-06-22 14:49:15'),
(2, 1, 'Sample Address', 'cod', 750, 3, 1, '2021-06-22 15:26:07', '2021-06-22 15:32:55'),
(4, 0, 'الاسم: حسنين\nالهاتف: 07516980598\nالعنوان: الكوت', 'الدفع عند الاستلام', 50000, 0, 0, '2025-08-28 16:38:03', '2025-08-28 16:38:03');

-- --------------------------------------------------------

--
-- Table structure for table `order_list`
--

DROP TABLE IF EXISTS `order_list`;
CREATE TABLE IF NOT EXISTS `order_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `size` varchar(20) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `quantity` int NOT NULL,
  `price` double NOT NULL,
  `total` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_list`
--

INSERT INTO `order_list` (`id`, `order_id`, `product_id`, `size`, `unit`, `quantity`, `price`, `total`) VALUES
(1, 1, 4, 'L', 'pcs', 2, 550, 1100),
(2, 2, 3, 'M', 'pack', 5, 150, 750);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `sub_category_id` int NOT NULL,
  `model_id` int DEFAULT NULL,
  `product_name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `has_colors` tinyint(1) DEFAULT '0',
  `colors` text,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `unit` varchar(50) DEFAULT 'قطعة',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `sub_category_id`, `model_id`, `product_name`, `description`, `status`, `has_colors`, `colors`, `price`, `quantity`, `unit`, `date_created`) VALUES
(1, 1, 1, NULL, 'Dog Food 101', '&lt;p&gt;Sample Product&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px;&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras dolor felis, mattis sit amet turpis eu, porta efficitur arcu. Ut orci est, posuere a mi sed, sollicitudin volutpat nisl. Vestibulum aliquam condimentum dictum. Sed a lobortis dolor, nec molestie nulla. Quisque purus justo, fermentum sed commodo in, hendrerit non nisi. In eleifend diam at pellentesque tempor. Mauris a augue ultrices, vulputate ipsum ac, lobortis eros. Nulla tempor odio sit amet magna finibus dignissim vitae eu turpis.&lt;/p&gt;&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px;&quot;&gt;Proin nec semper nisi. Aenean varius purus at eros aliquam, non luctus eros interdum. Etiam non nisl ut lacus semper ornare sed iaculis justo. Mauris justo mauris, faucibus sit amet pharetra at, accumsan quis felis. Nulla gravida elementum porttitor. Vestibulum blandit semper ligula sit amet laoreet. Aliquam a est consectetur, blandit odio ultricies, finibus enim. Sed gravida pretium elit, et bibendum est dignissim sed. Aliquam ultrices felis a arcu feugiat, vel porta neque luctus. Vivamus dignissim porttitor nulla, non pulvinar nulla blandit a. Sed nisi leo, volutpat in nibh sit amet, laoreet semper massa.&lt;/p&gt;', 1, 0, NULL, NULL, NULL, 'قطعة', '2021-06-21 11:19:31'),
(3, 1, 3, NULL, 'Cat Food 101', '&lt;p&gt;Cat Food 101&lt;/p&gt;&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px;&quot;&gt;Sed interdum odio a efficitur volutpat. Etiam porta erat ut quam feugiat iaculis. Nam tincidunt sem metus, quis mattis nisl iaculis id. Aliquam vehicula auctor facilisis. Etiam tincidunt id velit sed pulvinar. Mauris mi est, varius in mauris ut, rhoncus congue nisi. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Fusce mollis arcu mauris, tempor fermentum odio vehicula nec. Morbi sit amet dui mollis, sodales dolor vel, efficitur tortor. Nam vel pretium lectus. Morbi ultricies magna eget libero bibendum posuere. Ut ultrices tellus ac enim egestas feugiat.&lt;/p&gt;&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px;&quot;&gt;Mauris faucibus, erat porta auctor porttitor, ligula leo ornare sem, eu dignissim diam massa a purus. Praesent et faucibus metus. Nulla iaculis enim nec efficitur consectetur. Sed vehicula purus neque, quis luctus odio varius non. Sed hendrerit leo et velit ultricies, eget venenatis elit ornare. Pellentesque nec tincidunt nunc. Donec fringilla tristique lectus, vitae malesuada massa mollis ut. Nulla eleifend ac ligula vel rutrum.&lt;/p&gt;', 1, 0, NULL, NULL, NULL, 'قطعة', '2021-06-21 16:48:16'),
(4, 4, 4, NULL, 'Dog bed', '&lt;p&gt;Sample&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;text-align: justify;&quot;&gt;Proin nec semper nisi. Aenean varius purus at eros aliquam, non luctus eros interdum. Etiam non nisl ut lacus semper ornare sed iaculis justo. Mauris justo mauris, faucibus sit amet pharetra at, accumsan quis felis. Nulla gravida elementum porttitor. Vestibulum blandit semper ligula sit amet laoreet. Aliquam a est consectetur, blandit odio ultricies, finibus enim. Sed gravida pretium elit, et bibendum est dignissim sed. Aliquam ultrices felis a arcu feugiat, vel porta neque luctus. Vivamus dignissim porttitor nulla, non pulvinar nulla blandit a. Sed nisi leo, volutpat in nibh sit amet, laoreet semper massa.&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', 1, 0, NULL, NULL, NULL, 'قطعة', '2021-06-21 16:49:07'),
(5, 4, 5, NULL, 'Cat  Plates 623', '&lt;p&gt;&lt;span style=&quot;text-align: justify;&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras dolor felis, mattis sit amet turpis eu, porta efficitur arcu. Ut orci est, posuere a mi sed, sollicitudin volutpat nisl. Vestibulum aliquam condimentum dictum. Sed a lobortis dolor, nec molestie nulla. Quisque purus justo, fermentum sed commodo in, hendrerit non nisi. In eleifend diam at pellentesque tempor. Mauris a augue ultrices, vulputate ipsum ac, lobortis eros. Nulla tempor odio sit amet magna finibus dignissim vitae eu turpis.&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', 1, 0, NULL, NULL, NULL, 'قطعة', '2021-06-21 16:50:11'),
(6, 4, 4, NULL, 'Dog Belt', '&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px;&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas dui nulla, tincidunt in arcu at, vulputate volutpat velit. Quisque volutpat gravida erat, gravida porttitor magna malesuada sed. Curabitur massa est, ullamcorper a diam vitae, tincidunt sagittis justo. Nam eu orci ligula. Duis ullamcorper dui at nisi consequat, sed suscipit sapien lacinia. Praesent ut lacus id arcu bibendum egestas. Cras ullamcorper dictum mi, non commodo mauris iaculis a. Pellentesque porta sem id dapibus tincidunt. Aenean metus tellus, efficitur ut feugiat in, euismod et arcu. In pharetra, dolor in fermentum facilisis, metus urna lacinia metus, in maximus justo tellus et tortor. Nam pulvinar eu enim auctor pellentesque.&lt;/p&gt;&lt;p style=&quot;margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px;&quot;&gt;Nam ut quam velit. Suspendisse commodo non urna nec dictum. Pellentesque eget enim id velit bibendum auctor vel id lectus. Maecenas dolor nibh, ultricies eget metus vel, efficitur varius tellus. Donec semper eros sit amet urna bibendum scelerisque. Interdum et malesuada fames ac ante ipsum primis in faucibus. Integer cursus est in sapien sodales, quis pulvinar nisl aliquet. Pellentesque blandit diam lobortis pulvinar ornare. Sed venenatis imperdiet massa, ut mollis sapien sagittis a. Nulla dignissim ultrices metus a mattis. Nunc egestas mattis nisl at posuere. Donec malesuada ut justo sed aliquam. Sed venenatis sit amet tortor et semper. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Vivamus sit amet massa at massa malesuada volutpat quis nec libero.&lt;/p&gt;', 1, 0, NULL, NULL, NULL, 'قطعة', '2021-06-22 15:50:16'),
(7, 1, 1, 1, 'Test iPhone Case', 'Test product description', 1, 1, '[{\"name\":\"أحمر\",\"code\":\"#ff0000\"},{\"name\":\"أزرق\",\"code\":\"#0000ff\"}]', '29.99', 50, 'قطعة', '2025-08-28 17:43:16');
INSERT INTO `products` (`id`, `category_id`, `sub_category_id`, `model_id`, `product_name`, `description`, `status`, `has_colors`, `colors`, `price`, `quantity`, `unit`, `date_created`) VALUES
(8, 1, 1, 1, 'تجربة', '&lt;div style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;&lt;div class=&quot;flex w-full flex-row&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); display: flex; width: 256px;&quot;&gt;&lt;div class=&quot;flex min-w-0 flex-grow flex-col&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); display: flex; min-width: 0px; flex-grow: 1; flex-direction: column;&quot;&gt;&lt;div class=&quot;group top-0 flex w-full flex-row items-start gap-2 relative after:absolute after:block after:h-[calc(100cqh-1px)] after:w-full after:content-[&amp;quot;&amp;quot;] after:pointer-events-none&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); position: relative; top: 0px; display: flex; width: 256px; align-items: flex-start; gap: 0.5rem;&quot;&gt;&lt;div class=&quot;flex min-w-0 flex-grow flex-col items-stretch&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); display: flex; min-width: 0px; flex-grow: 1; flex-direction: column; align-items: stretch;&quot;&gt;&lt;div class=&quot;flex w-full justify-end&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); display: flex; width: 256px; justify-content: flex-end;&quot;&gt;&lt;div class=&quot;relative rounded-[8px] border border-[#7d7d7d20] bg-ide-input-background px-[9px] py-[6px] [&amp;amp;_p]:text-[12px] cursor-pointer hover:bg-[color-mix(in_srgb,var(--codeium-input-background)_70%,var(--codeium-chat-background)_30%)]&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-color: rgba(125, 125, 125, 0.125); position: relative; cursor: pointer; border-radius: 8px; background-color: var(--codeium-input-background); padding: 6px 9px;&quot;&gt;&lt;div class=&quot;relative&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); position: relative;&quot;&gt;&lt;div aria-autocomplete=&quot;none&quot; aria-readonly=&quot;true&quot; class=&quot;z-1 mark-js-ignore&quot; contenteditable=&quot;false&quot; role=&quot;textbox&quot; spellcheck=&quot;true&quot; data-lexical-editor=&quot;true&quot; dir=&quot;rtl&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); user-select: text; white-space-collapse: preserve; word-break: break-word;&quot;&gt;&lt;p class=&quot;&quot; dir=&quot;rtl&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); margin-right: 0px; margin-bottom: 0px; margin-left: 0px; font-size: 12px;&quot;&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;البلوكات اسمائها بالانكليزي رغم ججدول البراندات  بالعربي و اللوغو الخاص بيهن ماكو ماموجود + كل اللوغو بملف admin\\images&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;( ! )&amp;nbsp;Notice: Undefined index: category in C:\\wamp64\\www\\gayar_plus\\brand_products.php on line&amp;nbsp;44Call Stack#TimeMemoryFunctionLocation&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;1&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;0.0002&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;416632&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;{main}( )&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;...\\brand_products.php:0&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;( ! )Notice: Undefined index: category in C:\\wamp64\\www\\gayar_plus\\brand_products.php on line55Call Stack #TimeMemoryFunctionLocation 10.0002416632{main}( )...\\brand_products.php:0 &quot; class=&quot;brand-logo&quot;&amp;gt;&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;( ! )&amp;nbsp;Notice: Undefined index: category in C:\\wamp64\\www\\gayar_plus\\brand_products.php on line&amp;nbsp;58Call Stack#TimeMemoryFunctionLocation&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;1&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;0.0002&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;416632&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;{main}( )&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;...\\brand_products.php:0&lt;/span&gt;&lt;span data-lexical-text=&quot;true&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;\r\n&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198);&quot;&gt;&lt;div class=&quot;flex flex-col gap-1.5&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); display: flex; flex-direction: column; gap: 0.375rem;&quot;&gt;&lt;div class=&quot;relative flex flex-row&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); position: relative; display: flex;&quot;&gt;&lt;div class=&quot;flex min-w-0 flex-grow flex-col gap-1&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); display: flex; min-width: 0px; flex-grow: 1; flex-direction: column; gap: 0.25rem;&quot;&gt;&lt;div class=&quot;flex min-w-0 flex-col gap-1 text-[12px] text-[color-mix(in_srgb,var(--codeium-editor-color)_50%,#0000)]&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); display: flex; min-width: 0px; flex-direction: column; gap: 0.25rem; font-size: 12px; color: color-mix(in srgb,var(--codeium-editor-color) 50%,rgba(0,0,0,0));&quot;&gt;&lt;div class=&quot;-my-1 flex min-w-0 flex-row items-center gap-1 py-1 group cursor-pointer select-none&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(67, 128, 180, 0.5); --tw-ring-offset-shadow: 0 0 rgba(0,0,0,0); --tw-ring-shadow: 0 0 rgba(0,0,0,0); --tw-shadow: 0 0 rgba(0,0,0,0); --tw-shadow-colored: 0 0 rgba(0,0,0,0); --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(192, 193, 198); margin-top: -0.25rem; margin-bottom: -0.25rem; display: flex; min-width: 0px; cursor: pointer; user-select: none; align-items: center; gap: 0.25rem;&quot;&gt;&lt;div class=&quot;flex min-w-0 items-center gap-1 overflow-hidden&quot; style=&quot;--tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-r', 1, 1, '[{\"name\":\"ابيض\",\"code\":\"#007bff\"}]', '0.00', 0, 'قطعة', '2025-08-28 17:46:51');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `total_amount` double NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `order_id`, `total_amount`, `date_created`) VALUES
(1, 1, 1100, '2021-06-22 13:48:54'),
(2, 2, 750, '2021-06-22 15:26:07');

-- --------------------------------------------------------

--
-- Table structure for table `series`
--

DROP TABLE IF EXISTS `series`;
CREATE TABLE IF NOT EXISTS `series` (
  `id` int NOT NULL AUTO_INCREMENT,
  `brand_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_brand_id` (`brand_id`),
  KEY `idx_status` (`status`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `series`
--

INSERT INTO `series` (`id`, `brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`, `date_created`, `date_updated`) VALUES
(1, 1, 'aio', NULL, '', 1, 0, '2025-08-28 16:52:48', NULL),
(2, 1, 'iPad', 'آيباد', 'Apple tablet series', 1, 0, '2025-08-28 17:06:46', NULL),
(3, 2, 'Galaxy S', 'جالاكسي إس', 'Samsung flagship smartphone series', 1, 0, '2025-08-28 17:06:46', NULL),
(4, 2, 'Galaxy Note', 'جالاكسي نوت', 'Samsung phablet series', 1, 0, '2025-08-28 17:06:46', NULL),
(5, 2, 'Galaxy A', 'جالاكسي إيه', 'Samsung mid-range smartphone series', 1, 0, '2025-08-28 17:06:46', NULL),
(6, 3, 'P Series', 'سلسلة بي', 'Huawei flagship smartphone series', 1, 0, '2025-08-28 17:06:46', NULL),
(7, 3, 'Mate Series', 'سلسلة مايت', 'Huawei premium smartphone series', 1, 0, '2025-08-28 17:06:46', NULL),
(8, 4, 'Mi', 'مي', 'Xiaomi flagship smartphone series', 1, 0, '2025-08-28 17:06:46', NULL),
(9, 4, 'Redmi', 'ريدمي', 'Xiaomi budget smartphone series', 1, 0, '2025-08-28 17:06:46', NULL),
(10, 9, 'كسي', NULL, '', 1, 0, '2025-08-28 17:21:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

DROP TABLE IF EXISTS `sizes`;
CREATE TABLE IF NOT EXISTS `sizes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `size` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`id`, `size`) VALUES
(1, 'xs'),
(2, 's'),
(3, 'm'),
(4, 'l'),
(5, 'xl'),
(6, 'None');

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

DROP TABLE IF EXISTS `sub_categories`;
CREATE TABLE IF NOT EXISTS `sub_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL,
  `sub_category` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `parent_id`, `sub_category`, `description`, `status`, `date_created`) VALUES
(1, 1, 'Dog Food', '&lt;p&gt;Sample only&lt;/p&gt;', 1, '2021-06-21 10:58:32'),
(3, 1, 'Cat Food', '&lt;p&gt;Sample&lt;/p&gt;', 1, '2021-06-21 16:34:59'),
(4, 4, 'Dog Needs', '&lt;p&gt;Sample&amp;nbsp;&lt;/p&gt;', 1, '2021-06-21 16:35:26'),
(5, 4, 'Cat Needs', '&lt;p&gt;Sample&lt;/p&gt;', 1, '2021-06-21 16:35:36');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

DROP TABLE IF EXISTS `system_info`;
CREATE TABLE IF NOT EXISTS `system_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Pet Shop Food and Accessories Shop'),
(6, 'short_name', 'Pet Needs'),
(11, 'logo', 'uploads/1624240440_paw.png'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/1624240440_banner1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `temp_warehouse`
--

DROP TABLE IF EXISTS `temp_warehouse`;
CREATE TABLE IF NOT EXISTS `temp_warehouse` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_name` text NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `suggested_brand` varchar(100) DEFAULT NULL,
  `confirmed_brand` varchar(100) DEFAULT NULL,
  `suggested_type` varchar(100) DEFAULT NULL,
  `confirmed_type` varchar(100) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `sub_category_id` int DEFAULT NULL,
  `status` enum('unclassified','classified','published') DEFAULT 'unclassified',
  `import_batch` varchar(50) DEFAULT NULL,
  `raw_data` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_brand` (`suggested_brand`),
  KEY `idx_status` (`status`),
  KEY `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`) VALUES
(1, 'Adminstrator', 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 'uploads/1624240500_avatar.png', '2025-08-29 08:24:30', 1, '2021-01-20 14:02:37', '2025-08-29 08:24:30'),
(4, 'John', 'Smith', 'jsmith', '1254737c076cf867dc53d60a0364f38e', NULL, NULL, 0, '2021-06-19 08:36:09', '2021-06-19 10:53:12'),
(5, 'Claire', 'Blake', 'cblake', '4744ddea876b11dcb1d169fadf494418', NULL, NULL, 0, '2021-06-19 10:01:51', '2021-06-19 12:03:23');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

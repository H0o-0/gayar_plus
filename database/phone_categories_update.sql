-- تحديث فئات الهواتف في قاعدة البيانات
-- Phone Categories Database Update

-- أولاً نحذف البيانات الموجودة في جدول series
DELETE FROM `series`;

-- إعادة تعيين AUTO_INCREMENT
ALTER TABLE `series` AUTO_INCREMENT = 1;

-- إدراج فئات الهواتف لكل براند

-- سامسونج (Samsung) - Brand ID: 11
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(11, 'Galaxy A Series', 'جالاكسي إيه (اقتصادية)', 'الفئة الاقتصادية من سامسونج', 1, 1),
(11, 'Galaxy M Series', 'جالاكسي إم (متوسطة)', 'الفئة المتوسطة من سامسونج', 1, 2),
(11, 'Galaxy A Series Premium', 'جالاكسي إيه المتقدمة', 'الفئة المتوسطة العليا (A3x, A5x, A7x)', 1, 3),
(11, 'Galaxy S Series', 'جالاكسي إس (رائدة)', 'الفئة الرائدة من سامسونج', 1, 4),
(11, 'Galaxy Z Series', 'جالاكسي زد (قابلة للطي)', 'الفئة الفريدة القابلة للطي (Fold و Flip)', 1, 5),

-- أبل (Apple) - Brand ID: 10
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(10, 'iPhone SE', 'آيفون إس إي', 'الفئة الاقتصادية من آبل', 1, 1),
(10, 'iPhone Standard', 'آيفون القياسي', 'iPhone و iPhone Plus', 1, 2),
(10, 'iPhone Pro', 'آيفون برو', 'iPhone Pro و iPhone Pro Max', 1, 3),

-- شاومي (Xiaomi) - Brand ID: 12
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(12, 'Redmi Series', 'ريدمي (اقتصادية)', 'الفئة الاقتصادية من شاومي', 1, 1),
(12, 'Redmi Note Series', 'ريدمي نوت (متوسطة)', 'الفئة المتوسطة من شاومي', 1, 2),
(12, 'POCO M Series', 'بوكو إم', 'فئة POCO الاقتصادية', 1, 3),
(12, 'POCO X Series', 'بوكو إكس', 'فئة POCO المتوسطة', 1, 4),
(12, 'POCO F Series', 'بوكو إف', 'فئة POCO المتوسطة العليا', 1, 5),
(12, 'Xiaomi Series', 'شاومي (رائدة)', 'الفئة الرائدة (Mi سابقاً)', 1, 6),
(12, 'MIX Series', 'مكس سيريز', 'فئة MIX الرائدة', 1, 7),

-- أوبو (OPPO) - Brand ID: 15
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(15, 'Oppo A Series', 'أوبو إيه (اقتصادية)', 'الفئة الاقتصادية من أوبو', 1, 1),
(15, 'Oppo K Series', 'أوبو كيه (متوسطة)', 'الفئة المتوسطة من أوبو', 1, 2),
(15, 'Oppo Reno Series', 'أوبو رينو', 'الفئة المتوسطة العليا والرائدة', 1, 3),
(15, 'Oppo Find X Series', 'أوبو فايند إكس', 'الفئة الأعلى رائدة', 1, 4),

-- ريلمي (realme) - Brand ID: 17
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(17, 'Realme C Series', 'ريلمي سي (اقتصادية)', 'الفئة الاقتصادية من ريلمي', 1, 1),
(17, 'Realme Narzo Series', 'ريلمي نارزو', 'الفئة المتوسطة من ريلمي', 1, 2),
(17, 'Realme Number Series', 'ريلمي الرقمية', 'الفئة المتوسطة العليا', 1, 3),
(17, 'Realme GT Series', 'ريلمي جي تي', 'الفئة الرائدة من ريلمي', 1, 4),

-- هواوي (Huawei) - Brand ID: 13
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(13, 'Huawei Y Series', 'هواوي واي (اقتصادية)', 'الفئة الاقتصادية من هواوي', 1, 1),
(13, 'Huawei Nova Series', 'هواوي نوفا (متوسطة)', 'الفئة المتوسطة من هواوي', 1, 2),
(13, 'Huawei P Series', 'هواوي بي (رائدة)', 'الفئة الرائدة من هواوي', 1, 3),
(13, 'Huawei Mate Series', 'هواوي مايت (رائدة)', 'الفئة الرائدة المتقدمة', 1, 4),

-- فيفو (vivo) - Brand ID: 16
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(16, 'Vivo Y Series', 'فيفو واي (اقتصادية)', 'الفئة الاقتصادية من فيفو', 1, 1),
(16, 'Vivo V Series', 'فيفو في (متوسطة)', 'الفئة المتوسطة من فيفو', 1, 2),
(16, 'Vivo X Series', 'فيفو إكس (رائدة)', 'الفئة الرائدة من فيفو', 1, 3),

-- انفنكس (Infinix) - Brand ID: 19
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(19, 'Infinix Smart Series', 'انفنكس سمارت', 'الفئة الاقتصادية الأساسية', 1, 1),
(19, 'Infinix Hot Series', 'انفنكس هوت', 'الفئة الاقتصادية المحسنة', 1, 2),
(19, 'Infinix Note Series', 'انفنكس نوت (متوسطة)', 'الفئة المتوسطة من انفنكس', 1, 3),
(19, 'Infinix Zero Series', 'انفنكس زيرو (رائدة)', 'الفئة الرائدة من انفنكس', 1, 4),

-- تكنو (Tecno) - Brand ID: 20
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(20, 'Tecno Spark Series', 'تكنو سبارك', 'الفئة الاقتصادية الأساسية', 1, 1),
(20, 'Tecno Pop Series', 'تكنو بوب', 'الفئة الاقتصادية البسيطة', 1, 2),
(20, 'Tecno Camon Series', 'تكنو كامون (متوسطة)', 'الفئة المتوسطة من تكنو', 1, 3),
(20, 'Tecno Phantom Series', 'تكنو فانتوم (رائدة)', 'الفئة الرائدة من تكنو', 1, 4),

-- وان بلس (OnePlus) - Brand ID: 18
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(18, 'OnePlus Nord Series', 'وان بلس نورد', 'الفئة المتوسطة العليا', 1, 1),
(18, 'OnePlus Series', 'وان بلس (رائدة)', 'الفئة الرائدة من وان بلس', 1, 2),

-- جوجل (Google) - Brand ID: 14
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(14, 'Pixel A Series', 'بكسل إيه', 'الفئة الاقتصادية من جوجل', 1, 1),
(14, 'Pixel Series', 'بكسل', 'الفئة الرائدة من جوجل', 1, 2),
(14, 'Pixel Pro Series', 'بكسل برو', 'الفئة الرائدة المتقدمة', 1, 3),
(14, 'Pixel Fold Series', 'بكسل فولد', 'الفئة القابلة للطي', 1, 4);

-- إضافة براندات مفقودة إذا لزم الأمر

-- هونر (Honor) - نحتاج إضافة البراند أولاً
INSERT INTO `brands` (`name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
('Honor', 'هونر', 'براند هونر للهواتف الذكية', 1, 0);

-- الحصول على ID الجديد لهونر (سيكون 21)
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(21, 'Honor X Series', 'هونر إكس', 'الفئة الاقتصادية والمتوسطة', 1, 1),
(21, 'Honor Play Series', 'هونر بلاي', 'فئة الألعاب المتوسطة', 1, 2),
(21, 'Honor Number Series', 'هونر الرقمية', 'الفئة المتوسطة العليا', 1, 3),
(21, 'Honor Magic Series', 'هونر ماجيك (رائدة)', 'الفئة الرائدة من هونر', 1, 4);

-- ايتل (itel) - نحتاج إضافة البراند أولاً
INSERT INTO `brands` (`name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
('itel', 'ايتل', 'براند ايتل للهواتف الاقتصادية', 1, 0);

-- الحصول على ID الجديد لايتل (سيكون 22)
INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES
(22, 'Itel A Series', 'ايتل إيه', 'الفئة الاقتصادية الأساسية', 1, 1),
(22, 'Itel P Series', 'ايتل بي', 'الفئة الاقتصادية المحسنة', 1, 2);

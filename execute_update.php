<?php
// تنفيذ تحديث فئات الهواتف
require_once('config.php');

echo "<h2>تحديث فئات الهواتف</h2>";

try {
    // حذف البيانات الموجودة في جدول series
    $conn->query("DELETE FROM `series`");
    echo "✓ تم حذف البيانات الموجودة<br>";
    
    // إعادة تعيين AUTO_INCREMENT
    $conn->query("ALTER TABLE `series` AUTO_INCREMENT = 1");
    echo "✓ تم إعادة تعيين AUTO_INCREMENT<br>";
    
    // إضافة براندات مفقودة
    $missing_brands = [
        ['Honor', 'هونر', 'براند هونر للهواتف الذكية'],
        ['itel', 'ايتل', 'براند ايتل للهواتف الاقتصادية']
    ];
    
    foreach ($missing_brands as $brand) {
        // فحص إذا كان البراند موجود
        $check = $conn->prepare("SELECT id FROM brands WHERE name = ?");
        $check->bind_param("s", $brand[0]);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO `brands` (`name`, `name_ar`, `description`, `status`, `sort_order`) VALUES (?, ?, ?, 1, 0)");
            $stmt->bind_param("sss", $brand[0], $brand[1], $brand[2]);
            $stmt->execute();
            echo "✓ تم إضافة براند: " . $brand[0] . "<br>";
        }
    }
    
    // الحصول على IDs للبراندات
    $brand_ids = [];
    $brands_query = $conn->query("SELECT id, name FROM brands");
    while ($row = $brands_query->fetch_assoc()) {
        $brand_ids[$row['name']] = $row['id'];
    }
    
    // إدراج فئات الهواتف
    $categories = [
        // سامسونج (Samsung)
        [$brand_ids['Samsung'], 'Galaxy A Series', 'جالاكسي إيه (اقتصادية)', 'الفئة الاقتصادية من سامسونج', 1],
        [$brand_ids['Samsung'], 'Galaxy M Series', 'جالاكسي إم (متوسطة)', 'الفئة المتوسطة من سامسونج', 2],
        [$brand_ids['Samsung'], 'Galaxy A Series Premium', 'جالاكسي إيه المتقدمة', 'الفئة المتوسطة العليا (A3x, A5x, A7x)', 3],
        [$brand_ids['Samsung'], 'Galaxy S Series', 'جالاكسي إس (رائدة)', 'الفئة الرائدة من سامسونج', 4],
        [$brand_ids['Samsung'], 'Galaxy Z Series', 'جالاكسي زد (قابلة للطي)', 'الفئة الفريدة القابلة للطي (Fold و Flip)', 5],
        
        // أبل (Apple)
        [$brand_ids['Apple'], 'iPhone SE', 'آيفون إس إي', 'الفئة الاقتصادية من آبل', 1],
        [$brand_ids['Apple'], 'iPhone Standard', 'آيفون القياسي', 'iPhone و iPhone Plus', 2],
        [$brand_ids['Apple'], 'iPhone Pro', 'آيفون برو', 'iPhone Pro و iPhone Pro Max', 3],
        
        // شاومي (Xiaomi)
        [$brand_ids['Xiaomi'], 'Redmi Series', 'ريدمي (اقتصادية)', 'الفئة الاقتصادية من شاومي', 1],
        [$brand_ids['Xiaomi'], 'Redmi Note Series', 'ريدمي نوت (متوسطة)', 'الفئة المتوسطة من شاومي', 2],
        [$brand_ids['Xiaomi'], 'POCO M Series', 'بوكو إم', 'فئة POCO الاقتصادية', 3],
        [$brand_ids['Xiaomi'], 'POCO X Series', 'بوكو إكس', 'فئة POCO المتوسطة', 4],
        [$brand_ids['Xiaomi'], 'POCO F Series', 'بوكو إف', 'فئة POCO المتوسطة العليا', 5],
        [$brand_ids['Xiaomi'], 'Xiaomi Series', 'شاومي (رائدة)', 'الفئة الرائدة (Mi سابقاً)', 6],
        [$brand_ids['Xiaomi'], 'MIX Series', 'مكس سيريز', 'فئة MIX الرائدة', 7],
        
        // أوبو (OPPO)
        [$brand_ids['Oppo'], 'Oppo A Series', 'أوبو إيه (اقتصادية)', 'الفئة الاقتصادية من أوبو', 1],
        [$brand_ids['Oppo'], 'Oppo K Series', 'أوبو كيه (متوسطة)', 'الفئة المتوسطة من أوبو', 2],
        [$brand_ids['Oppo'], 'Oppo Reno Series', 'أوبو رينو', 'الفئة المتوسطة العليا والرائدة', 3],
        [$brand_ids['Oppo'], 'Oppo Find X Series', 'أوبو فايند إكس', 'الفئة الأعلى رائدة', 4],
        
        // ريلمي (realme)
        [$brand_ids['Realme'], 'Realme C Series', 'ريلمي سي (اقتصادية)', 'الفئة الاقتصادية من ريلمي', 1],
        [$brand_ids['Realme'], 'Realme Narzo Series', 'ريلمي نارزو', 'الفئة المتوسطة من ريلمي', 2],
        [$brand_ids['Realme'], 'Realme Number Series', 'ريلمي الرقمية', 'الفئة المتوسطة العليا', 3],
        [$brand_ids['Realme'], 'Realme GT Series', 'ريلمي جي تي', 'الفئة الرائدة من ريلمي', 4],
        
        // هواوي (Huawei)
        [$brand_ids['Huawei'], 'Huawei Y Series', 'هواوي واي (اقتصادية)', 'الفئة الاقتصادية من هواوي', 1],
        [$brand_ids['Huawei'], 'Huawei Nova Series', 'هواوي نوفا (متوسطة)', 'الفئة المتوسطة من هواوي', 2],
        [$brand_ids['Huawei'], 'Huawei P Series', 'هواوي بي (رائدة)', 'الفئة الرائدة من هواوي', 3],
        [$brand_ids['Huawei'], 'Huawei Mate Series', 'هواوي مايت (رائدة)', 'الفئة الرائدة المتقدمة', 4],
        
        // فيفو (vivo)
        [$brand_ids['Vivo'], 'Vivo Y Series', 'فيفو واي (اقتصادية)', 'الفئة الاقتصادية من فيفو', 1],
        [$brand_ids['Vivo'], 'Vivo V Series', 'فيفو في (متوسطة)', 'الفئة المتوسطة من فيفو', 2],
        [$brand_ids['Vivo'], 'Vivo X Series', 'فيفو إكس (رائدة)', 'الفئة الرائدة من فيفو', 3],
        
        // انفنكس (Infinix)
        [$brand_ids['Infinix'], 'Infinix Smart Series', 'انفنكس سمارت', 'الفئة الاقتصادية الأساسية', 1],
        [$brand_ids['Infinix'], 'Infinix Hot Series', 'انفنكس هوت', 'الفئة الاقتصادية المحسنة', 2],
        [$brand_ids['Infinix'], 'Infinix Note Series', 'انفنكس نوت (متوسطة)', 'الفئة المتوسطة من انفنكس', 3],
        [$brand_ids['Infinix'], 'Infinix Zero Series', 'انفنكس زيرو (رائدة)', 'الفئة الرائدة من انفنكس', 4],
        
        // تكنو (Tecno)
        [$brand_ids['Tecno'], 'Tecno Spark Series', 'تكنو سبارك', 'الفئة الاقتصادية الأساسية', 1],
        [$brand_ids['Tecno'], 'Tecno Pop Series', 'تكنو بوب', 'الفئة الاقتصادية البسيطة', 2],
        [$brand_ids['Tecno'], 'Tecno Camon Series', 'تكنو كامون (متوسطة)', 'الفئة المتوسطة من تكنو', 3],
        [$brand_ids['Tecno'], 'Tecno Phantom Series', 'تكنو فانتوم (رائدة)', 'الفئة الرائدة من تكنو', 4],
        
        // وان بلس (OnePlus)
        [$brand_ids['Oneplus'], 'OnePlus Nord Series', 'وان بلس نورد', 'الفئة المتوسطة العليا', 1],
        [$brand_ids['Oneplus'], 'OnePlus Series', 'وان بلس (رائدة)', 'الفئة الرائدة من وان بلس', 2],
        
        // جوجل (Google)
        [$brand_ids['google '], 'Pixel A Series', 'بكسل إيه', 'الفئة الاقتصادية من جوجل', 1],
        [$brand_ids['google '], 'Pixel Series', 'بكسل', 'الفئة الرائدة من جوجل', 2],
        [$brand_ids['google '], 'Pixel Pro Series', 'بكسل برو', 'الفئة الرائدة المتقدمة', 3],
        [$brand_ids['google '], 'Pixel Fold Series', 'بكسل فولد', 'الفئة القابلة للطي', 4],
        
        // هونر (Honor)
        [$brand_ids['Honor'], 'Honor X Series', 'هونر إكس', 'الفئة الاقتصادية والمتوسطة', 1],
        [$brand_ids['Honor'], 'Honor Play Series', 'هونر بلاي', 'فئة الألعاب المتوسطة', 2],
        [$brand_ids['Honor'], 'Honor Number Series', 'هونر الرقمية', 'الفئة المتوسطة العليا', 3],
        [$brand_ids['Honor'], 'Honor Magic Series', 'هونر ماجيك (رائدة)', 'الفئة الرائدة من هونر', 4],
        
        // ايتل (itel)
        [$brand_ids['itel'], 'Itel A Series', 'ايتل إيه', 'الفئة الاقتصادية الأساسية', 1],
        [$brand_ids['itel'], 'Itel P Series', 'ايتل بي', 'الفئة الاقتصادية المحسنة', 2]
    ];
    
    // إدراج الفئات
    $stmt = $conn->prepare("INSERT INTO `series` (`brand_id`, `name`, `name_ar`, `description`, `status`, `sort_order`) VALUES (?, ?, ?, ?, 1, ?)");
    
    $success_count = 0;
    foreach ($categories as $category) {
        if (isset($category[0]) && $category[0]) {
            $stmt->bind_param("isssi", $category[0], $category[1], $category[2], $category[3], $category[4]);
            if ($stmt->execute()) {
                $success_count++;
            }
        }
    }
    
    echo "<br><h3>النتائج:</h3>";
    echo "✓ تم إضافة $success_count فئة هاتف بنجاح<br>";
    echo "✓ تم تحديث قاعدة البيانات بالكامل<br>";
    
    // عرض النتائج
    echo "<br><h3>فئات الهواتف حسب البراند:</h3>";
    $query = "SELECT b.name as brand_name, b.name_ar as brand_name_ar, s.name as series_name, s.name_ar as series_name_ar 
              FROM brands b 
              LEFT JOIN series s ON b.id = s.brand_id 
              WHERE s.id IS NOT NULL
              ORDER BY b.name, s.sort_order";
    
    $result = $conn->query($query);
    $current_brand = '';
    
    while ($row = $result->fetch_assoc()) {
        if ($current_brand != $row['brand_name']) {
            $current_brand = $row['brand_name'];
            echo "<h4>{$row['brand_name']} ({$row['brand_name_ar']})</h4>";
        }
        echo "- {$row['series_name']} ({$row['series_name_ar']})<br>";
    }
    
} catch (Exception $e) {
    echo "خطأ: " . $e->getMessage();
}
?>

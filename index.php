<?php require_once('config.php'); ?>
<?php
// معالجة طلبات AJAX
if(isset($_POST['action']) && $_POST['action'] == 'get_inventory') {
    // تسجيل معلومات الطلب للتصحيح
    error_log("Processing get_inventory request: " . json_encode($_POST));
    
    // التأكد من تضمين الملفات المطلوبة
    require_once('classes/Master.php');
    
    // إنشاء كائن Master
    $master = new Master();
    
    // الحصول على بيانات المخزون
    $result = $master->get_inventory();
    
    // تسجيل النتيجة للتصحيح
    error_log("get_inventory result: {$result}");
    
    // ضبط نوع المحتوى كـ JSON
    header('Content-Type: application/json');
    
    // إرسال النتيجة
    echo $result;
    exit;
}

// إعادة توجيه إلى الصفحة النظيفة مع التصميم الحديث
$page = isset($_GET['p']) ? $_GET['p'] : 'home';

// التحقق من وجود الصفحة
if($page == 'home' || $page == '') {
    // استخدام الصفحة الرئيسية المطورة مع نظام السلة الجديد
    include 'home.php';
    exit;
}

// للصفحات الأخرى، استخدم النظام العادي مع التحديثات
if(!file_exists($page.".php") && !is_dir($page)){
    include '404.html';
} else {
    // تحميل header حديث (يشمل topBarNav.php داخلياً)
    require_once('inc/header.php');
    
    // إزالة تضمين topBarNav.php المنفصل لمنع التكرار
    // require_once('inc/topBarNav.php'); // تمت إزالة هذا السطر
    
    // تحميل المحتوى
    if(is_dir($page))
        include $page.'/index.php';
    else
        include $page.'.php';
    
    // تحميل footer حديث
    require_once('inc/modern-footer.php');
}
?>
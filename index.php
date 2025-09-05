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

// نظام التوجيه العادي
$page = isset($_GET['p']) ? $_GET['p'] : 'home';

// التحقق من وجود الصفحة
if(!file_exists($page.".php") && !is_dir($page)){
    include '404.html';
} else {
    // تحميل المحتوى مباشرة
    if(is_dir($page))
        include $page.'/index.php';
    else
        include $page.'.php';
}
?>
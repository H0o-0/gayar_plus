<?php
// إعدادات أساسية
ob_start();
ini_set('date.timezone','Asia/Baghdad');
date_default_timezone_set('Asia/Baghdad');

// بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// إعدادات قاعدة البيانات
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'gayar_plus');

// إعدادات الموقع
define('base_url', 'http://localhost/gayar_plus/');
define('BASE_URL', base_url);

// إنشاء الاتصال بقاعدة البيانات
try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
    }
    
    // تعيين الترميز لدعم أفضل للعربية
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("خطأ في الاتصال: " . $e->getMessage());
}

// دوال مساعدة
function redirect($url=''){
    if(!empty($url)) {
        echo '<script>location.href="'.$url.'"</script>';
    }
}

function validate_image($file){
    if(!empty($file)){
        $full_path = __DIR__ . '/' . $file;
        if(file_exists($full_path)){
            return $file;
        }
    }
    return './assets/images/no-image.svg';
}

function isMobileDevice(){
    $aMobileUA = array(
        '/iphone/i' => 'iPhone', 
        '/ipod/i' => 'iPod', 
        '/ipad/i' => 'iPad', 
        '/android/i' => 'Android', 
        '/blackberry/i' => 'BlackBerry', 
        '/webos/i' => 'Mobile'
    );

    foreach($aMobileUA as $sMobileKey => $sMobileOS){
        if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
            return true;
        }
    }
    return false;
}

// Note: Removed ob_end_flush() to prevent conflicts with output buffering in specific pages
?>
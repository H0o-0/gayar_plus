<?php
$dev_data = array('id'=>'-1','firstname'=>'Developer','lastname'=>'','username'=>'dev_oretnom','password'=>'5da283a2d990e8d8512cf967df5bc0d0','last_login'=>'','date_updated'=>'','date_added'=>'');

if(!defined('base_url')) define('base_url','http://localhost/gayar_plus/');

// تعريف مسار التطبيق الأساسي بطريقة متوافقة مع ويندوز
if(!defined('base_app')) {
    $dir_path = str_replace('\\', '/', __DIR__);
    $dir_path = rtrim($dir_path, '/').'/';
    define('base_app', $dir_path);
    // تسجيل المسار للتصحيح
    error_log("Base App Path: {$dir_path}");
}

// إعدادات قاعدة البيانات
if(!defined('DB_SERVER')) define('DB_SERVER',"localhost");
if(!defined('DB_USERNAME')) define('DB_USERNAME',"root");
if(!defined('DB_PASSWORD')) define('DB_PASSWORD',"");
if(!defined('DB_NAME')) define('DB_NAME',"gayar_plus");

// تسجيل إعدادات قاعدة البيانات للتصحيح
error_log("Database settings - Server: " . DB_SERVER . ", Database: " . DB_NAME . ", User: " . DB_USERNAME);

// تحميل الكلاسات
require_once('classes/DBConnection.php');
require_once('classes/SystemSettings.php');
require_once('classes/Master.php');

// إنشاء كائنات النظام
try {
    $db = new DBConnection;
    $conn = $db->conn;
    
    // التحقق من صحة الاتصال
    if (!$conn || $conn->connect_error) {
        error_log("Database connection failed in initialize.php: " . ($conn ? $conn->connect_error : 'No connection object'));
        throw new Exception('فشل الاتصال بقاعدة البيانات في initialize.php');
    }
    
    // اختبار الاتصال
    if (!$conn->ping()) {
        error_log("Database ping failed in initialize.php");
        throw new Exception('فشل في اختبار الاتصال بقاعدة البيانات في initialize.php');
    }
    
    error_log("Database connection successful in initialize.php");
    
} catch (Exception $e) {
    error_log("Initialize error: " . $e->getMessage());
    // لا نوقف التطبيق، فقط نسجل الخطأ
}

ob_start();
ini_set('date.timezone','Asia/Baghdad');
date_default_timezone_set('Asia/Baghdad');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// إنشاء كائن الإعدادات
try {
    $_settings = new SystemSettings();
} catch (Exception $e) {
    error_log("SystemSettings error: " . $e->getMessage());
}

// دوال مساعدة - Only define if they don't already exist
if (!function_exists('redirect')) {
    function redirect($url=''){
        if(!empty($url)) {
            echo '<script>location.href="'.$url.'"</script>';
        }
    }
}

if (!function_exists('validate_image')) {
    function validate_image($file){
        if(!empty($file)){
            $full_path = __DIR__ . '/' . $file;
            if(file_exists($full_path)){
                return $file;
            }
        }
        return './assets/images/no-image.svg';
    }
}

if (!function_exists('isMobileDevice')) {
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
}

?>
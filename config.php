<?php
ob_start();
ini_set('date.timezone','Asia/Baghdad');
date_default_timezone_set('Asia/Baghdad');
session_start();

require_once('initialize.php');
require_once('classes/DBConnection.php');
require_once('classes/SystemSettings.php');
$db = new DBConnection;
$conn = $db->conn;

function redirect($url=''){
	if(!empty($url))
	echo '<script>location.href="'.base_url .$url.'"</script>';
}
function validate_image($file){
	if(!empty($file)){
			// exit;
		if(is_file(base_app.$file)){
			return base_url.$file;
		}else{
			return base_url.'dist/img/no-image-available.png';
		}
	}else{
		return base_url.'dist/img/no-image-available.png';
	}
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

    //Return true if Mobile User Agent is detected
    foreach($aMobileUA as $sMobileKey => $sMobileOS){
        if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
            return true;
        }
    }
    //Otherwise return false..  
    return false;
}
function clear_specific_tables($conn){
    // حذف جميع البيانات من جدول السلاسل (series)
    $conn->query("DELETE FROM `series`");

    // حذف جميع البيانات من جدول الموديلات (models)
    $conn->query("DELETE FROM `models`");

    // التحقق من أن جدول العلامات التجارية (brands) لم يتغير
    echo "تم حذف البيانات من جداول series و models بنجاح. بيانات brands بقاءت كما هي.";
}

ob_end_flush();
?>
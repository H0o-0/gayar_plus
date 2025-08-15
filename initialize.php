<?php
$dev_data = array('id'=>'-1','firstname'=>'Developer','lastname'=>'','username'=>'dev_oretnom','password'=>'5da283a2d990e8d8512cf967df5bc0d0','last_login'=>'','date_updated'=>'','date_added'=>'');
if(!defined('base_url')) define('base_url','http://localhost/pet_shop/');
if(!defined('base_app')) define('base_app', str_replace('\\','/',__DIR__).'/' );
if(!defined('dev_data')) define('dev_data',$dev_data);
if(!defined('DB_SERVER')) define('DB_SERVER',"localhost");
if(!defined('DB_USERNAME')) define('DB_USERNAME',"root");
if(!defined('DB_PASSWORD')) define('DB_PASSWORD',"");
if(!defined('DB_NAME')) define('DB_NAME',"pet_shop_db");

// الاتصال بقاعدة البيانات
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// تحميل الكلاسات
require_once('classes/DBConnection.php');
require_once('classes/SystemSettings.php');
require_once('classes/Master.php');

// إنشاء كائنات النظام
$db = new DBConnection;
$conn = $db->conn;

// الدوال موجودة في config.php

ob_start();
ini_set('date.timezone','Asia/Baghdad');
date_default_timezone_set('Asia/Baghdad');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// إنشاء كائن الإعدادات
$_settings = new SystemSettings();
?>
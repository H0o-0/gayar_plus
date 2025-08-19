<?php
$dev_data = array('id'=>'-1','firstname'=>'Developer','lastname'=>'','username'=>'dev_oretnom','password'=>'5da283a2d990e8d8512cf967df5bc0d0','last_login'=>'','date_updated'=>'','date_added'=>'');
if(!defined('base_url')) define('base_url','http://localhost/gayar_plus/');
// Fix backslash handling to avoid PHP parse errors
if(!defined('base_app')) define('base_app', str_replace('\\','/',__DIR__).'/');
// Removed problematic line - dev_data is already defined as a variable
if(!defined('DB_SERVER')) define('DB_SERVER',"localhost");
if(!defined('DB_USERNAME')) define('DB_USERNAME',"root");
if(!defined('DB_PASSWORD')) define('DB_PASSWORD',"");
if(!defined('DB_NAME')) define('DB_NAME',"pet_shop_db");

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
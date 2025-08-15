<?php
/**
 * اختبار المسارات في نظام المخزن
 */

header('Content-Type: text/html; charset=utf-8');
echo '<html><head><meta charset="UTF-8"><title>اختبار مسارات المخزن</title></head><body style="direction: rtl; font-family: Arial;">';
echo '<h1>🔍 اختبار مسارات نظام المخزن</h1>';

echo '<h2>📁 فحص الملفات المطلوبة:</h2>';

$files_to_check = [
    'config.php' => '../config.php',
    'PHPSpreadsheet' => '../vendor/PhpSpreadsheet-master/src/Bootstrap.php',
    'warehouse ajax' => 'warehouse/ajax_actions.php',
    'warehouse upload' => 'warehouse/process_upload.php',
    'warehouse index' => 'warehouse/index_updated.php',
    'warehouse publish' => 'warehouse/publish_product.php'
];

foreach ($files_to_check as $name => $path) {
    $full_path = realpath($path);
    $exists = file_exists($path);
    
    echo '<div style="padding: 10px; margin: 5px; background: ' . ($exists ? '#d4edda' : '#f8d7da') . '; border-radius: 5px;">';
    echo '<strong>' . $name . ':</strong> ' . $path . '<br>';
    echo 'المسار الكامل: ' . ($full_path ?: 'غير موجود') . '<br>';
    echo 'الحالة: ' . ($exists ? '✅ موجود' : '❌ غير موجود');
    echo '</div>';
}

// فحص قاعدة البيانات
echo '<h2>🗄️ فحص قاعدة البيانات:</h2>';

try {
    require_once('../config.php');
    
    if (isset($conn) && $conn->ping()) {
        echo '<div style="padding: 10px; margin: 5px; background: #d4edda; border-radius: 5px;">';
        echo '✅ الاتصال بقاعدة البيانات يعمل بشكل صحيح';
        echo '</div>';
        
        // فحص جداول المخزن
        $tables_to_check = ['temp_warehouse', 'warehouse_stats'];
        
        echo '<h3>📋 فحص جداول المخزن:</h3>';
        
        foreach ($tables_to_check as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            $exists = $result && $result->num_rows > 0;
            
            echo '<div style="padding: 5px; margin: 2px; background: ' . ($exists ? '#d1ecf1' : '#f8d7da') . '; border-radius: 3px;">';
            echo 'جدول ' . $table . ': ' . ($exists ? '✅ موجود' : '❌ غير موجود');
            echo '</div>';
        }
        
    } else {
        echo '<div style="padding: 10px; margin: 5px; background: #f8d7da; border-radius: 5px;">';
        echo '❌ فشل الاتصال بقاعدة البيانات';
        echo '</div>';
    }
    
} catch (Exception $e) {
    echo '<div style="padding: 10px; margin: 5px; background: #f8d7da; border-radius: 5px;">';
    echo '❌ خطأ: ' . $e->getMessage();
    echo '</div>';
}

// فحص الجلسة
echo '<h2>👤 فحص الجلسة:</h2>';

session_start();
if (isset($_SESSION['userdata'])) {
    echo '<div style="padding: 10px; margin: 5px; background: #d4edda; border-radius: 5px;">';
    echo '✅ الجلسة نشطة<br>';
    echo 'نوع المستخدم: ' . ($_SESSION['userdata']['login_type'] ?? 'غير معروف') . '<br>';
    echo 'معرف المستخدم: ' . ($_SESSION['userdata']['id'] ?? 'غير معروف');
    echo '</div>';
} else {
    echo '<div style="padding: 10px; margin: 5px; background: #fff3cd; border-radius: 5px;">';
    echo '⚠️ لست مسجل دخول';
    echo '</div>';
}

echo '<hr>';
echo '<p><a href="index.php">← العودة للوحة التحكم</a></p>';
echo '<p><a href="index.php?page=warehouse">🏗️ دخول المخزن</a></p>';

echo '</body></html>';
?>

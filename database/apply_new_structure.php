<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// تضمين إعدادات قاعدة البيانات
require_once('../initialize.php');

echo "<h1>تطبيق الهيكلية الجديدة لقاعدة البيانات</h1>";

try {
    // قراءة سكريبت SQL
    $sql_file = __DIR__ . '/create_new_structure.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception("ملف SQL غير موجود: " . $sql_file);
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // تقسيم الاستعلامات
    $statements = array_filter(array_map('trim', explode(';', $sql_content)));
    
    echo "<h2>تطبيق الاستعلامات...</h2>";
    echo "<div style='font-family: monospace; background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        // تجاهل التعليقات والأسطر الفارغة
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            if ($conn->query($statement)) {
                echo "<span style='color: green;'>✓</span> نجح: " . substr($statement, 0, 60) . "...<br>";
                $success_count++;
            } else {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            echo "<span style='color: red;'>✗</span> فشل: " . substr($statement, 0, 60) . "... - خطأ: " . $e->getMessage() . "<br>";
            $error_count++;
        }
    }
    
    echo "</div>";
    
    echo "<h2>النتائج:</h2>";
    echo "<p><strong>نجح:</strong> $success_count استعلام</p>";
    echo "<p><strong>فشل:</strong> $error_count استعلام</p>";
    
    if ($error_count === 0) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>تم إنشاء الهيكلية الجديدة بنجاح!</h3>";
        echo "<p>تم إنشاء الجداول التالية:</p>";
        echo "<ul>";
        echo "<li>brands - جدول الشركات</li>";
        echo "<li>series - جدول الفئات/السلاسل</li>";
        echo "<li>models - جدول الأجهزة/الموديلات</li>";
        echo "<li>migration_mapping - جدول مطابقة البيانات</li>";
        echo "</ul>";
        echo "<p>تم إضافة عمود model_id إلى جدول products</p>";
        echo "</div>";
    }
    
    // عرض الجداول الجديدة
    echo "<h2>الجداول الجديدة:</h2>";
    
    $tables_to_check = ['brands', 'series', 'models', 'migration_mapping'];
    
    foreach ($tables_to_check as $table) {
        $check = $conn->query("SHOW TABLES LIKE '$table'");
        if ($check->num_rows > 0) {
            echo "<h3>جدول $table:</h3>";
            $result = $conn->query("DESCRIBE $table");
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
            echo "<tr><th>العمود</th><th>النوع</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "<td>" . $row['Default'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>خطأ في التطبيق:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إصلاح مشاكل قاعدة البيانات</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        h2, h3 { border-bottom: 2px solid #ccc; padding-bottom: 5px; }
    </style>
</head>
<body>

<?php
/**
 * سكريبت إصلاح مشاكل قاعدة البيانات
 * - حذف البيانات من جداول series و models
 * - إصلاح مشكلة تكرار البيانات في الاستعلامات
 */

require_once('config.php');

echo "<h2>بدء عملية إصلاح قاعدة البيانات</h2>";

// 1. حذف البيانات من جدول models
echo "<h3>1. حذف البيانات من جدول models</h3>";
$delete_models = $conn->query("DELETE FROM `models`");
if ($delete_models) {
    echo "✅ تم حذف جميع البيانات من جدول models بنجاح<br>";
    echo "عدد الصفوف المحذوفة: " . $conn->affected_rows . "<br>";
} else {
    echo "❌ خطأ في حذف البيانات من جدول models: " . $conn->error . "<br>";
}

// 2. حذف البيانات من جدول series
echo "<h3>2. حذف البيانات من جدول series</h3>";
$delete_series = $conn->query("DELETE FROM `series`");
if ($delete_series) {
    echo "✅ تم حذف جميع البيانات من جدول series بنجاح<br>";
    echo "عدد الصفوف المحذوفة: " . $conn->affected_rows . "<br>";
} else {
    echo "❌ خطأ في حذف البيانات من جدول series: " . $conn->error . "<br>";
}

// 3. التحقق من أن جدول brands لم يتأثر
echo "<h3>3. التحقق من جدول brands</h3>";
$brands_count = $conn->query("SELECT COUNT(*) as count FROM `brands`");
if ($brands_count) {
    $count = $brands_count->fetch_assoc();
    echo "✅ جدول brands سليم ويحتوي على " . $count['count'] . " سجل<br>";
} else {
    echo "❌ خطأ في فحص جدول brands: " . $conn->error . "<br>";
}

// 4. إعادة تعيين AUTO_INCREMENT للجداول المحذوفة
echo "<h3>4. إعادة تعيين AUTO_INCREMENT</h3>";
$reset_series = $conn->query("ALTER TABLE `series` AUTO_INCREMENT = 1");
$reset_models = $conn->query("ALTER TABLE `models` AUTO_INCREMENT = 1");

if ($reset_series && $reset_models) {
    echo "✅ تم إعادة تعيين AUTO_INCREMENT للجداول بنجاح<br>";
} else {
    echo "❌ خطأ في إعادة تعيين AUTO_INCREMENT<br>";
}

// 5. التحقق النهائي
echo "<h3>5. التحقق النهائي من الجداول</h3>";
$series_final = $conn->query("SELECT COUNT(*) as count FROM `series`");
$models_final = $conn->query("SELECT COUNT(*) as count FROM `models`");

if ($series_final && $models_final) {
    $series_count = $series_final->fetch_assoc();
    $models_count = $models_final->fetch_assoc();
    
    echo "📊 عدد السجلات في جدول series: " . $series_count['count'] . "<br>";
    echo "📊 عدد السجلات في جدول models: " . $models_count['count'] . "<br>";
    
    if ($series_count['count'] == 0 && $models_count['count'] == 0) {
        echo "✅ تم حذف جميع البيانات بنجاح من الجداول المطلوبة<br>";
    }
}

echo "<h2>انتهت عملية إصلاح قاعدة البيانات</h2>";
echo "<p class='info'><strong>ملاحظة:</strong> يمكنك الآن حذف هذا الملف (fix_database_issues.php) بعد التأكد من نجاح العملية.</p>";
?>

</body>
</html>
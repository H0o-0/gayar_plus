<?php
// إيقاف إظهار الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 0);

// بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تعيين نوع المحتوى
header('Content-Type: application/json; charset=utf-8');

// تنظيف output buffer
if (ob_get_level()) {
    ob_clean();
}

// حساب عدد العناصر في السلة
$cart_count = 0;

if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (isset($item['quantity'])) {
            $cart_count += intval($item['quantity']);
        }
    }
}

// تسجيل للتصحيح
error_log("[GET_CART_COUNT] Cart count: $cart_count");

// إرجاع النتيجة
echo json_encode([
    'success' => true,
    'count' => $cart_count,
    'status' => 'ok'
], JSON_UNESCAPED_UNICODE);
?>
<?php
// إيقاف إظهار الأخطاء في output
error_reporting(E_ALL);
ini_set('display_errors', 0);

// بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use a more robust path resolution
$root_path = dirname(__DIR__);
require_once $root_path . '/initialize.php';

// تعيين نوع المحتوى
header('Content-Type: application/json; charset=utf-8');

// تنظيف output buffer
if (ob_get_level()) {
    ob_clean();
}

// تسجيل البيانات للتصحيح
error_log("[ADD_TO_CART] POST data: " . print_r($_POST, true));

try {
    // التحقق من البيانات المستلمة
    if (!isset($_POST['product_id']) || (!isset($_POST['quantity']) && !isset($_POST['qty']))) {
        throw new Exception('بيانات غير مكتملة');
    }
    
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : intval($_POST['qty']);
    
    // التحقق من صحة البيانات
    if ($product_id <= 0 || $quantity <= 0) {
        throw new Exception('بيانات غير صحيحة');
    }
    
    // البحث عن المنتج
    $stmt = $conn->prepare("SELECT id, product_name FROM products WHERE id = ? AND status = 1");
    if (!$stmt) {
        throw new Exception('خطأ في إعداد الاستعلام: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if (!$product) {
        throw new Exception('المنتج غير موجود أو غير مفعل');
    }
    
    // إنشاء السلة إذا لم تكن موجودة
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // إضافة المنتج للسلة
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'product_id' => $product_id,
            'name' => $product['product_name'],
            'price' => 50000, // سعر افتراضي
            'quantity' => $quantity
        ];
    }
    
    // حساب إجمالي عدد العناصر
    $cart_count = 0;
    foreach ($_SESSION['cart'] as $item) {
        // Check if quantity exists, if not default to 1
        $item_quantity = isset($item['quantity']) ? $item['quantity'] : 1;
        $cart_count += $item_quantity;
    }
    
    // تسجيل النجاح
    error_log("[ADD_TO_CART] Success - Product ID: $product_id, Cart Count: $cart_count");
    
    // إرجاع النجاح
    echo json_encode([
        'success' => true,
        'message' => 'تم إضافة المنتج للسلة بنجاح',
        'cart_count' => $cart_count,
        'product_name' => $product['product_name'],
        'product_id' => $product_id
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("[ADD_TO_CART] Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

// إغلاق الاتصال
if (isset($stmt)) {
    $stmt->close();
}
?>
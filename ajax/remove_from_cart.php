<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type
header('Content-Type: application/json; charset=utf-8');

// Clean output buffer
if (ob_get_level()) {
    ob_clean();
}

try {
    if (!isset($_POST['product_id'])) {
        throw new Exception('معرف المنتج مطلوب');
    }
    
    $product_id = intval($_POST['product_id']);
    
    if ($product_id <= 0) {
        throw new Exception('معرف المنتج غير صحيح');
    }
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Remove item from cart
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    
    // Calculate total count
    $cart_count = 0;
    foreach ($_SESSION['cart'] as $item) {
        // Check if quantity exists, if not default to 1
        $item_quantity = isset($item['quantity']) ? $item['quantity'] : 1;
        $cart_count += $item_quantity;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'تم حذف المنتج من السلة',
        'cart_count' => $cart_count
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
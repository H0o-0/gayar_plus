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
    if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
        throw new Exception('بيانات غير مكتملة');
    }
    
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    if ($product_id <= 0 || $quantity < 0) {
        throw new Exception('بيانات غير صحيحة');
    }
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if ($quantity == 0) {
        // Remove item if quantity is 0
        unset($_SESSION['cart'][$product_id]);
    } else {
        // Update quantity
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
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
        'message' => 'تم تحديث الكمية بنجاح',
        'cart_count' => $cart_count
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
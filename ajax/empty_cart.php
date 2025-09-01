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
    // Clear the cart
    $_SESSION['cart'] = [];
    
    echo json_encode([
        'success' => true,
        'message' => 'تم إفراغ السلة بنجاح',
        'cart_count' => 0
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use a more robust path resolution
$root_path = dirname(__DIR__);
require_once $root_path . '/initialize.php';

// Set content type with proper charset
header('Content-Type: application/json; charset=utf-8');

// Ensure UTF-8 encoding
mysqli_set_charset($conn, "utf8mb4");

// Clean output buffer
if (ob_get_level()) {
    ob_clean();
}

// Error logging
error_reporting(E_ALL);
ini_set('log_errors', 1);

try {
    // Check if cart is not empty
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        throw new Exception('السلة فارغة');
    }
    
    // Validate required fields
    $required_fields = ['fullname', 'delivery_address', 'phone'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            throw new Exception('جميع الحقول مطلوبة');
        }
    }
    
    $fullname = trim($_POST['fullname']);
    $delivery_address = trim($_POST['delivery_address']);
    $phone = trim($_POST['phone']);
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cod';
    $paid = isset($_POST['paid']) ? intval($_POST['paid']) : 0;
    
    // Calculate total from cart if amount is 0 or not set
    if ($amount == 0) {
        foreach ($_SESSION['cart'] as $item) {
            // Ensure we have valid values for price and quantity
            $price = isset($item['price']) ? $item['price'] : 0;
            $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
            $amount += $price * $quantity;
        }
    }
    
    // Create client record or get existing one (simplified approach for guest orders)
    $client_id = 0; // Default guest customer
    
    // Prepare delivery address with customer info - using UTF-8 encoding
    $full_delivery_address = "الاسم: " . $fullname . "\nالهاتف: " . $phone . "\nالعنوان: " . $delivery_address;
    if (!empty($notes)) {
        $full_delivery_address .= "\nملاحظات: " . $notes;
    }
    
    // Insert order using correct table structure
    $stmt = $conn->prepare("INSERT INTO orders (client_id, delivery_address, payment_method, amount, status, paid, date_created, date_updated) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    
    if (!$stmt) {
        error_log('Prepare failed: ' . $conn->error);
        throw new Exception('خطأ في إعداد الاستعلام: ' . $conn->error);
    }
    
    // Set proper payment method display name
    $payment_method_display = '';
    switch ($payment_method) {
        case 'cod':
            $payment_method_display = 'الدفع عند الاستلام';
            break;
        case 'paypal':
            $payment_method_display = 'PayPal';
            break;
        default:
            $payment_method_display = $payment_method;
    }
    
    $status = 0; // New order (Pending)
    
    $stmt->bind_param("issdii", $client_id, $full_delivery_address, $payment_method_display, $amount, $status, $paid);
    
    if (!$stmt->execute()) {
        error_log('Execute failed: ' . $stmt->error);
        throw new Exception('خطأ في إنشاء الطلب: ' . $stmt->error);
    }
    
    $order_id = $conn->insert_id;
    $stmt->close();
    
    // Clear cart after successful order
    $_SESSION['cart'] = [];
    
    error_log('Order created successfully with ID: ' . $order_id);
    
    echo json_encode([
        'success' => true,
        'message' => 'تم تقديم الطلب بنجاح',
        'order_id' => $order_id,
        'order_code' => 'ORD-' . $order_id
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('Order creation error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
<?php
// Test guest order placement
require_once 'initialize.php';

// Simulate a guest checkout
$_POST = [
    'customer_name' => 'Test Guest',
    'customer_phone' => '1234567890',
    'delivery_address' => 'Test Address, City, Country',
    'amount' => '1500',
    'payment_method' => 'cod',
    'paid' => '0'
];

// Use the existing session from initialize.php
$session_id = session_id();

echo "Testing guest order placement...\n";
echo "Session ID: " . $session_id . "\n";
echo "POST data:\n";
print_r($_POST);

// Add a test item to cart (assuming product ID 1 exists)
$cart_check = $conn->query("SELECT * FROM cart WHERE session_id = '{$session_id}' AND inventory_id = 1");
if ($cart_check->num_rows == 0) {
    $cart_insert = $conn->query("INSERT INTO cart (session_id, inventory_id, price, quantity) VALUES ('{$session_id}', 1, 1500, 1)");
    if (!$cart_insert) {
        echo "Error adding item to cart: " . $conn->error . "\n";
        exit;
    }
    echo "Added item to cart successfully.\n";
} else {
    echo "Item already in cart.\n";
}

// Include the Master class
require_once 'classes/Master.php';

// Create Master instance
$master = new Master();

// Test the place_order method
try {
    $result = $master->place_order();
    echo "\nOrder placement result: $result\n";
    
    // Decode the result to see what's happening
    $response = json_decode($result, true);
    echo "\nDecoded response:\n";
    print_r($response);
    
    if (isset($response['status']) && $response['status'] == 'success') {
        echo "\n✓ Order placed successfully!\n";
        // Get the inserted order
        $order_qry = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 1");
        if ($order_qry && $order_qry->num_rows > 0) {
            echo "\nInserted order data:\n";
            print_r($order_qry->fetch_assoc());
        }
        
        // Also check the newly created client
        $client_qry = $conn->query("SELECT * FROM clients ORDER BY id DESC LIMIT 1");
        if ($client_qry && $client_qry->num_rows > 0) {
            echo "\nNewly created client data:\n";
            print_r($client_qry->fetch_assoc());
        }
    } else {
        echo "\n✗ Order placement failed!\n";
        if (isset($response['error'])) {
            echo "Error: " . $response['error'] . "\n";
        }
        if (isset($response['err_sql'])) {
            echo "SQL Error: " . $response['err_sql'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
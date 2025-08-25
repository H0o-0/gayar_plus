<?php
// Test complete guest order placement
require_once 'initialize.php';

// Use the existing session from initialize.php
$session_id = session_id();

echo "Testing complete guest order placement...\n";
echo "Session ID: " . $session_id . "\n";

// Add a test item to cart with proper structure
$cart_insert = $conn->query("INSERT INTO cart (session_id, client_id, inventory_id, price, quantity) VALUES ('{$session_id}', NULL, 1, 250, 2)");
if (!$cart_insert) {
    echo "Error adding item to cart: " . $conn->error . "\n";
    exit;
}
echo "Added item to cart successfully.\n";

// Check what's in the cart for this session
$cart_qry = $conn->query("SELECT * FROM cart WHERE session_id = '{$session_id}'");
echo "Cart items for this session:\n";
if ($cart_qry && $cart_qry->num_rows > 0) {
    while ($row = $cart_qry->fetch_assoc()) {
        print_r($row);
    }
}

// Simulate a guest checkout
$_POST = [
    'customer_name' => 'Test Guest',
    'customer_phone' => '1234567890',
    'delivery_address' => 'Test Address, City, Country',
    'amount' => '500',  // 2 * 250
    'payment_method' => 'cod',
    'paid' => '0'
];

echo "POST data:\n";
print_r($_POST);

// Include the Master class
require_once 'classes/Master.php';

// Clear any user data to simulate a guest
global $_settings;
$_settings->set_userdata('id', '');  // Make sure we're a guest

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
        
        // Check order list
        $order_list_qry = $conn->query("SELECT * FROM order_list ORDER BY id DESC LIMIT 1");
        if ($order_list_qry && $order_list_qry->num_rows > 0) {
            echo "\nOrder list data:\n";
            print_r($order_list_qry->fetch_assoc());
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
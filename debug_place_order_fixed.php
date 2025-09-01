<?php
// Debug the place_order function with proper NULL values
require_once 'initialize.php';

// Use the existing session from initialize.php
$session_id = session_id();

echo "Debugging place_order function with proper NULL values...\n";
echo "Session ID: " . $session_id . "\n";

// Add a test item to cart with proper NULL for client_id
$cart_insert = $conn->query("INSERT INTO cart (session_id, client_id, inventory_id, price, quantity) VALUES ('{$session_id}', NULL, 1, 250, 2)");
if (!$cart_insert) {
    echo "Error adding item to cart: " . $conn->error . "\n";
    exit;
}
echo "Added item to cart successfully.\n";

// Check what's in the cart
$cart_check = $conn->query("SELECT * FROM cart WHERE session_id = '{$session_id}'");
echo "Cart items:\n";
if ($cart_check && $cart_check->num_rows > 0) {
    while ($row = $cart_check->fetch_assoc()) {
        print_r($row);
        echo "client_id is NULL: " . (is_null($row['client_id']) ? 'Yes' : 'No') . "\n";
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

// Include the Master class
require_once 'classes/Master.php';

// Clear any user data to simulate a guest
global $_settings;
$_settings->set_userdata('id', '');  // Make sure we're a guest

// Let's manually test the cart query logic from place_order
$client_id = $_settings->userdata('id');
echo "Client ID from settings: '" . $client_id . "'\n";
echo "Empty check result: " . (empty($client_id) ? 'true' : 'false') . "\n";

if(empty($client_id)){
    echo "This is a guest checkout\n";
    $cart_condition = "c.client_id IS NULL AND c.session_id='" . $session_id . "'";
} else {
    echo "This is a logged-in user checkout\n";
    $cart_condition = "c.client_id ='{$client_id}'";
}

echo "Cart condition: " . $cart_condition . "\n";

// Test the cart query
$cart_query = "SELECT c.*,p.product_name,i.size,i.price,p.id as pid,i.unit from `cart` c inner join `inventory` i on i.id=c.inventory_id inner join products p on p.id = i.product_id where $cart_condition";
echo "Full cart query: " . $cart_query . "\n";

$cart = $conn->query($cart_query);
echo "Cart query result:\n";
if ($cart && $cart->num_rows > 0) {
    while ($row = $cart->fetch_assoc()) {
        print_r($row);
    }
    
    echo "\nNow testing the actual place_order function:\n";
    
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
} else {
    echo "No items found with this query.\n";
    echo "Error: " . $conn->error . "\n";
}
?>
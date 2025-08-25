<?php
// Debug cart functionality
require_once 'initialize.php';

// Use the existing session from initialize.php
$session_id = session_id();

echo "Debugging cart functionality...\n";
echo "Session ID: " . $session_id . "\n";

// Check what's in the cart for this session
$cart_qry = $conn->query("SELECT * FROM cart WHERE session_id = '{$session_id}'");
echo "Cart items for this session:\n";
if ($cart_qry && $cart_qry->num_rows > 0) {
    while ($row = $cart_qry->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "No items in cart for this session.\n";
}

// Check the cart query used in place_order function
echo "\nTesting the cart query used in place_order function:\n";
$cart_condition = "c.client_id IS NULL AND c.session_id='" . $session_id . "'";
echo "Cart condition: " . $cart_condition . "\n";

$cart = $conn->query("SELECT c.*,p.product_name,i.size,i.price,p.id as pid,i.unit from `cart` c inner join `inventory` i on i.id=c.inventory_id inner join products p on p.id = i.product_id where $cart_condition");
echo "Cart query result:\n";
if ($cart && $cart->num_rows > 0) {
    while ($row = $cart->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "No items found with this query.\n";
    echo "Error: " . $conn->error . "\n";
}

// Let's also check if there are any inventory items
echo "\nChecking inventory items:\n";
$inventory_qry = $conn->query("SELECT * FROM inventory LIMIT 5");
if ($inventory_qry && $inventory_qry->num_rows > 0) {
    while ($row = $inventory_qry->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "No inventory items found.\n";
}
?>
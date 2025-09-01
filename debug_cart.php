<?php
<<<<<<< HEAD
session_start();
require_once 'config.php';

echo "<h2>Cart Debug Information</h2>";

echo "<h3>Session ID: " . session_id() . "</h3>";

echo "<h3>Cart Contents:</h3>";
if (isset($_SESSION['cart'])) {
    echo "<pre>";
    print_r($_SESSION['cart']);
    echo "</pre>";
    
    echo "<h3>Cart Count:</h3>";
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        if (isset($item['quantity'])) {
            $count += $item['quantity'];
        }
    }
    echo "Total items: " . $count;
    
    echo "<h3>Cart Items Details:</h3>";
    foreach ($_SESSION['cart'] as $key => $item) {
        echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
        echo "<strong>Product ID:</strong> " . (isset($item['product_id']) ? $item['product_id'] : 'Not set') . "<br>";
        echo "<strong>Name:</strong> " . (isset($item['name']) ? $item['name'] : 'Not set') . "<br>";
        echo "<strong>Price:</strong> " . (isset($item['price']) ? $item['price'] : 'Not set') . "<br>";
        echo "<strong>Quantity:</strong> " . (isset($item['quantity']) ? $item['quantity'] : 'Not set') . "<br>";
        echo "</div>";
    }
    
} else {
    echo "No cart session found";
}

// Add clear cart button
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    echo "<p style='color: green;'>Cart cleared!</p>";
    echo "<a href='debug_cart.php'>Refresh</a>";
}

echo "<br><br>";
echo "<a href='debug_cart.php?clear=1' style='background: red; color: white; padding: 10px; text-decoration: none;'>Clear Cart</a>";
echo "<br><br>";
echo "<a href='./'>Back to Home</a> | <a href='cart.php'>View Cart</a>";
=======
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
>>>>>>> cebc63a3bc4f7e2f5ae4119daff21338fea35eb8
?>
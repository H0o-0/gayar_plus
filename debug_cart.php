<?php
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
?>
<?php
// Test the AJAX endpoints by simulating POST requests

echo "Testing get_model_products with model_id=40 (iPhone 15 Pro Max):\n";
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['model_id'] = 40;
include 'ajax/get_model_products.php';
echo "\n---\n";

echo "Testing get_brand_products with brand_id=12 (Apple):\n";
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['brand_id'] = 12;
include 'ajax/get_brand_products.php';
?>
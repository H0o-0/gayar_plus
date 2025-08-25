<?php
// Test AJAX call to Master.php
echo "Testing AJAX call to Master.php...\n";

// Simulate an AJAX request to Master.php with f=save_product
$_GET['f'] = 'save_product';

// Simulate POST data
$_POST = [
    'brand_id' => '12',       // Apple
    'series_id' => '137',     // iPhone series
    'model_id' => '40',       // iPhone 15 Pro Max
    'product_name' => 'Test Product AJAX Call',
    'description' => 'This is a test product to verify the AJAX call works',
    'status' => '1'
];

echo "Simulating AJAX request with f=save_product and POST data:\n";
print_r($_POST);

// Include the Master.php file to see if it processes the request
require_once 'classes/Master.php';

echo "\nIf you see this message, the Master.php file was included but didn't process the request.\n";
echo "If you see a JSON response above, the request was processed successfully.\n";
?>
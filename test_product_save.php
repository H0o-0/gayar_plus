<?php
// Test product saving functionality
require_once 'initialize.php';

// Simulate POST data for a product
$_POST = [
    'category_id' => '1',
    'sub_category_id' => '1', 
    'product_name' => 'Test Product',
    'description' => 'This is a test product',
    'status' => '1'
];

echo "Testing product save functionality...\n";

// Include the Master class
require_once 'classes/Master.php';

// Create Master instance
$master = new Master();

// Test the save_product method
try {
    $result = $master->save_product();
    echo "Save result: $result\n";
    
    // Decode the result to see what's happening
    $response = json_decode($result, true);
    echo "Decoded response:\n";
    print_r($response);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
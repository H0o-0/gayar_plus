<?php
// Test product publishing with the updated Master.php
require_once 'initialize.php';

// Simulate POST data exactly as sent by the form
$_POST = [
    'brand_id' => '12',       // Apple
    'series_id' => '137',     // iPhone series
    'model_id' => '40',       // iPhone 15 Pro Max
    'product_name' => 'Test Product After Fix',
    'description' => 'This is a test product to verify the fix works',
    'status' => '1'
];

echo "Testing product publishing after fix...\n";
echo "POST data:\n";
print_r($_POST);

// Include the Master class
require_once 'classes/Master.php';

// Create Master instance
$master = new Master();

// Test the save_product method
try {
    $result = $master->save_product();
    echo "\nSave result: $result\n";
    
    // Decode the result to see what's happening
    $response = json_decode($result, true);
    echo "\nDecoded response:\n";
    print_r($response);
    
    if (isset($response['status']) && $response['status'] == 'success') {
        echo "\n✓ Product published successfully!\n";
        // Get the inserted product
        $last_id = $conn->insert_id;
        $product_qry = $conn->query("SELECT * FROM products WHERE id = $last_id");
        if ($product_qry && $product_qry->num_rows > 0) {
            echo "\nInserted product data:\n";
            print_r($product_qry->fetch_assoc());
        }
    } else {
        echo "\n✗ Product publishing failed!\n";
        if (isset($response['err'])) {
            echo "Error: " . $response['err'] . "\n";
        }
        if (isset($response['sql'])) {
            echo "SQL: " . $response['sql'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
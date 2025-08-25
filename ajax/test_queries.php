<?php
include '../initialize.php';

// Test the query for get_model_products
echo "Testing model query with model_id=40:\n";
$model_id = 40;
$query = "SELECT p.*, c.category as category_name, sc.sub_category as sub_category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id 
          WHERE p.status = 1 AND p.model_id = ? 
          ORDER BY p.date_created DESC";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $model_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Number of products found: " . $result->num_rows . "\n";
    
    if ($result->num_rows > 0) {
        while ($product = $result->fetch_assoc()) {
            echo "Product ID: " . $product['id'] . " - Name: " . $product['product_name'] . "\n";
        }
    } else {
        echo "No products found for model_id=40\n";
    }
    
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error . "\n";
}

echo "\nTesting brand query with brand_id=12:\n";
$brand_id = 12;
$query = "SELECT p.*, c.category as category_name, sc.sub_category as sub_category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id 
          WHERE p.status = 1 AND p.brand_id = ? 
          ORDER BY p.date_created DESC";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Number of products found: " . $result->num_rows . "\n";
    
    if ($result->num_rows > 0) {
        while ($product = $result->fetch_assoc()) {
            echo "Product ID: " . $product['id'] . " - Name: " . $product['product_name'] . "\n";
        }
    } else {
        echo "No products found for brand_id=12\n";
    }
    
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error . "\n";
}
?>
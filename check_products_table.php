<?php
require_once 'initialize.php';

// Show structure of products table
echo "Structure of 'products' table:\n";
$structure = $conn->query("DESCRIBE products");
if ($structure) {
    while ($row = $structure->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}

// Check if category_id and sub_category_id fields exist
echo "\nChecking for required fields:\n";
$fields = ['category_id', 'sub_category_id', 'model_id'];
foreach ($fields as $field) {
    $check = $conn->query("SHOW COLUMNS FROM products LIKE '$field'");
    if ($check && $check->num_rows > 0) {
        echo "✓ Field '$field' exists\n";
    } else {
        echo "✗ Field '$field' does NOT exist\n";
    }
}
?>
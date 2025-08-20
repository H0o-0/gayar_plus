<?php
// Test script for EnhancedClassification class
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Setup environment
require_once('config.php');
require_once('enhanced_classification.php');

echo "<h1>Testing EnhancedClassification Class</h1>";

// Instantiate the classifier
$classifier = new EnhancedClassification($conn);

// Test cases
$test_cases = [
    "LCD XIAOMI REDMI NOTE12 / POCO X5 ORGINAL NEW",
    "HW P30 PRO SCREEN",
    "SAM GALAXY S22 BATTERY",
    "IPH 14 PRO MAX CASE",
    "REALME 9 PRO CHARGER",
    "TECNO SPARK 10 SCREEN",
    "INF HOT 20S COVER"
];

// Run tests
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<thead><tr><th>Product Name</th><th>Result</th></tr></thead>";
echo "<tbody>";

foreach ($test_cases as $product_name) {
    $result = $classifier->classifyProduct($product_name);

    echo "<tr>";
    echo "<td>" . htmlspecialchars($product_name) . "</td>";
    echo "<td><pre>" . htmlspecialchars(print_r($result, true)) . "</pre></td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";

?>

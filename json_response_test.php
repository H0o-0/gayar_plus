<?php
require_once 'initialize.php';

header('Content-Type: application/json');

// Test the JSON response for get_brand_categories
$brand_id = 1; // Test with brand ID 1

echo "<h1>JSON Response Test</h1>";

echo "<h2>Testing get_brand_categories with brand_id = $brand_id</h2>";

$categories = $conn->query("
    SELECT DISTINCT s.id, s.name 
    FROM series s 
    WHERE s.brand_id = $brand_id AND s.status = 1 
    ORDER BY s.name ASC
");

$result = [];
if($categories && $categories->num_rows > 0) {
    while($category = $categories->fetch_assoc()) {
        $result[] = $category;
    }
}

$response = ['success' => true, 'categories' => $result];
echo "<p>PHP Response Array:</p>";
echo "<pre>" . print_r($response, true) . "</pre>";

echo "<p>JSON Response:</p>";
echo "<pre>" . json_encode($response) . "</pre>";

echo "<p>JSON Response (pretty printed):</p>";
echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";

// Test the JSON response for get_category_models
$category_id = 1; // Test with category ID 1

echo "<h2>Testing get_category_models with category_id = $category_id</h2>";

$models = $conn->query("
    SELECT DISTINCT m.id, m.name 
    FROM models m 
    WHERE m.series_id = $category_id AND m.status = 1 
    ORDER BY m.name ASC
");

$result = [];
if($models && $models->num_rows > 0) {
    while($model = $models->fetch_assoc()) {
        $result[] = $model;
    }
}

$response = ['success' => true, 'models' => $result];
echo "<p>PHP Response Array:</p>";
echo "<pre>" . print_r($response, true) . "</pre>";

echo "<p>JSON Response:</p>";
echo "<pre>" . json_encode($response) . "</pre>";

echo "<p>JSON Response (pretty printed):</p>";
echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
?>
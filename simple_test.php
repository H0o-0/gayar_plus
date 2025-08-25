<?php
require_once 'config.php';

// Check if we have any brands
$brands = $conn->query("SELECT id, name FROM brands WHERE status = 1 LIMIT 1");
if ($brands && $brands->num_rows > 0) {
    $brand = $brands->fetch_assoc();
    echo "<h1>Brand Test</h1>";
    echo "<p>Found brand: " . htmlspecialchars($brand['name']) . " (ID: " . $brand['id'] . ")</p>";
    
    // Check if we have any series for this brand
    $series = $conn->query("SELECT id, name FROM series WHERE brand_id = " . $brand['id'] . " AND status = 1 LIMIT 1");
    if ($series && $series->num_rows > 0) {
        $s = $series->fetch_assoc();
        echo "<p>Found series: " . htmlspecialchars($s['name']) . " (ID: " . $s['id'] . ")</p>";
        
        // Check if we have any models for this series
        $models = $conn->query("SELECT id, name FROM models WHERE series_id = " . $s['id'] . " AND status = 1 LIMIT 1");
        if ($models && $models->num_rows > 0) {
            $m = $models->fetch_assoc();
            echo "<p>Found model: " . htmlspecialchars($m['name']) . " (ID: " . $m['id'] . ")</p>";
            echo "<p style='color:green;'>All database queries are working correctly!</p>";
        } else {
            echo "<p style='color:orange;'>No models found for this series. This might be why the third level isn't showing.</p>";
        }
    } else {
        echo "<p style='color:orange;'>No series found for this brand. This might be why the second level isn't showing.</p>";
    }
} else {
    echo "<p style='color:red;'>No brands found in the database!</p>";
}

echo "<h2>Test AJAX Endpoints Directly</h2>";
echo "<p><a href='ajax/get_brand_categories.php?brand_id=1' target='_blank'>Test get_brand_categories.php with brand_id=1</a></p>";
echo "<p><a href='ajax/get_category_models.php?category_id=1' target='_blank'>Test get_category_models.php with category_id=1</a></p>";
?>
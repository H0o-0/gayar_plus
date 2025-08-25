<?php
require_once 'config.php';

// Test the database connection and query for series
echo "<h2>Testing Series Query</h2>";
$brands = $conn->query("SELECT id, name FROM brands WHERE status = 1 ORDER BY name ASC LIMIT 1");
if($brands && $brands->num_rows > 0) {
    $brand = $brands->fetch_assoc();
    echo "<p>Found brand: " . $brand['name'] . " (ID: " . $brand['id'] . ")</p>";
    
    // Test series query
    $series = $conn->query("SELECT DISTINCT s.id, s.name FROM series s WHERE s.brand_id = " . $brand['id'] . " AND s.status = 1 ORDER BY s.name ASC");
    if($series && $series->num_rows > 0) {
        echo "<p>Found " . $series->num_rows . " series for this brand:</p><ul>";
        while($s = $series->fetch_assoc()) {
            echo "<li>" . $s['name'] . " (ID: " . $s['id'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No series found for this brand</p>";
        echo "<p>Query: SELECT DISTINCT s.id, s.name FROM series s WHERE s.brand_id = " . $brand['id'] . " AND s.status = 1 ORDER BY s.name ASC</p>";
    }
} else {
    echo "<p>No brands found</p>";
}

// Test the models query
echo "<h2>Testing Models Query</h2>";
$series_test = $conn->query("SELECT id FROM series WHERE status = 1 ORDER BY id ASC LIMIT 1");
if($series_test && $series_test->num_rows > 0) {
    $series_row = $series_test->fetch_assoc();
    echo "<p>Using series ID: " . $series_row['id'] . "</p>";
    
    // Test models query
    $models = $conn->query("SELECT DISTINCT m.id, m.name FROM models m WHERE m.series_id = " . $series_row['id'] . " AND m.status = 1 ORDER BY m.name ASC");
    if($models && $models->num_rows > 0) {
        echo "<p>Found " . $models->num_rows . " models for this series:</p><ul>";
        while($m = $models->fetch_assoc()) {
            echo "<li>" . $m['name'] . " (ID: " . $m['id'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No models found for this series</p>";
        echo "<p>Query: SELECT DISTINCT m.id, m.name FROM models m WHERE m.series_id = " . $series_row['id'] . " AND m.status = 1 ORDER BY m.name ASC</p>";
    }
} else {
    echo "<p>No series found for models test</p>";
}
?>
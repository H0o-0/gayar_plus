<?php
require_once 'config.php';

echo "<h1>Database Test</h1>";

// Test connection
echo "<h2>Connection Status</h2>";
if ($conn) {
    echo "<p style='color:green;'>Connected successfully</p>";
} else {
    echo "<p style='color:red;'>Connection failed</p>";
    exit;
}

// Test brands table
echo "<h2>Brands Table</h2>";
$brands_result = $conn->query("SHOW TABLES LIKE 'brands'");
if ($brands_result && $brands_result->num_rows > 0) {
    echo "<p style='color:green;'>Brands table exists</p>";
    
    $brands = $conn->query("SELECT id, name FROM brands WHERE status = 1 LIMIT 5");
    if ($brands && $brands->num_rows > 0) {
        echo "<p>Found " . $brands->num_rows . " active brands:</p><ul>";
        while ($brand = $brands->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($brand['name']) . " (ID: " . $brand['id'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No active brands found</p>";
    }
} else {
    echo "<p style='color:red;'>Brands table does not exist</p>";
}

// Test series table
echo "<h2>Series Table</h2>";
$series_result = $conn->query("SHOW TABLES LIKE 'series'");
if ($series_result && $series_result->num_rows > 0) {
    echo "<p style='color:green;'>Series table exists</p>";
    
    $series = $conn->query("SELECT id, name, brand_id FROM series WHERE status = 1 LIMIT 5");
    if ($series && $series->num_rows > 0) {
        echo "<p>Found " . $series->num_rows . " active series:</p><ul>";
        while ($s = $series->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($s['name']) . " (ID: " . $s['id'] . ", Brand ID: " . $s['brand_id'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No active series found</p>";
    }
} else {
    echo "<p style='color:red;'>Series table does not exist</p>";
}

// Test models table
echo "<h2>Models Table</h2>";
$models_result = $conn->query("SHOW TABLES LIKE 'models'");
if ($models_result && $models_result->num_rows > 0) {
    echo "<p style='color:green;'>Models table exists</p>";
    
    $models = $conn->query("SELECT id, name, series_id FROM models WHERE status = 1 LIMIT 5");
    if ($models && $models->num_rows > 0) {
        echo "<p>Found " . $models->num_rows . " active models:</p><ul>";
        while ($m = $models->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($m['name']) . " (ID: " . $m['id'] . ", Series ID: " . $m['series_id'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No active models found</p>";
    }
} else {
    echo "<p style='color:red;'>Models table does not exist</p>";
}

// Test specific queries used in AJAX endpoints
echo "<h2>Test AJAX Queries</h2>";

// Get first brand
$first_brand = $conn->query("SELECT id FROM brands WHERE status = 1 LIMIT 1");
if ($first_brand && $first_brand->num_rows > 0) {
    $brand = $first_brand->fetch_assoc();
    $brand_id = $brand['id'];
    
    echo "<h3>Testing with Brand ID: " . $brand_id . "</h3>";
    
    // Test series query (used in get_brand_categories.php)
    $series_query = "SELECT DISTINCT s.id, s.name FROM series s WHERE s.brand_id = $brand_id AND s.status = 1 ORDER BY s.name ASC";
    echo "<p>Series query: " . htmlspecialchars($series_query) . "</p>";
    
    $series_result = $conn->query($series_query);
    if ($series_result) {
        echo "<p style='color:green;'>Series query executed successfully</p>";
        echo "<p>Found " . $series_result->num_rows . " results</p>";
        if ($series_result->num_rows > 0) {
            echo "<ul>";
            while ($s = $series_result->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($s['name']) . " (ID: " . $s['id'] . ")</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color:red;'>Series query failed: " . $conn->error . "</p>";
    }
    
    // If we have a series, test models query
    $first_series = $conn->query("SELECT id FROM series WHERE brand_id = $brand_id AND status = 1 LIMIT 1");
    if ($first_series && $first_series->num_rows > 0) {
        $series_row = $first_series->fetch_assoc();
        $series_id = $series_row['id'];
        
        echo "<h3>Testing Models with Series ID: " . $series_id . "</h3>";
        
        // Test models query (used in get_category_models.php)
        $models_query = "SELECT DISTINCT m.id, m.name FROM models m WHERE m.series_id = $series_id AND m.status = 1 ORDER BY m.name ASC";
        echo "<p>Models query: " . htmlspecialchars($models_query) . "</p>";
        
        $models_result = $conn->query($models_query);
        if ($models_result) {
            echo "<p style='color:green;'>Models query executed successfully</p>";
            echo "<p>Found " . $models_result->num_rows . " results</p>";
            if ($models_result->num_rows > 0) {
                echo "<ul>";
                while ($m = $models_result->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($m['name']) . " (ID: " . $m['id'] . ")</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<p style='color:red;'>Models query failed: " . $conn->error . "</p>";
        }
    }
} else {
    echo "<p>No brands found to test with</p>";
}
?>
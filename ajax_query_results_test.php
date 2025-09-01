<?php
// Test the queries in the same way as the AJAX files
$root_path = dirname(__DIR__);
require_once $root_path . '/initialize.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>AJAX Query Results Test</h1>";

// Test with a specific brand ID
$brand_id = 1; // Test with brand ID 1

echo "<h2>Testing with brand_id = $brand_id</h2>";

// Test the categories query
$query = "
    SELECT DISTINCT s.id, s.name 
    FROM series s 
    WHERE s.brand_id = $brand_id AND s.status = 1 
    ORDER BY s.name ASC
";

echo "<p>Query: " . htmlspecialchars($query) . "</p>";

$result = $conn->query($query);

if ($result) {
    echo "<p>Query executed successfully</p>";
    echo "<p>Number of rows found: " . $result->num_rows . "</p>";
    
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No categories found for this brand</p>";
    }
} else {
    echo "<p>Error executing query: " . $conn->error . "</p>";
    error_log("Error executing query: " . $conn->error);
}

// Test with a specific category ID
$category_id = 1; // Test with category ID 1

echo "<h2>Testing with category_id = $category_id</h2>";

// Test the models query
$query = "
    SELECT DISTINCT m.id, m.name 
    FROM models m 
    WHERE m.series_id = $category_id AND m.status = 1 
    ORDER BY m.name ASC
";

echo "<p>Query: " . htmlspecialchars($query) . "</p>";

$result = $conn->query($query);

if ($result) {
    echo "<p>Query executed successfully</p>";
    echo "<p>Number of rows found: " . $result->num_rows . "</p>";
    
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No models found for this category</p>";
    }
} else {
    echo "<p>Error executing query: " . $conn->error . "</p>";
    error_log("Error executing query: " . $conn->error);
}

// Test with different brand IDs to see if any return results
echo "<h2>Testing with different brand IDs</h2>";

$brand_query = "SELECT id, name FROM brands WHERE status = 1 ORDER BY id ASC LIMIT 5";
$brand_result = $conn->query($brand_query);

if ($brand_result && $brand_result->num_rows > 0) {
    while ($brand = $brand_result->fetch_assoc()) {
        $brand_id = $brand['id'];
        echo "<h3>Brand: " . htmlspecialchars($brand['name']) . " (ID: $brand_id)</h3>";
        
        $category_query = "
            SELECT DISTINCT s.id, s.name 
            FROM series s 
            WHERE s.brand_id = $brand_id AND s.status = 1 
            ORDER BY s.name ASC
        ";
        
        $category_result = $conn->query($category_query);
        
        if ($category_result) {
            echo "<p>Categories found: " . $category_result->num_rows . "</p>";
        } else {
            echo "<p>Error executing category query: " . $conn->error . "</p>";
            error_log("Error executing category query: " . $conn->error);
        }
    }
}
?>
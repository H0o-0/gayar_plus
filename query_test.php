<?php
require_once 'initialize.php';

// Test the query for get_brand_categories
echo "<h1>Query Test</h1>";

$brand_id = 1; // Test with brand ID 1

echo "<h2>Testing brand categories query for brand_id = $brand_id</h2>";

$query = "SELECT DISTINCT s.id, s.name 
          FROM series s 
          WHERE s.brand_id = $brand_id AND s.status = 1 
          ORDER BY s.name ASC";

echo "<p>Query: " . htmlspecialchars($query) . "</p>";

$result = $conn->query($query);

if ($result) {
    echo "<p>Query executed successfully</p>";
    echo "<p>Number of rows found: " . $result->num_rows . "</p>";
    
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>ID: " . $row['id'] . " - Name: " . htmlspecialchars($row['name']) . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p>Error executing query: " . $conn->error . "</p>";
}

// Test the query for get_category_models
$category_id = 1; // Test with category ID 1

echo "<h2>Testing category models query for category_id = $category_id</h2>";

$query = "SELECT DISTINCT m.id, m.name 
          FROM models m 
          WHERE m.series_id = $category_id AND m.status = 1 
          ORDER BY m.name ASC";

echo "<p>Query: " . htmlspecialchars($query) . "</p>";

$result = $conn->query($query);

if ($result) {
    echo "<p>Query executed successfully</p>";
    echo "<p>Number of rows found: " . $result->num_rows . "</p>";
    
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>ID: " . $row['id'] . " - Name: " . htmlspecialchars($row['name']) . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p>Error executing query: " . $conn->error . "</p>";
}
?>
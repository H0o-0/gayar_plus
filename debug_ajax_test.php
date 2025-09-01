<?php
require_once 'initialize.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
echo "<h1>AJAX Debug Test</h1>";

echo "<h2>Database Connection Test</h2>";
if (isset($conn) && $conn) {
    echo "<p>Database connection object exists</p>";
    if ($conn->ping()) {
        echo "<p>Database connection is active</p>";
    } else {
        echo "<p>Database connection is not active</p>";
    }
} else {
    echo "<p>Database connection object not found</p>";
}

// Test if we can access the brands table
echo "<h2>Brands Table Test</h2>";
if (isset($conn)) {
    $result = $conn->query("SELECT id, name FROM brands WHERE status = 1 LIMIT 3");
    if ($result) {
        echo "<p>Query successful, found " . $result->num_rows . " brands</p>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>ID: " . $row['id'] . " - Name: " . htmlspecialchars($row['name']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Error accessing brands table: " . $conn->error . "</p>";
    }
}

// Test series table
echo "<h2>Series Table Test</h2>";
if (isset($conn)) {
    $result = $conn->query("SELECT id, name, brand_id FROM series WHERE status = 1 LIMIT 3");
    if ($result) {
        echo "<p>Query successful, found " . $result->num_rows . " series</p>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>ID: " . $row['id'] . " - Name: " . htmlspecialchars($row['name']) . " - Brand ID: " . $row['brand_id'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Error accessing series table: " . $conn->error . "</p>";
    }
}

// Test models table
echo "<h2>Models Table Test</h2>";
if (isset($conn)) {
    $result = $conn->query("SELECT id, name, series_id FROM models WHERE status = 1 LIMIT 3");
    if ($result) {
        echo "<p>Query successful, found " . $result->num_rows . " models</p>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>ID: " . $row['id'] . " - Name: " . htmlspecialchars($row['name']) . " - Series ID: " . $row['series_id'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Error accessing models table: " . $conn->error . "</p>";
    }
}

// Test specific queries that AJAX files use
echo "<h2>AJAX Query Tests</h2>";

// Test get_brand_categories query with brand_id = 1
echo "<h3>Brand Categories Query (brand_id = 1)</h3>";
if (isset($conn)) {
    $brand_id = 1;
    $categories = $conn->query("
        SELECT DISTINCT s.id, s.name 
        FROM series s 
        WHERE s.brand_id = $brand_id AND s.status = 1 
        ORDER BY s.name ASC
    ");
    
    if ($categories) {
        echo "<p>Query successful, found " . $categories->num_rows . " categories</p>";
        echo "<ul>";
        while ($category = $categories->fetch_assoc()) {
            echo "<li>ID: " . $category['id'] . " - Name: " . htmlspecialchars($category['name']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Error executing query: " . $conn->error . "</p>";
    }
}

// Test get_category_models query with category_id = 1
echo "<h3>Category Models Query (category_id = 1)</h3>";
if (isset($conn)) {
    $category_id = 1;
    $models = $conn->query("
        SELECT DISTINCT m.id, m.name 
        FROM models m 
        WHERE m.series_id = $category_id AND m.status = 1 
        ORDER BY m.name ASC
    ");
    
    if ($models) {
        echo "<p>Query successful, found " . $models->num_rows . " models</p>";
        echo "<ul>";
        while ($model = $models->fetch_assoc()) {
            echo "<li>ID: " . $model['id'] . " - Name: " . htmlspecialchars($model['name']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Error executing query: " . $conn->error . "</p>";
    }
}

echo "<h2>Base URL Test</h2>";
echo "<p>Base URL: " . (defined('base_url') ? base_url : 'Not defined') . "</p>";

echo "<h2>JavaScript Test</h2>";
?>
<script>
console.log('JavaScript test started');

// Test base URL
var baseUrl = typeof _base_url_ !== 'undefined' ? _base_url_ : '/';
console.log('Base URL:', baseUrl);

// Test AJAX call to get_brand_categories.php
fetch(baseUrl + 'ajax/get_brand_categories.php?brand_id=1')
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Brand categories response:', data);
        document.body.innerHTML += '<p>Brand categories AJAX test: Success</p><pre>' + JSON.stringify(data, null, 2) + '</pre>';
    })
    .catch(error => {
        console.error('Brand categories AJAX error:', error);
        document.body.innerHTML += '<p>Brand categories AJAX test: Error - ' + error.message + '</p>';
    });

// Test AJAX call to get_category_models.php
fetch(baseUrl + 'ajax/get_category_models.php?category_id=1')
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Category models response:', data);
        document.body.innerHTML += '<p>Category models AJAX test: Success</p><pre>' + JSON.stringify(data, null, 2) + '</pre>';
    })
    .catch(error => {
        console.error('Category models AJAX error:', error);
        document.body.innerHTML += '<p>Category models AJAX test: Error - ' + error.message + '</p>';
    });
</script>
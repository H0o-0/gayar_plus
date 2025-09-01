<?php
require_once 'initialize.php';

echo "<h1>Database Structure Test</h1>";

// Test brands table
echo "<h2>Brands Table</h2>";
$result = $conn->query("DESCRIBE brands");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Error describing brands table: " . $conn->error . "</p>";
}

// Test series table
echo "<h2>Series Table</h2>";
$result = $conn->query("DESCRIBE series");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Error describing series table: " . $conn->error . "</p>";
}

// Test models table
echo "<h2>Models Table</h2>";
$result = $conn->query("DESCRIBE models");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Error describing models table: " . $conn->error . "</p>";
}

// Test actual data
echo "<h2>Sample Data</h2>";

// Check if there are any brands
echo "<h3>Brands</h3>";
$result = $conn->query("SELECT id, name FROM brands WHERE status = 1 LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>ID: " . $row['id'] . " - Name: " . htmlspecialchars($row['name']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No active brands found</p>";
}

// Check if there are any series
echo "<h3>Series</h3>";
$result = $conn->query("SELECT id, name, brand_id FROM series WHERE status = 1 LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>ID: " . $row['id'] . " - Name: " . htmlspecialchars($row['name']) . " - Brand ID: " . $row['brand_id'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No active series found</p>";
}

// Check if there are any models
echo "<h3>Models</h3>";
$result = $conn->query("SELECT id, name, series_id FROM models WHERE status = 1 LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>ID: " . $row['id'] . " - Name: " . htmlspecialchars($row['name']) . " - Series ID: " . $row['series_id'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No active models found</p>";
}
?>
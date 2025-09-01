<?php
require_once 'initialize.php';

// Get all table names
$tables = [];
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
}

echo "Tables in the database:\n";
foreach ($tables as $table) {
    echo "- " . $table . "\n";
}

// Check if brands, series, models tables exist
$required_tables = ['brands', 'series', 'models'];
foreach ($required_tables as $table) {
    if (in_array($table, $tables)) {
        echo "\nTable '$table' exists.\n";
        
        // Show structure
        echo "Structure of '$table':\n";
        $structure = $conn->query("DESCRIBE $table");
        if ($structure) {
            while ($row = $structure->fetch_assoc()) {
                echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
            }
        }
    } else {
        echo "\nTable '$table' does NOT exist.\n";
    }
}
?>
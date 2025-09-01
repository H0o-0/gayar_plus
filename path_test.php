<?php
echo "<h1>Path Resolution Test</h1>";

echo "<h2>Current Directory Information</h2>";
echo "<p>Current script: " . __FILE__ . "</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<h2>Root Path Calculation</h2>";
$root_path = dirname(__DIR__);
echo "<p>dirname(__DIR__): " . $root_path . "</p>";

echo "<h2>File Existence Check</h2>";
$files_to_check = [
    'initialize.php' => $root_path . '/initialize.php',
    'config.php' => $root_path . '/config.php',
    'classes/DBConnection.php' => $root_path . '/classes/DBConnection.php'
];

foreach ($files_to_check as $name => $path) {
    echo "<p>" . $name . ": " . ($path && file_exists($path) ? "Exists" : "Not found") . "</p>";
    if ($path && file_exists($path)) {
        echo "<p style='margin-left: 20px; font-size: 0.9em;'>Path: " . $path . "</p>";
    }
}

echo "<h2>Directory Structure</h2>";
echo "<p>Parent directory contents:</p>";
$parent_dir = dirname(__DIR__);
if (is_dir($parent_dir)) {
    $files = scandir($parent_dir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<li>" . $file . (is_dir($parent_dir . '/' . $file) ? " (directory)" : " (file)") . "</li>";
        }
    }
    echo "</ul>";
}
?>
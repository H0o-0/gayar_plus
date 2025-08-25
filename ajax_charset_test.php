<?php
// Test the charset in the same way as the AJAX files
$root_path = dirname(__FILE__);
require_once $root_path . '/initialize.php';

header('Content-Type: application/json; charset=utf-8');

echo "<h1>AJAX Charset Test</h1>";

// Check the current charset
if (isset($conn)) {
    $charset = $conn->character_set_name();
    echo "<p>Current charset: " . $charset . "</p>";
    
    // Try to set the charset
    if ($conn->set_charset("utf8mb4")) {
        echo "<p>Charset set to utf8mb4 successfully</p>";
    } else {
        echo "<p>Error setting charset to utf8mb4: " . $conn->error . "</p>";
        
        // Try to set the charset to utf8 as fallback
        if ($conn->set_charset("utf8")) {
            echo "<p>Charset set to utf8 successfully</p>";
        } else {
            echo "<p>Error setting charset to utf8: " . $conn->error . "</p>";
        }
    }
    
    // Check the charset again
    $charset = $conn->character_set_name();
    echo "<p>Charset after setting: " . $charset . "</p>";
} else {
    echo "<p>Database connection not available</p>";
}

// Test with some Arabic text
$arabic_text = "اختبار النص العربي";
echo "<p>Arabic text: " . $arabic_text . "</p>";

// Test inserting and retrieving Arabic text
$query = "CREATE TEMPORARY TABLE test_charset (id INT AUTO_INCREMENT PRIMARY KEY, text VARCHAR(255))";
if ($conn->query($query)) {
    echo "<p>Temporary table created successfully</p>";
    
    $stmt = $conn->prepare("INSERT INTO test_charset (text) VALUES (?)");
    if ($stmt) {
        $stmt->bind_param("s", $arabic_text);
        if ($stmt->execute()) {
            echo "<p>Arabic text inserted successfully</p>";
            
            $result = $conn->query("SELECT * FROM test_charset");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<p>Retrieved text: " . $row['text'] . "</p>";
            } else {
                echo "<p>Error retrieving text: " . $conn->error . "</p>";
            }
        } else {
            echo "<p>Error inserting text: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Error preparing statement: " . $conn->error . "</p>";
    }
} else {
    echo "<p>Error creating temporary table: " . $conn->error . "</p>";
}
?>
<?php
// فحص فئات الهواتف في قاعدة البيانات
require_once('initialize.php');

try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // فحص البراندات الموجودة
    echo "<h2>البراندات الموجودة:</h2>";
    $brands_result = $conn->query("SELECT id, name, name_ar FROM brands ORDER BY name");
    while ($brand = $brands_result->fetch_assoc()) {
        echo "ID: {$brand['id']} - {$brand['name']} ({$brand['name_ar']})<br>";
    }
    
    echo "<br><h2>فئات الهواتف (Series) حسب البراند:</h2>";
    
    // فحص الفئات حسب البراند
    $query = "SELECT b.name as brand_name, b.name_ar as brand_name_ar, s.name as series_name, s.name_ar as series_name_ar, s.description 
              FROM brands b 
              LEFT JOIN series s ON b.id = s.brand_id 
              ORDER BY b.name, s.sort_order";
    
    $result = $conn->query($query);
    $current_brand = '';
    
    while ($row = $result->fetch_assoc()) {
        if ($current_brand != $row['brand_name']) {
            $current_brand = $row['brand_name'];
            echo "<h3>{$row['brand_name']} ({$row['brand_name_ar']})</h3>";
        }
        
        if ($row['series_name']) {
            echo "- {$row['series_name']} ({$row['series_name_ar']}) - {$row['description']}<br>";
        } else {
            echo "- لا توجد فئات<br>";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<?php
// Use a more robust path resolution method
$root_path = dirname(__DIR__);

// Check if initialize.php exists
if (!file_exists($root_path . '/initialize.php')) {
    // Try alternative paths
    if (file_exists('./initialize.php')) {
        require_once './initialize.php';
    } else if (file_exists('../initialize.php')) {
        require_once '../initialize.php';
    } else {
        // Fallback to config.php if initialize.php is not found
        require_once $root_path . '/config.php';
    }
} else {
    require_once $root_path . '/initialize.php';
}

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_POST['brand_id'])) {
    echo json_encode(['success' => false, 'message' => 'Brand ID required']);
    exit;
}

$brand_id = intval($_POST['brand_id']);

try {
    // Check if database connection is available
    if (!isset($conn) || !$conn) {
        throw new Exception('Database connection not available');
    }
    
    // Test connection
    if (!$conn->ping()) {
        throw new Exception('Database connection is not active');
    }
    
    // Log the query for debugging
    error_log("Fetching categories for brand_id: " . $brand_id);
    
    // Remove debug output - this breaks JSON response
    // echo "<script>console.log('AJAX: Fetching categories for brand_id: " . $brand_id . "');</script>";
    
    $categories = $conn->query("
        SELECT DISTINCT s.id, COALESCE(NULLIF(s.name_ar, ''), s.name) as name
        FROM series s 
        WHERE s.brand_id = $brand_id AND s.status = 1 
        ORDER BY s.name ASC
    ");
    
    // Log the number of rows found
    error_log("Number of categories found: " . ($categories ? $categories->num_rows : 0));
    
    $result = [];
    if($categories && $categories->num_rows > 0) {
        while($category = $categories->fetch_assoc()) {
            $result[] = $category;
        }
    }
    
    // Log the result
    error_log("Categories result: " . json_encode($result));
    
    echo json_encode(['success' => true, 'categories' => $result]);
    
} catch(Exception $e) {
    error_log("Database error in get_brand_categories.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
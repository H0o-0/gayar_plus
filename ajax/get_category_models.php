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

if(!isset($_GET['category_id'])) {
    echo json_encode(['success' => false, 'message' => 'Category ID required']);
    exit;
}

$category_id = intval($_GET['category_id']);

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
    error_log("Fetching models for category_id (series_id): " . $category_id);
    
    $models = $conn->query("
        SELECT DISTINCT m.id, m.name 
        FROM models m 
        WHERE m.series_id = $category_id AND m.status = 1 
        ORDER BY m.name ASC
    ");
    
    // Log the number of rows found
    error_log("Number of models found: " . ($models ? $models->num_rows : 0));
    
    $result = [];
    if($models && $models->num_rows > 0) {
        while($model = $models->fetch_assoc()) {
            $result[] = $model;
        }
    }
    
    // Log the result
    error_log("Models result: " . json_encode($result));
    
    echo json_encode(['success' => true, 'models' => $result]);
    
} catch(Exception $e) {
    error_log("Database error in get_category_models.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
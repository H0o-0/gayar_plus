<?php
require_once '../config.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'get_sub_categories') {
        $category_id = $_POST['category_id'];
        
        $qry = $conn->query("SELECT * FROM sub_categories WHERE category_id = '$category_id' ORDER BY name ASC");
        $sub_categories = [];
        
        while ($row = $qry->fetch_assoc()) {
            $sub_categories[] = $row;
        }
        
        $response = [
            'status' => 'success',
            'data' => $sub_categories
        ];
        
        echo json_encode($response);
        exit;
    } elseif ($action == 'add_to_cart') {
        // Forward the request to the Master class
        require_once '../classes/Master.php';
        $master = new Master();
        
        // Call the add_to_cart method and output its response
        echo $master->add_to_cart();
        exit;
    }
}

// Default response for unknown actions or invalid requests
echo json_encode([
    'status' => 'error',
    'message' => 'Unknown action or invalid request',
    'received_action' => $_POST['action'] ?? 'none'
]);
?>
<?php
require_once '../../config.php';

if(isset($_POST['series_id'])){
    $series_id = intval($_POST['series_id']);
    
    $qry = $conn->query("SELECT * FROM models WHERE series_id = '$series_id' AND status = 1 ORDER BY name ASC");
    
    if($qry === false) {
        $response = [
            'status' => 'failed',
            'message' => 'Database error: ' . $conn->error
        ];
        echo json_encode($response);
        exit;
    }
    
    $models = [];
    
    while($row = $qry->fetch_assoc()){
        $models[$row['id']] = $row['name'];
    }
    
    $response = [
        'status' => 'success',
        'models' => $models
    ];
    
    echo json_encode($response);
    exit;
} else {
    $response = [
        'status' => 'failed',
        'message' => 'series_id parameter is required'
    ];
    echo json_encode($response);
    exit;
}
?>

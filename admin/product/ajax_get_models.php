<?php
require_once '../../config.php';

if(isset($_POST['series_id'])){
    $series_id = $_POST['series_id'];
    
    $qry = $conn->query("SELECT * FROM models WHERE series_id = '$series_id' AND status = 1 ORDER BY name ASC");
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
}
?>

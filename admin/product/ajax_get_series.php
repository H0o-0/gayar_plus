<?php
require_once '../../config.php';

if(isset($_POST['brand_id'])){
    $brand_id = intval($_POST['brand_id']);
    $qry = $conn->query("SELECT * FROM series WHERE brand_id = '$brand_id' AND status = 1 ORDER BY name ASC");
    if($qry === false) {
        $response = [
            'status' => 'failed',
            'message' => 'Database error: ' . $conn->error
        ];
        echo json_encode($response);
        exit;
    }
    $brand_id = $_POST['brand_id'];
    
    $qry = $conn->query("SELECT * FROM series WHERE brand_id = '$brand_id' AND status = 1 ORDER BY name ASC");
    $series = [];
    
    while($row = $qry->fetch_assoc()){
        // استخدام الاسم العربي إذا كان متوفراً، وإلا الاسم الإنجليزي
        $name = !empty($row['name_ar']) ? $row['name_ar'] : $row['name'];
        $series[$row['id']] = $name;
    }
    
    $response = [
        'status' => 'success',
        'series' => $series
    ];
    
    echo json_encode($response);
    exit;
} else {
    $response = [
        'status' => 'failed',
        'message' => 'brand_id parameter is required'
    ];
    echo json_encode($response);
    exit;
}
?>

<?php
require_once('../../config.php');
if(isset($_POST['series_id'])){
    $qry = $conn->query("SELECT * FROM `models` where series_id = '{$_POST['series_id']}' order by category asc");
    $data = array();
    while($row = $qry->fetch_assoc()){
        $data[] = array("id"=>$row['id'], "category"=>$row['category']);
    }
    echo json_encode(array('status'=>'success', 'data'=>$data));
}
?>
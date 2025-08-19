<?php
require_once('../../config.php');
if(isset($_POST['brand_id'])){
    $qry = $conn->query("SELECT * FROM `series` where brand_id = '{$_POST['brand_id']}' order by name asc");
    $data = array();
    while($row = $qry->fetch_assoc()){
        $data[] = array("id"=>$row['id'], "name"=>$row['name']);
    }
    echo json_encode(array('status'=>'success', 'data'=>$data));
}
?>
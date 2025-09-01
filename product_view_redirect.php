<?php
// Redirect script to generate MD5 hash and redirect to product view page
require_once 'config.php';

if(!isset($_GET['id'])) {
    header('Location: ./');
    exit;
}

$product_id = intval($_GET['id']);

// Get the MD5 hash of the product ID
$md5_hash = md5($product_id);

// Redirect to the product view page with the MD5 hash
header('Location: ./?p=product_view&id=' . $md5_hash);
exit;
?>
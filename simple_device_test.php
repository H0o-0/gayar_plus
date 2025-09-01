<?php
$pageTitle = "Device Menu Test";
require_once 'initialize.php';
include 'inc/header.php';
?>

<div style="padding: 2rem;">
    <h1>Device Menu Test Page</h1>
    <p>This page is for testing the device menu functionality.</p>
    
    <h2>Test Instructions:</h2>
    <ol>
        <li>Hover over the "الأجهزة" (Devices) menu in the navigation bar</li>
        <li>Select a brand</li>
        <li>Check if categories load</li>
        <li>Select a category</li>
        <li>Check if models load</li>
    </ol>
    
    <h2>Debug Information:</h2>
    <p>Base URL: <?php echo defined('base_url') ? base_url : 'Not defined'; ?></p>
    <p>JavaScript _base_url_: <span id="js-base-url">Not loaded</span></p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Display the JavaScript base URL
    var baseUrlDisplay = document.getElementById('js-base-url');
    if (typeof _base_url_ !== 'undefined') {
        baseUrlDisplay.textContent = _base_url_;
    } else {
        baseUrlDisplay.textContent = 'Not defined';
    }
    
    // Test if the menu system is initialized
    console.log('Device menu system:', window.GayarPlusMenu);
    if (window.GayarPlusMenu) {
        console.log('Menu system initialized');
    } else {
        console.log('Menu system not found');
    }
});
</script>

<?php include 'inc/modern-footer.php'; ?>
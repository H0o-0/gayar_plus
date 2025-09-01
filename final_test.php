<?php
$pageTitle = "Final Test - Device Menu";
require_once 'initialize.php';
include 'inc/header.php';
?>

<div style="padding: 2rem; text-align: center;">
    <h1>Final Test - Device Menu Functionality</h1>
    <p>This page tests if the device menu works correctly on all pages.</p>
    
    <div style="margin: 2rem 0; padding: 1rem; background-color: #e9f7ef; border-radius: 8px; border: 1px solid #28a745;">
        <h2>Test Status</h2>
        <p id="test-status">Testing device menu initialization...</p>
        <div id="test-details"></div>
    </div>
    
    <div style="margin: 2rem 0;">
        <h2>How to Test</h2>
        <ol style="text-align: right; direction: rtl; max-width: 600px; margin: 0 auto;">
            <li>قم بتحريك الماوس فوق قائمة "الأجهزة" في شريط التنقل</li>
            <li>اختر شركة من القائمة (مثل آيفون أو سامسونج)</li>
            <li>تحقق من ظهور الفئات تلقائياً تحت القائمة</li>
            <li>قم بتحريك الماوس فوق إحدى الفئات</li>
            <li>تحقق من ظهور الموديلات تلقائياً</li>
        </ol>
    </div>
    
    <div style="margin: 2rem 0; padding: 1rem; background-color: #f8f9fa; border-radius: 8px;">
        <h2>Debug Information</h2>
        <p>Base URL: <strong><?php echo defined('base_url') ? base_url : 'Not defined'; ?></strong></p>
        <p>JavaScript _base_url_: <strong><span id="js-base-url">Not loaded</span></strong></p>
        <p>Menu System: <strong><span id="menu-system-status">Checking...</span></strong></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Display the JavaScript base URL
    var baseUrlDisplay = document.getElementById('js-base-url');
    if (typeof _base_url_ !== 'undefined') {
        baseUrlDisplay.textContent = _base_url_;
        baseUrlDisplay.style.color = 'green';
    } else {
        baseUrlDisplay.textContent = 'Not defined';
        baseUrlDisplay.style.color = 'red';
    }
    
    // Check menu system status
    var menuStatus = document.getElementById('menu-system-status');
    if (window.GayarPlusMenu) {
        menuStatus.textContent = 'Loaded and Ready';
        menuStatus.style.color = 'green';
        document.getElementById('test-status').innerHTML = '<span style="color: green; font-weight: bold;">✓ Device menu is properly initialized!</span>';
        document.getElementById('test-details').innerHTML = '<p style="color: #155724;">The device menu should work correctly on this page. Try hovering over the "الأجهزة" menu to test.</p>';
    } else {
        menuStatus.textContent = 'Not Found';
        menuStatus.style.color = 'red';
        document.getElementById('test-status').innerHTML = '<span style="color: red; font-weight: bold;">✗ Device menu is not initialized!</span>';
        document.getElementById('test-details').innerHTML = '<p style="color: #721c24;">There may be an issue with the device menu initialization. Please check the browser console for errors.</p>';
    }
    
    // Log to console for debugging
    console.log('Final Test Page Loaded');
    console.log('Base URL:', typeof _base_url_ !== 'undefined' ? _base_url_ : 'Not defined');
    console.log('Menu System:', window.GayarPlusMenu);
});

// Additional test function
function runManualTest() {
    if (window.GayarPlusMenu && typeof window.GayarPlusMenu.init === 'function') {
        try {
            window.GayarPlusMenu.init();
            alert('Menu initialized successfully!');
        } catch (e) {
            alert('Error initializing menu: ' + e.message);
        }
    } else {
        alert('Menu system not found!');
    }
}
</script>

<?php include 'inc/modern-footer.php'; ?>
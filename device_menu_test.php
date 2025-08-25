<?php
$pageTitle = "Device Menu Test";
require_once 'initialize.php';
include 'inc/header.php';
?>

<div style="padding: 2rem; text-align: center;">
    <h1>Device Menu Test Page</h1>
    <p>This page is for testing the device menu functionality.</p>
    
    <h2>Test Instructions:</h2>
    <ol style="text-align: right; direction: rtl; max-width: 600px; margin: 0 auto;">
        <li>قم بتحريك الماوس فوق قائمة "الأجهزة" في شريط التنقل</li>
        <li>اختر شركة من القائمة</li>
        <li>تحقق من ظهور الفئات تلقائياً</li>
        <li>قم بتحريك الماوس فوق إحدى الفئات</li>
        <li>تحقق من ظهور الموديلات تلقائياً</li>
    </ol>
    
    <div style="margin-top: 2rem; padding: 1rem; background-color: #f0f8ff; border-radius: 8px;">
        <h3>Debug Information:</h3>
        <p>Base URL: <strong><?php echo defined('base_url') ? base_url : 'Not defined'; ?></strong></p>
        <p>JavaScript _base_url_: <strong><span id="js-base-url">Not loaded</span></strong></p>
    </div>
    
    <div style="margin-top: 2rem;">
        <button onclick="testMenuInitialization()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Test Menu Initialization
        </button>
        <div id="test-result" style="margin-top: 1rem; padding: 1rem; border-radius: 5px;"></div>
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
    
    // Test if the menu system is initialized
    console.log('Device menu system:', window.GayarPlusMenu);
});

function testMenuInitialization() {
    const resultDiv = document.getElementById('test-result');
    
    if (window.GayarPlusMenu) {
        resultDiv.innerHTML = '<p style="color: green; font-weight: bold;">✓ Menu system is properly initialized</p>';
        resultDiv.style.backgroundColor = '#d4edda';
        resultDiv.style.border = '1px solid #c3e6cb';
    } else {
        resultDiv.innerHTML = '<p style="color: red; font-weight: bold;">✗ Menu system is not initialized</p>';
        resultDiv.style.backgroundColor = '#f8d7da';
        resultDiv.style.border = '1px solid #f5c6cb';
    }
    
    // Try to initialize manually if not already initialized
    if (!window.GayarPlusMenu) {
        if (typeof window.GayarPlusMenu !== 'undefined' && typeof window.GayarPlusMenu.init === 'function') {
            try {
                window.GayarPlusMenu.init();
                resultDiv.innerHTML += '<p style="color: orange; font-weight: bold;">→ Attempted manual initialization</p>';
            } catch (e) {
                resultDiv.innerHTML += '<p style="color: red; font-weight: bold;">→ Manual initialization failed: ' + e.message + '</p>';
            }
        }
    }
}
</script>

<?php include 'inc/modern-footer.php'; ?>
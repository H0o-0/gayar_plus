<?php
$pageTitle = "Comprehensive Device Menu Test";
require_once 'initialize.php';
include 'inc/header.php';
?>

<div style="padding: 2rem;">
    <h1>Comprehensive Device Menu Test</h1>
    <p>This page tests if the device menu works correctly on all pages.</p>
    
    <div style="margin: 2rem 0; padding: 1rem; background-color: #e9f7ef; border-radius: 8px; border: 1px solid #28a745;">
        <h2>Test Instructions</h2>
        <ol style="text-align: right; direction: rtl; max-width: 600px;">
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
        <p>Brand Items Count: <strong><span id="brand-items-count">0</span></strong></p>
    </div>
    
    <div style="margin: 2rem 0;">
        <button onclick="runComprehensiveTest()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Run Comprehensive Test
        </button>
        <button onclick="manualInit()" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Manual Initialization
        </button>
        <div id="test-results" style="margin-top: 1rem; padding: 1rem; border-radius: 5px;"></div>
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
    var menuSystem = document.getElementById('menu-system-status');
    if (window.GayarPlusMenu) {
        menuSystem.textContent = 'Loaded';
        menuSystem.style.color = 'green';
    } else {
        menuSystem.textContent = 'Not Found';
        menuSystem.style.color = 'red';
    }
    
    // Count brand items
    var brandItems = document.querySelectorAll('.brand-item');
    document.getElementById('brand-items-count').textContent = brandItems.length;
    
    console.log('Comprehensive Test Page Loaded');
    console.log('Base URL:', typeof _base_url_ !== 'undefined' ? _base_url_ : 'Not defined');
    console.log('Menu System:', window.GayarPlusMenu);
    console.log('Brand Items:', brandItems.length);
});

function runComprehensiveTest() {
    const resultsDiv = document.getElementById('test-results');
    
    // Test base URL
    const baseUrl = typeof _base_url_ !== 'undefined' ? _base_url_ : null;
    
    // Test menu system
    const menuSystem = window.GayarPlusMenu;
    
    // Test brand items
    const brandItems = document.querySelectorAll('.brand-item');
    
    let testResults = `
        <h3>Comprehensive Test Results:</h3>
        <p style="${baseUrl ? 'color: green;' : 'color: red;'}">${baseUrl ? '✓' : '✗'} Base URL ${baseUrl ? 'is properly defined' : 'is not defined'}</p>
        <p style="${menuSystem ? 'color: green;' : 'color: red;'}">${menuSystem ? '✓' : '✗'} Menu System ${menuSystem ? 'Loaded' : 'Not Found'}</p>
        <p style="${brandItems.length > 0 ? 'color: green;' : 'color: red;'}">${brandItems.length > 0 ? '✓' : '✗'} Found ${brandItems.length} Brand Items</p>
    `;
    
    if (baseUrl && menuSystem) {
        testResults += '<p style="color: green;">✓ All basic requirements met. Device menu should work correctly.</p>';
        testResults += '<p>Please test the menu manually by hovering over the "الأجهزة" link in the navigation bar.</p>';
    } else {
        testResults += '<p style="color: red;">✗ Some requirements are not met. Device menu may not work correctly.</p>';
    }
    
    resultsDiv.innerHTML = testResults;
}

function manualInit() {
    const resultsDiv = document.getElementById('test-results');
    
    if (typeof window.GayarPlusMenu !== 'undefined' && typeof window.GayarPlusMenu.init === 'function') {
        try {
            window.GayarPlusMenu.init();
            resultsDiv.innerHTML = '<p style="color: green; font-weight: bold;">✓ Device menu initialized successfully!</p>';
        } catch (e) {
            resultsDiv.innerHTML = '<p style="color: red; font-weight: bold;">✗ Error initializing device menu: ' + e.message + '</p>';
        }
    } else {
        resultsDiv.innerHTML = '<p style="color: red; font-weight: bold;">✗ Device menu system not found!</p>';
    }
}
</script>

<?php include 'inc/modern-footer.php'; ?>
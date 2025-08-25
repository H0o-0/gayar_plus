<?php
$pageTitle = "Debug Menu Test";
require_once 'initialize.php';
include 'inc/header.php';
?>

<div style="padding: 2rem;">
    <h1>Debug Menu Test</h1>
    <p>This page is for debugging the device menu functionality.</p>
    
    <div id="debug-info" style="margin: 1rem 0; padding: 1rem; background-color: #f0f8ff; border-radius: 8px;">
        <h2>Debug Information</h2>
        <p>Base URL: <span id="base-url"><?php echo defined('base_url') ? base_url : 'Not defined'; ?></span></p>
        <p>JavaScript _base_url_: <span id="js-base-url">Not loaded</span></p>
        <p>Menu System: <span id="menu-system">Checking...</span></p>
        <p>Brand Items Count: <span id="brand-items-count">0</span></p>
    </div>
    
    <button onclick="runDebugTest()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Run Debug Test
    </button>
    
    <div id="test-results" style="margin-top: 1rem; padding: 1rem; border-radius: 5px;"></div>
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
    var menuSystem = document.getElementById('menu-system');
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
    
    console.log('Debug Test Page Loaded');
    console.log('Base URL:', typeof _base_url_ !== 'undefined' ? _base_url_ : 'Not defined');
    console.log('Menu System:', window.GayarPlusMenu);
    console.log('Brand Items:', brandItems.length);
});

function runDebugTest() {
    const resultsDiv = document.getElementById('test-results');
    
    // Test base URL
    const baseUrl = typeof _base_url_ !== 'undefined' ? _base_url_ : null;
    
    // Test menu system
    const menuSystem = window.GayarPlusMenu;
    
    // Test brand items
    const brandItems = document.querySelectorAll('.brand-item');
    
    // Test AJAX endpoints
    let ajaxTestResults = '';
    
    if (baseUrl) {
        const testUrl = baseUrl + 'ajax/get_brand_categories.php?brand_id=1';
        
        fetch(testUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                ajaxTestResults = '<p style="color: green;">✓ AJAX Test Successful</p>';
                ajaxTestResults += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                
                resultsDiv.innerHTML = `
                    <h3>Debug Test Results:</h3>
                    <p style="color: green;">✓ Base URL is properly defined</p>
                    <p style="${menuSystem ? 'color: green;' : 'color: red;'}">${menuSystem ? '✓' : '✗'} Menu System ${menuSystem ? 'Loaded' : 'Not Found'}</p>
                    <p style="${brandItems.length > 0 ? 'color: green;' : 'color: red;'}">${brandItems.length > 0 ? '✓' : '✗'} Found ${brandItems.length} Brand Items</p>
                    ${ajaxTestResults}
                `;
            })
            .catch(error => {
                ajaxTestResults = `<p style="color: red;">✗ AJAX Test Failed: ${error.message}</p>`;
                
                resultsDiv.innerHTML = `
                    <h3>Debug Test Results:</h3>
                    <p style="color: green;">✓ Base URL is properly defined</p>
                    <p style="${menuSystem ? 'color: green;' : 'color: red;'}">${menuSystem ? '✓' : '✗'} Menu System ${menuSystem ? 'Loaded' : 'Not Found'}</p>
                    <p style="${brandItems.length > 0 ? 'color: green;' : 'color: red;'}">${brandItems.length > 0 ? '✓' : '✗'} Found ${brandItems.length} Brand Items</p>
                    ${ajaxTestResults}
                `;
            });
    } else {
        resultsDiv.innerHTML = `
            <h3>Debug Test Results:</h3>
            <p style="color: red;">✗ Base URL is not properly defined</p>
            <p style="${menuSystem ? 'color: green;' : 'color: red;'}">${menuSystem ? '✓' : '✗'} Menu System ${menuSystem ? 'Loaded' : 'Not Found'}</p>
            <p style="${brandItems.length > 0 ? 'color: green;' : 'color: red;'}">${brandItems.length > 0 ? '✓' : '✗'} Found ${brandItems.length} Brand Items</p>
        `;
    }
}
</script>

<?php include 'inc/modern-footer.php'; ?>
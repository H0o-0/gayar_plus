<?php
$pageTitle = "Final Navigation Test - Gayar Plus";
require_once 'config.php';
include 'inc/header.php';
?>

<style>
.test-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
}

.test-section {
    margin-bottom: 2rem;
}

.test-section h2 {
    color: var(--primary-navy);
    margin-bottom: 1rem;
}

.test-button {
    background: var(--primary-blue);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    margin: 0.5rem;
}

.test-button:hover {
    background: var(--primary-navy);
}

.result {
    background: var(--light-gray);
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
    font-family: monospace;
    white-space: pre-wrap;
}

.success {
    background: #f0fff4;
    border: 1px solid #9ae6b4;
    color: #2f855a;
}

.error {
    background: #fed7d7;
    border: 1px solid #e53e3e;
    color: #c53030;
}
</style>

<div class="test-container">
    <h1>Final Navigation Test</h1>
    
    <div class="test-section">
        <h2>Immediate Debug Info</h2>
        <div id="debug-info" class="result"></div>
    </div>
    
    <div class="test-section">
        <h2>Test Navigation</h2>
        <button id="test-functions" class="test-button">Test Functions</button>
        <button id="test-ajax" class="test-button">Test AJAX Directly</button>
        <button id="reinit-nav" class="test-button">Reinitialize Navigation</button>
        <div id="test-result" class="result" style="display: none;"></div>
    </div>
    
    <div class="test-section">
        <h2>Manual Test</h2>
        <p>Try hovering over the "الأجهزة" menu in the navigation bar above, then hover over brands to see if categories load.</p>
    </div>
    
    <div class="test-section">
        <h2>Console Debugging</h2>
        <p>Open the browser console and try these commands:</p>
        <div class="result">
// Check if functions exist:
typeof loadBrandCategories
typeof loadCategoryModels

// Test AJAX directly:
fetch('./ajax/get_brand_categories.php?brand_id=1').then(r => r.json()).then(console.log)

// Try calling function directly:
loadBrandCategories(1)
        </div>
    </div>
</div>

<script>
// Immediate debug info
document.addEventListener('DOMContentLoaded', function() {
    const debugInfo = document.getElementById('debug-info');
    
    const info = '=== IMMEDIATE DEBUG INFO ===\n';
    info += 'Page loaded: ' + new Date().toLocaleTimeString() + '\n';
    info += 'DOM ready state: ' + document.readyState + '\n';
    info += 'initSiteWide: ' + (typeof initSiteWide) + '\n';
    info += 'initTopBarNav: ' + (typeof initTopBarNav) + '\n';
    info += 'loadBrandCategories: ' + (typeof loadBrandCategories) + '\n';
    info += 'loadCategoryModels: ' + (typeof loadCategoryModels) + '\n';
    
    debugInfo.textContent = info;
    
    // Additional console logging
    console.log('=== FINAL NAVIGATION TEST ===');
    console.log('initSiteWide:', typeof initSiteWide);
    console.log('initTopBarNav:', typeof initTopBarNav);
    console.log('loadBrandCategories:', typeof loadBrandCategories);
    console.log('loadCategoryModels:', typeof loadCategoryModels);
});

// Button event handlers
document.addEventListener('DOMContentLoaded', function() {
    const resultDiv = document.getElementById('test-result');
    
    // Test functions
    document.getElementById('test-functions').addEventListener('click', function() {
        resultDiv.style.display = 'block';
        resultDiv.className = 'result';
        
        let result = '=== FUNCTION TEST ===\n';
        
        result += 'initSiteWide: ' + (typeof initSiteWide) + '\n';
        result += 'initTopBarNav: ' + (typeof initTopBarNav) + '\n';
        result += 'loadBrandCategories: ' + (typeof loadBrandCategories) + '\n';
        result += 'loadCategoryModels: ' + (typeof loadCategoryModels) + '\n';
        
        // Try to call functions
        if (typeof initTopBarNav === 'function') {
            try {
                result += '\nCalling initTopBarNav()...\n';
                initTopBarNav();
                result += 'initTopBarNav() completed\n';
            } catch (e) {
                result += 'ERROR calling initTopBarNav(): ' + e.message + '\n';
            }
        }
        
        resultDiv.textContent = result;
        resultDiv.className = 'result success';
    });
    
    // Test AJAX directly
    document.getElementById('test-ajax').addEventListener('click', function() {
        resultDiv.style.display = 'block';
        resultDiv.className = 'result';
        
        let result = '=== AJAX TEST ===\n';
        resultDiv.textContent = result;
        
        // Test brand categories
        fetch('./ajax/get_brand_categories.php?brand_id=1')
            .then(response => {
                result += 'Brand categories response status: ' + response.status + '\n';
                return response.json();
            })
            .then(data => {
                result += 'Brand categories success: ' + data.success + '\n';
                if (data.categories) {
                    result += 'Categories count: ' + data.categories.length + '\n';
                }
                resultDiv.textContent = result;
                resultDiv.className = 'result success';
            })
            .catch(error => {
                result += 'Brand categories error: ' + error.message + '\n';
                resultDiv.textContent = result;
                resultDiv.className = 'result error';
            });
    });
    
    // Reinitialize navigation
    document.getElementById('reinit-nav').addEventListener('click', function() {
        resultDiv.style.display = 'block';
        resultDiv.className = 'result';
        
        let result = '=== REINITIALIZE NAVIGATION ===\n';
        
        if (typeof initTopBarNav === 'function') {
            try {
                result += 'Reinitializing navigation...\n';
                initTopBarNav();
                result += 'Navigation reinitialized successfully\n';
                resultDiv.className = 'result success';
            } catch (e) {
                result += 'ERROR reinitializing navigation: ' + e.message + '\n';
                resultDiv.className = 'result error';
            }
        } else {
            result += 'initTopBarNav function not available\n';
            resultDiv.className = 'result error';
        }
        
        resultDiv.textContent = result;
    });
});
</script>

<?php include 'inc/modern-footer.php'; ?>
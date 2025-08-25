<?php
$pageTitle = "Navigation Test After Fix - Gayar Plus";
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
    <h1>Navigation Test After Fix</h1>
    
    <div class="test-section">
        <h2>Test Instructions</h2>
        <p>1. Open browser console (F12)</p>
        <p>2. Click the test button below</p>
        <p>3. Try hovering over the "الأجهزة" menu and brands</p>
        <p>4. Check console for output</p>
    </div>
    
    <div class="test-section">
        <h2>Run Test</h2>
        <button id="run-test" class="test-button">Run Comprehensive Test</button>
        <div id="test-result" class="result" style="display: none;"></div>
    </div>
    
    <div class="test-section">
        <h2>Console Commands to Try</h2>
        <div class="result">
// Check if functions exist:
typeof initSiteWide
typeof initTopBarNav
typeof loadBrandCategories
typeof loadCategoryModels

// Try calling functions directly:
loadBrandCategories(1)
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('run-test').addEventListener('click', function() {
        const resultDiv = document.getElementById('test-result');
        resultDiv.style.display = 'block';
        
        let result = '=== COMPREHENSIVE NAVIGATION TEST ===\n\n';
        
        try {
            // Check all elements
            const devicesNavItem = document.querySelector('.nav-devices');
            const megaMenu = document.querySelector('.mega-menu');
            const brandItems = document.querySelectorAll('.brand-item');
            const categoriesSection = document.getElementById('categories-section');
            const phonesSection = document.getElementById('phones-section');
            
            result += '=== ELEMENT CHECK ===\n';
            result += 'Devices Nav Item: ' + (devicesNavItem ? 'FOUND' : 'MISSING') + '\n';
            result += 'Mega Menu: ' + (megaMenu ? 'FOUND' : 'MISSING') + '\n';
            result += 'Brand Items: ' + brandItems.length + ' found\n';
            result += 'Categories Section: ' + (categoriesSection ? 'FOUND' : 'MISSING') + '\n';
            result += 'Phones Section: ' + (phonesSection ? 'FOUND' : 'MISSING') + '\n';
            
            // Check functions
            result += '\n=== FUNCTION CHECK ===\n';
            result += 'initSiteWide: ' + (typeof initSiteWide) + '\n';
            result += 'initTopBarNav: ' + (typeof initTopBarNav) + '\n';
            result += 'loadBrandCategories: ' + (typeof loadBrandCategories) + '\n';
            result += 'loadCategoryModels: ' + (typeof loadCategoryModels) + '\n';
            
            // Try to manually initialize if needed
            result += '\n=== INITIALIZATION TEST ===\n';
            if (typeof initTopBarNav === 'function') {
                result += 'Calling initTopBarNav()\n';
                initTopBarNav();
                result += 'initTopBarNav completed\n';
            } else {
                result += 'initTopBarNav not available\n';
            }
            
            if (typeof initSiteWide === 'function') {
                result += 'Calling initSiteWide()\n';
                initSiteWide();
                result += 'initSiteWide completed\n';
            } else {
                result += 'initSiteWide not available\n';
            }
            
            resultDiv.className = 'result success';
            resultDiv.textContent = result;
            
        } catch (error) {
            result += '\n=== ERROR ===\n';
            result += 'ERROR: ' + error.message + '\n';
            resultDiv.className = 'result error';
            resultDiv.textContent = result;
            console.error('Test error:', error);
        }
    });
    
    // Additional debugging
    console.log('=== PAGE LOADED ===');
    console.log('initSiteWide:', typeof initSiteWide);
    console.log('initTopBarNav:', typeof initTopBarNav);
    console.log('loadBrandCategories:', typeof loadBrandCategories);
    console.log('loadCategoryModels:', typeof loadCategoryModels);
});
</script>

<?php include 'inc/modern-footer.php'; ?>
<?php
$pageTitle = "Quick Navigation Test - Gayar Plus";
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
    <h1>Quick Navigation Test</h1>
    
    <div class="test-section">
        <h2>Test Navigation</h2>
        <button id="test-nav" class="test-button">Test Navigation Functions</button>
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
// Test AJAX directly:
fetch('./ajax/get_brand_categories.php?brand_id=1').then(r => r.json()).then(console.log)

// Try calling function directly:
if (typeof loadBrandCategories === 'function') {
    loadBrandCategories(1);
} else {
    console.log('Function not available');
}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('test-nav').addEventListener('click', function() {
        const resultDiv = document.getElementById('test-result');
        resultDiv.style.display = 'block';
        resultDiv.className = 'result';
        
        let result = '=== QUICK NAVIGATION TEST ===\n\n';
        
        // Check if functions exist
        result += 'loadBrandCategories: ' + (typeof loadBrandCategories) + '\n';
        result += 'loadCategoryModels: ' + (typeof loadCategoryModels) + '\n';
        result += 'initTopBarNav: ' + (typeof initTopBarNav) + '\n';
        result += 'initSiteWide: ' + (typeof initSiteWide) + '\n';
        
        // Try direct AJAX call
        result += '\n=== TESTING AJAX ===\n';
        fetch('./ajax/get_brand_categories.php?brand_id=1')
            .then(response => {
                result += 'AJAX Status: ' + response.status + '\n';
                return response.json();
            })
            .then(data => {
                result += 'Success: ' + data.success + '\n';
                if (data.categories) {
                    result += 'Categories: ' + data.categories.length + '\n';
                }
                resultDiv.textContent = result;
                resultDiv.className = 'result success';
            })
            .catch(error => {
                result += 'AJAX Error: ' + error.message + '\n';
                resultDiv.textContent = result;
                resultDiv.className = 'result error';
            });
    });
});
</script>

<?php include 'inc/modern-footer.php'; ?>
<?php
$pageTitle = "AJAX Debug - Gayar Plus";
require_once 'config.php';
include 'inc/header.php';
?>

<style>
.debug-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
}

.debug-section {
    margin-bottom: 2rem;
}

.debug-section h2 {
    color: var(--primary-navy);
    margin-bottom: 1rem;
}

.debug-output {
    background: var(--light-gray);
    padding: 1rem;
    border-radius: 8px;
    font-family: monospace;
    white-space: pre-wrap;
    max-height: 300px;
    overflow-y: auto;
}

.debug-button {
    background: var(--primary-blue);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    margin: 0.5rem;
}

.debug-button:hover {
    background: var(--primary-navy);
}

.error {
    color: #e53e3e;
    background: #fef2f2;
    border: 1px solid #fed7d7;
}

.success {
    color: #2f855a;
    background: #f0fff4;
    border: 1px solid #9ae6b4;
}
</style>

<div class="debug-container">
    <h1>AJAX Debug Page</h1>
    
    <div class="debug-section">
        <h2>Test AJAX Endpoints</h2>
        <button id="test-brand-categories" class="debug-button">Test Brand Categories (Brand ID: 1)</button>
        <button id="test-category-models" class="debug-button">Test Category Models (Category ID: 1)</button>
        <div id="ajax-output" class="debug-output"></div>
    </div>
    
    <div class="debug-section">
        <h2>JavaScript Functions Check</h2>
        <button id="check-functions" class="debug-button">Check Navigation Functions</button>
        <div id="functions-output" class="debug-output"></div>
    </div>
    
    <div class="debug-section">
        <h2>Manual Navigation Test</h2>
        <p>Try hovering over the "الأجهزة" menu item in the navigation bar above, then click on a brand to see if categories load.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ajaxOutput = document.getElementById('ajax-output');
    const functionsOutput = document.getElementById('functions-output');
    
    // Test brand categories AJAX endpoint
    document.getElementById('test-brand-categories').addEventListener('click', function() {
        ajaxOutput.textContent = 'Testing brand categories endpoint...\n';
        
        fetch('./ajax/get_brand_categories.php?brand_id=1')
            .then(response => {
                ajaxOutput.textContent += 'Response Status: ' + response.status + '\n';
                ajaxOutput.textContent += 'Response URL: ' + response.url + '\n';
                return response.json();
            })
            .then(data => {
                ajaxOutput.textContent += 'Success: ' + data.success + '\n';
                if (data.categories) {
                    ajaxOutput.textContent += 'Categories Count: ' + data.categories.length + '\n';
                    ajaxOutput.textContent += 'Categories: ' + JSON.stringify(data.categories, null, 2) + '\n';
                }
                if (data.message) {
                    ajaxOutput.textContent += 'Message: ' + data.message + '\n';
                }
                
                if (data.success) {
                    ajaxOutput.classList.remove('error');
                    ajaxOutput.classList.add('success');
                } else {
                    ajaxOutput.classList.remove('success');
                    ajaxOutput.classList.add('error');
                }
            })
            .catch(error => {
                ajaxOutput.textContent += 'Error: ' + error.message + '\n';
                ajaxOutput.classList.remove('success');
                ajaxOutput.classList.add('error');
                console.error('Brand categories error:', error);
            });
    });
    
    // Test category models AJAX endpoint
    document.getElementById('test-category-models').addEventListener('click', function() {
        ajaxOutput.textContent = 'Testing category models endpoint...\n';
        
        fetch('./ajax/get_category_models.php?category_id=1')
            .then(response => {
                ajaxOutput.textContent += 'Response Status: ' + response.status + '\n';
                ajaxOutput.textContent += 'Response URL: ' + response.url + '\n';
                return response.json();
            })
            .then(data => {
                ajaxOutput.textContent += 'Success: ' + data.success + '\n';
                if (data.models) {
                    ajaxOutput.textContent += 'Models Count: ' + data.models.length + '\n';
                    ajaxOutput.textContent += 'Models: ' + JSON.stringify(data.models, null, 2) + '\n';
                }
                if (data.message) {
                    ajaxOutput.textContent += 'Message: ' + data.message + '\n';
                }
                
                if (data.success) {
                    ajaxOutput.classList.remove('error');
                    ajaxOutput.classList.add('success');
                } else {
                    ajaxOutput.classList.remove('success');
                    ajaxOutput.classList.add('error');
                }
            })
            .catch(error => {
                ajaxOutput.textContent += 'Error: ' + error.message + '\n';
                ajaxOutput.classList.remove('success');
                ajaxOutput.classList.add('error');
                console.error('Category models error:', error);
            });
    });
    
    // Check JavaScript functions
    document.getElementById('check-functions').addEventListener('click', function() {
        functionsOutput.textContent = 'JavaScript Functions Check:\n';
        functionsOutput.textContent += 'loadBrandCategories: ' + (typeof loadBrandCategories !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        functionsOutput.textContent += 'loadCategoryModels: ' + (typeof loadCategoryModels !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        functionsOutput.textContent += 'jQuery: ' + (typeof $ !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        
        // Check for navigation elements
        const devicesNavItem = document.querySelector('.nav-devices');
        const brandItems = document.querySelectorAll('.brand-item');
        
        functionsOutput.textContent += '\nNavigation Elements:\n';
        functionsOutput.textContent += 'Devices Nav Item: ' + (devicesNavItem ? 'Found' : 'Not Found') + '\n';
        functionsOutput.textContent += 'Brand Items Count: ' + brandItems.length + '\n';
        
        if (devicesNavItem) {
            functionsOutput.textContent += 'Has "show" class: ' + devicesNavItem.classList.contains('show') + '\n';
        }
    });
});
</script>

<?php include 'inc/modern-footer.php'; ?>
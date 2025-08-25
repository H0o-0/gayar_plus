<?php
$pageTitle = "Navigation Test - Gayar Plus";
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

.test-output {
    background: var(--light-gray);
    padding: 1rem;
    border-radius: 8px;
    font-family: monospace;
    white-space: pre-wrap;
    max-height: 300px;
    overflow-y: auto;
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
</style>

<div class="test-container">
    <h1>Navigation Test Page</h1>
    
    <div class="test-section">
        <h2>Navigation Elements Check</h2>
        <button id="check-nav" class="test-button">Check Navigation Elements</button>
        <div id="nav-output" class="test-output"></div>
    </div>
    
    <div class="test-section">
        <h2>AJAX Test</h2>
        <button id="test-ajax" class="test-button">Test AJAX Endpoints</button>
        <div id="ajax-output" class="test-output"></div>
    </div>
    
    <div class="test-section">
        <h2>JavaScript Functions Check</h2>
        <button id="check-js" class="test-button">Check JavaScript Functions</button>
        <div id="js-output" class="test-output"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check navigation elements
    document.getElementById('check-nav').addEventListener('click', function() {
        const output = document.getElementById('nav-output');
        
        // Check for navigation elements
        const devicesNavItem = document.querySelector('.nav-devices');
        const megaMenu = document.querySelector('.mega-menu');
        const brandItems = document.querySelectorAll('.brand-item');
        
        output.textContent = 'Navigation Elements Check:\n';
        output.textContent += 'Devices Nav Item: ' + (devicesNavItem ? 'Found' : 'Not Found') + '\n';
        output.textContent += 'Mega Menu: ' + (megaMenu ? 'Found' : 'Not Found') + '\n';
        output.textContent += 'Brand Items Count: ' + brandItems.length + '\n';
        
        if (devicesNavItem) {
            output.textContent += 'Devices Nav Item Classes: ' + devicesNavItem.className + '\n';
        }
        
        if (megaMenu) {
            output.textContent += 'Mega Menu Classes: ' + megaMenu.className + '\n';
        }
        
        // Check if hover events are attached
        if (devicesNavItem) {
            const listeners = getEventListeners(devicesNavItem);
            output.textContent += 'Hover Event Listeners: ' + (listeners.mouseenter ? 'Yes' : 'No') + '\n';
        }
    });
    
    // Test AJAX endpoints
    document.getElementById('test-ajax').addEventListener('click', function() {
        const output = document.getElementById('ajax-output');
        output.textContent = 'Testing AJAX endpoints...\n';
        
        // Test brand categories endpoint
        fetch('./ajax/get_brand_categories.php?brand_id=1')
            .then(response => {
                output.textContent += 'Brand Categories Response Status: ' + response.status + '\n';
                return response.json();
            })
            .then(data => {
                output.textContent += 'Brand Categories Success: ' + data.success + '\n';
                if (data.categories) {
                    output.textContent += 'Categories Count: ' + data.categories.length + '\n';
                }
            })
            .catch(error => {
                output.textContent += 'Brand Categories Error: ' + error.message + '\n';
            });
            
        // Test category models endpoint
        fetch('./ajax/get_category_models.php?category_id=1')
            .then(response => {
                output.textContent += 'Category Models Response Status: ' + response.status + '\n';
                return response.json();
            })
            .then(data => {
                output.textContent += 'Category Models Success: ' + data.success + '\n';
                if (data.models) {
                    output.textContent += 'Models Count: ' + data.models.length + '\n';
                }
            })
            .catch(error => {
                output.textContent += 'Category Models Error: ' + error.message + '\n';
            });
    });
    
    // Check JavaScript functions
    document.getElementById('check-js').addEventListener('click', function() {
        const output = document.getElementById('js-output');
        
        output.textContent = 'JavaScript Functions Check:\n';
        output.textContent += 'loadBrandCategories function: ' + (typeof loadBrandCategories !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        output.textContent += 'loadCategoryModels function: ' + (typeof loadCategoryModels !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        output.textContent += 'initMegaMenu function: ' + (typeof initMegaMenu !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        output.textContent += 'initSiteWide function: ' + (typeof initSiteWide !== 'undefined' ? 'Available' : 'Not Available') + '\n';
    });
    
    // Helper function to get event listeners (for debugging)
    function getEventListeners(element) {
        // This is a simplified version - in real browsers, you might use getEventListeners from dev tools
        // For now, we'll just check if the element has the expected event listeners
        return {
            mouseenter: element.onmouseenter ? true : false,
            mouseleave: element.onmouseleave ? true : false
        };
    }
});
</script>

<?php include 'inc/modern-footer.php'; ?>
<?php
$pageTitle = "JavaScript Debug - Gayar Plus";
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
</style>

<div class="debug-container">
    <h1>JavaScript Debug Page</h1>
    
    <div class="debug-section">
        <h2>Console Errors</h2>
        <button id="check-errors" class="debug-button">Check for Console Errors</button>
        <div id="errors-output" class="debug-output"></div>
    </div>
    
    <div class="debug-section">
        <h2>Navigation JavaScript Test</h2>
        <button id="test-nav-js" class="debug-button">Test Navigation JavaScript</button>
        <div id="nav-js-output" class="debug-output"></div>
    </div>
    
    <div class="debug-section">
        <h2>AJAX Endpoints Test</h2>
        <button id="test-ajax" class="debug-button">Test AJAX Endpoints</button>
        <div id="ajax-output" class="debug-output"></div>
    </div>
</div>

<script>
// Capture console errors
(function() {
    const originalError = console.error;
    const errorOutput = document.getElementById('errors-output');
    let errorCount = 0;
    
    console.error = function() {
        errorCount++;
        const args = Array.from(arguments);
        const errorMessage = args.map(arg => typeof arg === 'object' ? JSON.stringify(arg) : arg).join(' ');
        
        const errorElement = document.createElement('div');
        errorElement.className = 'error';
        errorElement.textContent = `[${new Date().toLocaleTimeString()}] ERROR: ${errorMessage}`;
        
        if (errorOutput) {
            errorOutput.appendChild(errorElement);
            errorOutput.scrollTop = errorOutput.scrollHeight;
        }
        
        // Call original console.error
        originalError.apply(console, arguments);
    };
    
    // Also capture unhandled errors
    window.addEventListener('error', function(event) {
        errorCount++;
        const errorElement = document.createElement('div');
        errorElement.className = 'error';
        errorElement.textContent = `[${new Date().toLocaleTimeString()}] UNHANDLED ERROR: ${event.message} at ${event.filename}:${event.lineno}:${event.colno}`;
        
        if (errorOutput) {
            errorOutput.appendChild(errorElement);
            errorOutput.scrollTop = errorOutput.scrollHeight;
        }
    });
    
    document.getElementById('check-errors').addEventListener('click', function() {
        if (errorCount === 0) {
            errorOutput.textContent = 'No console errors detected.';
        } else {
            errorOutput.textContent = `Total errors detected: ${errorCount}\n` + errorOutput.textContent;
        }
    });
})();

document.addEventListener('DOMContentLoaded', function() {
    // Test navigation JavaScript
    document.getElementById('test-nav-js').addEventListener('click', function() {
        const output = document.getElementById('nav-js-output');
        output.textContent = '';
        
        try {
            // Check for navigation elements
            const devicesNavItem = document.querySelector('.nav-devices');
            const megaMenu = document.querySelector('.mega-menu');
            const brandItems = document.querySelectorAll('.brand-item');
            
            output.textContent += 'Navigation Elements Check:\n';
            output.textContent += 'Devices Nav Item: ' + (devicesNavItem ? 'Found' : 'Not Found') + '\n';
            output.textContent += 'Mega Menu: ' + (megaMenu ? 'Found' : 'Not Found') + '\n';
            output.textContent += 'Brand Items Count: ' + brandItems.length + '\n';
            
            // Check if required functions exist
            output.textContent += '\nJavaScript Functions Check:\n';
            output.textContent += 'loadBrandCategoriesFromServer: ' + (typeof loadBrandCategoriesFromServer !== 'undefined' ? 'Available' : 'Not Available') + '\n';
            output.textContent += 'loadCategoryModelsFromServer: ' + (typeof loadCategoryModelsFromServer !== 'undefined' ? 'Available' : 'Not Available') + '\n';
            output.textContent += 'initMegaMenu: ' + (typeof initMegaMenu !== 'undefined' ? 'Available' : 'Not Available') + '\n';
            output.textContent += 'initSiteWide: ' + (typeof initSiteWide !== 'undefined' ? 'Available' : 'Not Available') + '\n';
            
            // Check if event listeners are attached
            if (devicesNavItem) {
                output.textContent += '\nEvent Listeners Check:\n';
                output.textContent += 'mouseenter listener: ' + (devicesNavItem.onmouseenter !== null ? 'Attached' : 'Not Attached') + '\n';
                output.textContent += 'mouseleave listener: ' + (devicesNavItem.onmouseleave !== null ? 'Attached' : 'Not Attached') + '\n';
            }
            
            output.textContent += '\nTest completed successfully.';
        } catch (error) {
            output.textContent += 'Error during test: ' + error.message;
            console.error('Navigation test error:', error);
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
                if (data.message) {
                    output.textContent += 'Message: ' + data.message + '\n';
                }
            })
            .catch(error => {
                output.textContent += 'Brand Categories Error: ' + error.message + '\n';
                console.error('Brand categories error:', error);
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
                if (data.message) {
                    output.textContent += 'Message: ' + data.message + '\n';
                }
            })
            .catch(error => {
                output.textContent += 'Category Models Error: ' + error.message + '\n';
                console.error('Category models error:', error);
            });
    });
});
</script>

<?php include 'inc/modern-footer.php'; ?>
<?php
$pageTitle = "Navigation Debug - Gayar Plus";
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
</style>

<div class="debug-container">
    <h1>Navigation Debug Page</h1>
    
    <div class="debug-section">
        <h2>JavaScript Functions Check</h2>
        <button id="check-functions" class="debug-button">Check JavaScript Functions</button>
        <div id="functions-output" class="debug-output"></div>
    </div>
    
    <div class="debug-section">
        <h2>Event Listeners Check</h2>
        <button id="check-events" class="debug-button">Check Event Listeners</button>
        <div id="events-output" class="debug-output"></div>
    </div>
    
    <div class="debug-section">
        <h2>Test Navigation Hover</h2>
        <button id="test-hover" class="debug-button">Test Hover Functionality</button>
        <div id="hover-output" class="debug-output"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check JavaScript functions
    document.getElementById('check-functions').addEventListener('click', function() {
        const output = document.getElementById('functions-output');
        
        output.textContent = 'JavaScript Functions Check:\n';
        output.textContent += 'loadBrandCategoriesFromServer function: ' + (typeof loadBrandCategoriesFromServer !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        output.textContent += 'loadCategoryModelsFromServer function: ' + (typeof loadCategoryModelsFromServer !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        output.textContent += 'initMegaMenu function: ' + (typeof initMegaMenu !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        output.textContent += 'initSiteWide function: ' + (typeof initSiteWide !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        output.textContent += 'jQuery: ' + (typeof $ !== 'undefined' ? 'Available' : 'Not Available') + '\n';
    });
    
    // Check event listeners
    document.getElementById('check-events').addEventListener('click', function() {
        const output = document.getElementById('events-output');
        
        // Check for navigation elements
        const devicesNavItem = document.querySelector('.nav-devices');
        const brandItems = document.querySelectorAll('.brand-item');
        
        output.textContent = 'Event Listeners Check:\n';
        output.textContent += 'Devices Nav Item: ' + (devicesNavItem ? 'Found' : 'Not Found') + '\n';
        output.textContent += 'Brand Items Count: ' + brandItems.length + '\n';
        
        if (devicesNavItem) {
            // Check if hover events are attached
            const hasMouseEnter = devicesNavItem.onmouseenter !== null;
            const hasMouseLeave = devicesNavItem.onmouseleave !== null;
            output.textContent += 'Mouse Enter Event: ' + (hasMouseEnter ? 'Attached' : 'Not Attached') + '\n';
            output.textContent += 'Mouse Leave Event: ' + (hasMouseLeave ? 'Attached' : 'Not Attached') + '\n';
            
            // Check if show class is present
            const hasShowClass = devicesNavItem.classList.contains('show');
            output.textContent += 'Has "show" class: ' + hasShowClass + '\n';
        }
    });
    
    // Test hover functionality
    document.getElementById('test-hover').addEventListener('click', function() {
        const output = document.getElementById('hover-output');
        
        // Check for navigation elements
        const devicesNavItem = document.querySelector('.nav-devices');
        const megaMenu = document.querySelector('.mega-menu');
        
        output.textContent = 'Hover Test:\n';
        
        if (devicesNavItem && megaMenu) {
            // Simulate hover
            devicesNavItem.classList.add('show');
            output.textContent += 'Added "show" class to devices nav item\n';
            
            // Check if menu is visible
            const isVisible = megaMenu.offsetParent !== null;
            output.textContent += 'Mega menu is visible: ' + isVisible + '\n';
            
            // Remove show class after 2 seconds
            setTimeout(() => {
                devicesNavItem.classList.remove('show');
                output.textContent += 'Removed "show" class after 2 seconds\n';
            }, 2000);
        } else {
            output.textContent += 'Navigation elements not found\n';
        }
    });
});
</script>

<?php include 'inc/modern-footer.php'; ?>
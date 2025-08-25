<?php
$pageTitle = "Test Navigation - Gayar Plus";
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
    <h1>Test Navigation Page</h1>
    
    <div class="test-section">
        <h2>Navigation Elements Check</h2>
        <button id="check-nav" class="test-button">Check Navigation Elements</button>
        <div id="nav-output" class="test-output"></div>
    </div>
    
    <div class="test-section">
        <h2>JavaScript Functions Check</h2>
        <button id="check-js" class="test-button">Check JavaScript Functions</button>
        <div id="js-output" class="test-output"></div>
    </div>
    
    <div class="test-section">
        <h2>Test Navigation Hover</h2>
        <button id="test-hover" class="test-button">Test Hover Functionality</button>
        <div id="hover-output" class="test-output"></div>
    </div>
    
    <div class="test-section">
        <h2>Instructions</h2>
        <p>1. Click the "Check Navigation Elements" button to verify that navigation elements are present</p>
        <p>2. Click the "Check JavaScript Functions" button to verify that required JavaScript functions are available</p>
        <p>3. Click the "Test Hover Functionality" button to simulate hover on the devices menu</p>
        <p>4. Try manually hovering over the "الأجهزة" menu item in the navigation bar to see if it works</p>
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
    });
    
    // Check JavaScript functions
    document.getElementById('check-js').addEventListener('click', function() {
        const output = document.getElementById('js-output');
        
        output.textContent = 'JavaScript Functions Check:\n';
        output.textContent += 'loadBrandCategories function: ' + (typeof loadBrandCategories !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        output.textContent += 'loadCategoryModels function: ' + (typeof loadCategoryModels !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        output.textContent += 'initSiteWide function: ' + (typeof initSiteWide !== 'undefined' ? 'Available' : 'Not Available') + '\n';
        output.textContent += 'jQuery: ' + (typeof $ !== 'undefined' ? 'Available' : 'Not Available') + '\n';
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
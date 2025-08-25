<?php
$pageTitle = "Simple Navigation Debug - Gayar Plus";
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
    <h1>Simple Navigation Debug</h1>
    
    <div class="debug-section">
        <h2>Test Instructions</h2>
        <p>1. Open your browser's developer console (F12)</p>
        <p>2. Hover over the "الأجهزة" menu item in the navigation bar</p>
        <p>3. Hover over any brand in the menu</p>
        <p>4. Check the console for debugging output</p>
    </div>
    
    <div class="debug-section">
        <h2>Manual Test</h2>
        <button id="test-nav" class="debug-button">Test Navigation Elements</button>
        <div id="nav-output" class="debug-output"></div>
    </div>
    
    <div class="debug-section">
        <h2>Expected Console Output</h2>
        <div class="debug-output">
When you hover over "الأجهزة" menu:
- "Found brand items: X" (where X is the number of brands)

When you hover over a brand:
- "Hovering over brand: Y" (where Y is the brand ID)
- "Loading categories for brand: Y"
- "Brand categories response status: 200"
- "Brand categories data: {success: true, categories: Array(...)}"
        </div>
    </div>
    
    <div class="debug-section">
        <h2>Troubleshooting</h2>
        <p>If you don't see the expected output:</p>
        <ol>
            <li>Make sure JavaScript is enabled in your browser</li>
            <li>Check for JavaScript errors in the console</li>
            <li>Refresh the page and try again</li>
            <li>Clear your browser cache and try again</li>
        </ol>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navOutput = document.getElementById('nav-output');
    
    document.getElementById('test-nav').addEventListener('click', function() {
        navOutput.textContent = 'Testing navigation elements...\n';
        
        try {
            // Check for navigation elements
            const devicesNavItem = document.querySelector('.nav-devices');
            const megaMenu = document.querySelector('.mega-menu');
            const brandItems = document.querySelectorAll('.brand-item');
            const categoriesSection = document.getElementById('categories-section');
            const phonesSection = document.getElementById('phones-section');
            
            navOutput.textContent += 'Devices Nav Item: ' + (devicesNavItem ? 'Found' : 'Not Found') + '\n';
            navOutput.textContent += 'Mega Menu: ' + (megaMenu ? 'Found' : 'Not Found') + '\n';
            navOutput.textContent += 'Brand Items Count: ' + brandItems.length + '\n';
            navOutput.textContent += 'Categories Section: ' + (categoriesSection ? 'Found' : 'Not Found') + '\n';
            navOutput.textContent += 'Phones Section: ' + (phonesSection ? 'Found' : 'Not Found') + '\n';
            
            if (devicesNavItem) {
                navOutput.textContent += 'Devices Nav Item Classes: ' + devicesNavItem.className + '\n';
            }
            
            if (brandItems.length > 0) {
                navOutput.textContent += 'First Brand Item Dataset: ' + JSON.stringify(brandItems[0].dataset) + '\n';
            }
            
            navOutput.classList.remove('error');
            navOutput.classList.add('success');
            
            // Add some manual debugging
            console.log('Manual Navigation Test:');
            console.log('Devices Nav Item:', devicesNavItem);
            console.log('Brand Items Count:', brandItems.length);
            
            if (brandItems.length > 0) {
                console.log('First Brand Item Dataset:', brandItems[0].dataset);
            }
        } catch (error) {
            navOutput.textContent += 'Error: ' + error.message;
            navOutput.classList.remove('success');
            navOutput.classList.add('error');
            console.error('Test error:', error);
        }
    });
    
    // Add some debugging to check if the script is running
    console.log('Simple Navigation Debug Page Loaded');
    console.log('Checking for navigation functions...');
    
    // Check if required functions exist
    setTimeout(() => {
        console.log('Navigation Functions Check:');
        console.log('loadBrandCategories function:', typeof loadBrandCategories !== 'undefined' ? 'Available' : 'Not Available');
        console.log('loadCategoryModels function:', typeof loadCategoryModels !== 'undefined' ? 'Available' : 'Not Available');
    }, 1000);
});
</script>

<?php include 'inc/modern-footer.php'; ?>
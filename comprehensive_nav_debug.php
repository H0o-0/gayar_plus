<?php
$pageTitle = "Comprehensive Navigation Debug - Gayar Plus";
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

.warning {
    color: #d69e2e;
    background: #fffbeb;
    border: 1px solid #f6e05e;
}
</style>

<div class="debug-container">
    <h1>Comprehensive Navigation Debug</h1>
    
    <div class="debug-section">
        <h2>Navigation Elements Check</h2>
        <button id="check-elements" class="debug-button">Check Navigation Elements</button>
        <div id="elements-output" class="debug-output"></div>
    </div>
    
    <div class="debug-section">
        <h2>Event Listeners Check</h2>
        <button id="check-events" class="debug-button">Check Event Listeners</button>
        <div id="events-output" class="debug-output"></div>
    </div>
    
    <div class="debug-section">
        <h2>Manual Navigation Test</h2>
        <p>Try hovering over the "الأجهزة" menu item in the navigation bar above, then hover over brands to see if categories load.</p>
        <p>Open your browser's developer console (F12) to see detailed debugging information.</p>
    </div>
    
    <div class="debug-section">
        <h2>Expected Behavior</h2>
        <div class="debug-output">
1. When you hover over "الأجهزة" menu item, the mega menu should appear
2. When you hover over any brand, categories should load in the second column
3. When you hover over any category, models should load in the third column
4. Console should show detailed logging of all AJAX requests
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const elementsOutput = document.getElementById('elements-output');
    const eventsOutput = document.getElementById('events-output');
    
    // Check navigation elements
    document.getElementById('check-elements').addEventListener('click', function() {
        elementsOutput.textContent = 'Checking navigation elements...\n';
        
        try {
            // Check for navigation elements
            const devicesNavItem = document.querySelector('.nav-devices');
            const megaMenu = document.querySelector('.mega-menu');
            const brandItems = document.querySelectorAll('.brand-item');
            const categoriesSection = document.getElementById('categories-section');
            const phonesSection = document.getElementById('phones-section');
            
            elementsOutput.textContent += 'Devices Nav Item: ' + (devicesNavItem ? 'Found' : 'Not Found') + '\n';
            elementsOutput.textContent += 'Mega Menu: ' + (megaMenu ? 'Found' : 'Not Found') + '\n';
            elementsOutput.textContent += 'Brand Items Count: ' + brandItems.length + '\n';
            elementsOutput.textContent += 'Categories Section: ' + (categoriesSection ? 'Found' : 'Not Found') + '\n';
            elementsOutput.textContent += 'Phones Section: ' + (phonesSection ? 'Found' : 'Not Found') + '\n';
            
            if (devicesNavItem) {
                elementsOutput.textContent += 'Devices Nav Item Classes: ' + devicesNavItem.className + '\n';
                elementsOutput.textContent += 'Has "show" class: ' + devicesNavItem.classList.contains('show') + '\n';
            }
            
            if (megaMenu) {
                elementsOutput.textContent += 'Mega Menu Classes: ' + megaMenu.className + '\n';
                elementsOutput.textContent += 'Mega Menu Display: ' + (megaMenu.style.display || 'default') + '\n';
            }
            
            // Check brand items
            if (brandItems.length > 0) {
                elementsOutput.textContent += '\nFirst Brand Item:\n';
                const firstBrand = brandItems[0];
                elementsOutput.textContent += '  Dataset: ' + JSON.stringify(firstBrand.dataset) + '\n';
                elementsOutput.textContent += '  Classes: ' + firstBrand.className + '\n';
            }
            
            elementsOutput.classList.remove('error');
            elementsOutput.classList.add('success');
        } catch (error) {
            elementsOutput.textContent += 'Error: ' + error.message;
            elementsOutput.classList.remove('success');
            elementsOutput.classList.add('error');
            console.error('Elements check error:', error);
        }
    });
    
    // Check event listeners
    document.getElementById('check-events').addEventListener('click', function() {
        eventsOutput.textContent = 'Checking event listeners...\n';
        
        try {
            const devicesNavItem = document.querySelector('.nav-devices');
            const megaMenu = devicesNavItem ? devicesNavItem.querySelector('.mega-menu') : null;
            const brandItems = document.querySelectorAll('.brand-item');
            
            eventsOutput.textContent += 'Devices Nav Item Event Listeners:\n';
            if (devicesNavItem) {
                eventsOutput.textContent += '  mouseenter: ' + (devicesNavItem.onmouseenter ? 'Attached' : 'Not Attached') + '\n';
                eventsOutput.textContent += '  mouseleave: ' + (devicesNavItem.onmouseleave ? 'Attached' : 'Not Attached') + '\n';
            } else {
                eventsOutput.textContent += '  Not found\n';
            }
            
            eventsOutput.textContent += '\nMega Menu Event Listeners:\n';
            if (megaMenu) {
                eventsOutput.textContent += '  mouseenter: ' + (megaMenu.onmouseenter ? 'Attached' : 'Not Attached') + '\n';
                eventsOutput.textContent += '  mouseleave: ' + (megaMenu.onmouseleave ? 'Attached' : 'Not Attached') + '\n';
            } else {
                eventsOutput.textContent += '  Not found\n';
            }
            
            eventsOutput.textContent += '\nBrand Items Event Listeners:\n';
            eventsOutput.textContent += '  Total brand items: ' + brandItems.length + '\n';
            
            if (brandItems.length > 0) {
                const firstBrand = brandItems[0];
                eventsOutput.textContent += '  First brand item:\n';
                eventsOutput.textContent += '    mouseenter: ' + (firstBrand.onmouseenter ? 'Attached' : 'Not Attached') + '\n';
                eventsOutput.textContent += '    click: ' + (firstBrand.onclick ? 'Attached' : 'Not Attached') + '\n';
            }
            
            // Check if required functions exist
            eventsOutput.textContent += '\nJavaScript Functions:\n';
            eventsOutput.textContent += '  loadBrandCategories: ' + (typeof loadBrandCategories !== 'undefined' ? 'Available' : 'Not Available') + '\n';
            eventsOutput.textContent += '  loadCategoryModels: ' + (typeof loadCategoryModels !== 'undefined' ? 'Available' : 'Not Available') + '\n';
            eventsOutput.textContent += '  initSiteWide: ' + (typeof initSiteWide !== 'undefined' ? 'Available' : 'Not Available') + '\n';
            
            eventsOutput.classList.remove('error');
            eventsOutput.classList.add('success');
        } catch (error) {
            eventsOutput.textContent += 'Error: ' + error.message;
            eventsOutput.classList.remove('success');
            eventsOutput.classList.add('error');
            console.error('Events check error:', error);
        }
    });
    
    // Add some manual debugging
    console.log('Comprehensive Navigation Debug Page Loaded');
    console.log('Checking for navigation elements...');
    
    setTimeout(() => {
        const devicesNavItem = document.querySelector('.nav-devices');
        const brandItems = document.querySelectorAll('.brand-item');
        
        console.log('Devices Nav Item:', devicesNavItem);
        console.log('Brand Items Count:', brandItems.length);
        
        if (brandItems.length > 0) {
            console.log('First Brand Item Dataset:', brandItems[0].dataset);
        }
    }, 1000);
});
</script>

<?php include 'inc/modern-footer.php'; ?>
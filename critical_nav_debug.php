<?php
$pageTitle = "Critical Navigation Debug - Gayar Plus";
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
    <h1>Critical Navigation Debug</h1>
    
    <div class="debug-section">
        <h2>Immediate Test</h2>
        <button id="critical-test" class="debug-button">Run Critical Test</button>
        <div id="critical-output" class="debug-output"></div>
    </div>
    
    <div class="debug-section">
        <h2>Manual Instructions</h2>
        <p>1. Open browser console (F12)</p>
        <p>2. Type this in console and press Enter:</p>
        <div class="debug-output">
typeof loadBrandCategories
        </div>
        <p>3. If it says "function", type this and press Enter:</p>
        <div class="debug-output">
loadBrandCategories(1)
        </div>
        <p>4. Check what appears in the categories section</p>
    </div>
</div>

<script>
// Critical debugging - run immediately
console.log('=== CRITICAL NAVIGATION DEBUG ===');
console.log('Page loaded at:', new Date().toLocaleTimeString());

// Check if DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded');
        runCriticalDebug();
    });
} else {
    console.log('DOM already ready');
    runCriticalDebug();
}

function runCriticalDebug() {
    console.log('=== Running Critical Debug ===');
    
    // Check for all required elements
    const devicesNavItem = document.querySelector('.nav-devices');
    const megaMenu = document.querySelector('.mega-menu');
    const brandItems = document.querySelectorAll('.brand-item');
    const categoriesSection = document.getElementById('categories-section');
    const phonesSection = document.getElementById('phones-section');
    
    console.log('Devices Nav Item:', devicesNavItem);
    console.log('Mega Menu:', megaMenu);
    console.log('Brand Items Count:', brandItems.length);
    console.log('Categories Section:', categoriesSection);
    console.log('Phones Section:', phonesSection);
    
    // Check if functions exist
    console.log('loadBrandCategories function:', typeof loadBrandCategories);
    console.log('loadCategoryModels function:', typeof loadCategoryModels);
    
    // If brand items exist, manually attach a test event
    if (brandItems.length > 0) {
        console.log('Attaching manual test event to first brand item');
        const firstBrand = brandItems[0];
        console.log('First brand dataset:', firstBrand.dataset);
        
        // Remove any existing events to prevent conflicts
        const clone = firstBrand.cloneNode(true);
        firstBrand.parentNode.replaceChild(clone, firstBrand);
        
        // Add our own test event
        clone.addEventListener('mouseenter', function(e) {
            e.preventDefault();
            console.log('=== MANUAL TEST TRIGGERED ===');
            console.log('Brand ID:', this.dataset.brand);
            
            // Try to call the function directly
            if (typeof loadBrandCategories === 'function') {
                console.log('Calling loadBrandCategories directly');
                loadBrandCategories(this.dataset.brand);
            } else {
                console.log('ERROR: loadBrandCategories is not a function');
            }
        });
        
        console.log('Manual test event attached');
    }
}

// Button test
document.addEventListener('DOMContentLoaded', function() {
    const output = document.getElementById('critical-output');
    
    document.getElementById('critical-test').addEventListener('click', function() {
        output.textContent = 'Running critical test...\n';
        
        try {
            // Check all elements
            const devicesNavItem = document.querySelector('.nav-devices');
            const megaMenu = document.querySelector('.mega-menu');
            const brandItems = document.querySelectorAll('.brand-item');
            const categoriesSection = document.getElementById('categories-section');
            const phonesSection = document.getElementById('phones-section');
            
            output.textContent += '=== ELEMENT CHECK ===\n';
            output.textContent += 'Devices Nav Item: ' + (devicesNavItem ? 'FOUND' : 'MISSING') + '\n';
            output.textContent += 'Mega Menu: ' + (megaMenu ? 'FOUND' : 'MISSING') + '\n';
            output.textContent += 'Brand Items: ' + brandItems.length + ' found\n';
            output.textContent += 'Categories Section: ' + (categoriesSection ? 'FOUND' : 'MISSING') + '\n';
            output.textContent += 'Phones Section: ' + (phonesSection ? 'FOUND' : 'MISSING') + '\n';
            
            // Check functions
            output.textContent += '\n=== FUNCTION CHECK ===\n';
            output.textContent += 'loadBrandCategories: ' + (typeof loadBrandCategories) + '\n';
            output.textContent += 'loadCategoryModels: ' + (typeof loadCategoryModels) + '\n';
            
            // Try direct function call
            output.textContent += '\n=== DIRECT FUNCTION TEST ===\n';
            if (typeof loadBrandCategories === 'function' && brandItems.length > 0) {
                const brandId = brandItems[0].dataset.brand;
                output.textContent += 'Testing with brand ID: ' + brandId + '\n';
                output.textContent += 'Calling loadBrandCategories(' + brandId + ')\n';
                
                // Show that we're trying
                if (categoriesSection) {
                    categoriesSection.innerHTML = '<div class="menu-item">Testing direct call...</div>';
                }
                
                // Try the call
                try {
                    loadBrandCategories(brandId);
                    output.textContent += 'Function call completed\n';
                } catch (e) {
                    output.textContent += 'ERROR: ' + e.message + '\n';
                }
            } else {
                output.textContent += 'Cannot test - function or brand items missing\n';
            }
            
            output.classList.remove('error');
            output.classList.add('success');
        } catch (error) {
            output.textContent += 'CRITICAL ERROR: ' + error.message;
            output.classList.remove('success');
            output.classList.add('error');
            console.error('Critical test error:', error);
        }
    });
});
</script>

<?php include 'inc/modern-footer.php'; ?>
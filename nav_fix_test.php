<?php
$pageTitle = "Navigation Fix Test - Gayar Plus";
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

.warning {
    background: #fffbeb;
    border: 1px solid #f6e05e;
    color: #d69e2e;
}
</style>

<div class="test-container">
    <h1>Navigation Fix Test</h1>
    
    <div class="test-section">
        <h2>Problem Summary</h2>
        <p>The device menu (dropdown navigation) was working on the homepage but not on other pages throughout the website. This was caused by JavaScript conflicts between site-wide.js and topBarNav.php.</p>
    </div>
    
    <div class="test-section">
        <h2>Fixes Applied</h2>
        <ul>
            <li>Fixed "querySelector" error by properly handling empty href attributes</li>
            <li>Removed conflicting JavaScript functions from site-wide.js</li>
            <li>Ensured proper initialization order</li>
            <li>Increased z-index for mega menu visibility</li>
        </ul>
    </div>
    
    <div class="test-section">
        <h2>Automated Tests</h2>
        <button id="test-elements" class="test-button">Test Navigation Elements</button>
        <button id="test-functions" class="test-button">Test JavaScript Functions</button>
        <button id="test-hover" class="test-button">Test Hover Functionality</button>
        <button id="test-ajax" class="test-button">Test AJAX Endpoints</button>
        <div id="test-result" class="result" style="display: none;"></div>
    </div>
    
    <div class="test-section">
        <h2>Manual Test</h2>
        <p>Try hovering over the "الأجهزة" menu item in the navigation bar above. The dropdown menu should appear with brand categories.</p>
        <p>If this works, the navigation issue has been successfully resolved.</p>
    </div>
    
    <div class="test-section">
        <h2>Console Debugging</h2>
        <p>Open the browser console and try these commands:</p>
        <div class="result">
// Test AJAX directly:
fetch('./ajax/get_brand_categories.php?brand_id=1').then(r => r.json()).then(console.log)

// Try calling function directly:
if (typeof loadBrandCategories === 'function') {
    console.log('loadBrandCategories function is available');
} else {
    console.log('Function not available');
}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const resultDiv = document.getElementById('test-result');
    
    // Test navigation elements
    document.getElementById('test-elements').addEventListener('click', function() {
        resultDiv.style.display = 'block';
        resultDiv.className = 'result';
        
        try {
            const devicesNavItem = document.querySelector('.nav-devices');
            const megaMenu = document.querySelector('.mega-menu');
            const brandItems = document.querySelectorAll('.brand-item');
            
            let result = 'Navigation Elements Check:\n';
            result += 'Devices Nav Item: ' + (devicesNavItem ? 'Found' : 'Not Found') + '\n';
            result += 'Mega Menu: ' + (megaMenu ? 'Found' : 'Not Found') + '\n';
            result += 'Brand Items Count: ' + brandItems.length + '\n';
            
            if (devicesNavItem && megaMenu && brandItems.length > 0) {
                resultDiv.className = 'result success';
                result += '\n✓ All navigation elements found!';
            } else {
                resultDiv.className = 'result error';
                result += '\n✗ Some navigation elements are missing!';
            }
            
            resultDiv.textContent = result;
        } catch (error) {
            resultDiv.className = 'result error';
            resultDiv.textContent = 'ERROR: ' + error.message;
            console.error('Elements test error:', error);
        }
    });
    
    // Test JavaScript functions
    document.getElementById('test-functions').addEventListener('click', function() {
        resultDiv.style.display = 'block';
        resultDiv.className = 'result';
        
        try {
            let result = 'JavaScript Functions Check:\n';
            result += 'loadBrandCategories: ' + (typeof loadBrandCategories !== 'undefined' ? 'Available' : 'Not Available') + '\n';
            result += 'loadCategoryModels: ' + (typeof loadCategoryModels !== 'undefined' ? 'Available' : 'Not Available') + '\n';
            result += 'initTopBarNav: ' + (typeof initTopBarNav !== 'undefined' ? 'Available' : 'Not Available') + '\n';
            result += 'initSiteWide: ' + (typeof initSiteWide !== 'undefined' ? 'Available' : 'Not Available') + '\n';
            
            // Count available functions
            const functions = [
                typeof loadBrandCategories !== 'undefined',
                typeof loadCategoryModels !== 'undefined',
                typeof initTopBarNav !== 'undefined'
            ];
            
            const availableCount = functions.filter(Boolean).length;
            
            if (availableCount >= 2) {
                resultDiv.className = 'result success';
                result += '\n✓ Required JavaScript functions are available!';
            } else {
                resultDiv.className = 'result warning';
                result += '\n⚠ Some JavaScript functions may be missing!';
            }
            
            resultDiv.textContent = result;
        } catch (error) {
            resultDiv.className = 'result error';
            resultDiv.textContent = 'ERROR: ' + error.message;
            console.error('Functions test error:', error);
        }
    });
    
    // Test hover functionality
    document.getElementById('test-hover').addEventListener('click', function() {
        resultDiv.style.display = 'block';
        resultDiv.className = 'result';
        
        try {
            const devicesNavItem = document.querySelector('.nav-devices');
            const megaMenu = document.querySelector('.mega-menu');
            
            if (!devicesNavItem || !megaMenu) {
                resultDiv.className = 'result error';
                resultDiv.textContent = 'ERROR: Navigation elements not found!';
                return;
            }
            
            // Simulate hover
            devicesNavItem.classList.add('show');
            
            // Check if menu is visible
            setTimeout(() => {
                const isVisible = megaMenu.offsetParent !== null;
                
                let result = 'Hover Test:\n';
                result += 'Added "show" class to devices nav item\n';
                result += 'Mega menu is visible: ' + isVisible + '\n';
                
                if (isVisible) {
                    resultDiv.className = 'result success';
                    result += '\n✓ Hover functionality is working!';
                } else {
                    resultDiv.className = 'result error';
                    result += '\n✗ Hover functionality is not working!';
                }
                
                resultDiv.textContent = result;
                
                // Remove show class
                devicesNavItem.classList.remove('show');
            }, 100);
            
        } catch (error) {
            resultDiv.className = 'result error';
            resultDiv.textContent = 'ERROR: ' + error.message;
            console.error('Hover test error:', error);
        }
    });
    
    // Test AJAX endpoints
    document.getElementById('test-ajax').addEventListener('click', function() {
        resultDiv.style.display = 'block';
        resultDiv.className = 'result';
        
        let result = 'AJAX Endpoints Test:\n';
        let testsCompleted = 0;
        let testsPassed = 0;
        
        // Test brand categories endpoint
        fetch('./ajax/get_brand_categories.php?brand_id=1')
            .then(response => {
                result += 'Brand Categories Response Status: ' + response.status + '\n';
                return response.json();
            })
            .then(data => {
                testsCompleted++;
                result += 'Brand Categories Success: ' + data.success + '\n';
                if (data.categories) {
                    result += 'Categories Count: ' + data.categories.length + '\n';
                    if (data.categories.length > 0) testsPassed++;
                }
                if (data.message) {
                    result += 'Message: ' + data.message + '\n';
                }
                
                if (testsCompleted === 2) {
                    finishAjaxTest(result, testsPassed);
                }
            })
            .catch(error => {
                testsCompleted++;
                result += 'Brand Categories Error: ' + error.message + '\n';
                console.error('Brand categories error:', error);
                
                if (testsCompleted === 2) {
                    finishAjaxTest(result, testsPassed);
                }
            });
            
        // Test category models endpoint
        fetch('./ajax/get_category_models.php?category_id=1')
            .then(response => {
                result += 'Category Models Response Status: ' + response.status + '\n';
                return response.json();
            })
            .then(data => {
                testsCompleted++;
                result += 'Category Models Success: ' + data.success + '\n';
                if (data.models) {
                    result += 'Models Count: ' + data.models.length + '\n';
                    if (data.models.length > 0) testsPassed++;
                }
                if (data.message) {
                    result += 'Message: ' + data.message + '\n';
                }
                
                if (testsCompleted === 2) {
                    finishAjaxTest(result, testsPassed);
                }
            })
            .catch(error => {
                testsCompleted++;
                result += 'Category Models Error: ' + error.message + '\n';
                console.error('Category models error:', error);
                
                if (testsCompleted === 2) {
                    finishAjaxTest(result, testsPassed);
                }
            });
            
        function finishAjaxTest(result, testsPassed) {
            if (testsPassed === 2) {
                resultDiv.className = 'result success';
                result += '\n✓ All AJAX endpoints are working!';
            } else if (testsPassed > 0) {
                resultDiv.className = 'result warning';
                result += '\n⚠ Some AJAX endpoints may have issues!';
            } else {
                resultDiv.className = 'result error';
                result += '\n✗ AJAX endpoints are not working!';
            }
            
            resultDiv.textContent = result;
        }
    });
});
</script>

<?php include 'inc/modern-footer.php'; ?>
<?php
$pageTitle = "Simple Navigation Test - Gayar Plus";
require_once 'config.php';
include 'inc/header.php';
?>

<div style="max-width: 1200px; margin: 2rem auto; padding: 2rem; background: white; border-radius: 16px; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); border: 1px solid #e2e8f0;">
    <h1 style="color: #1e3a8a; margin-bottom: 1rem;">Simple Navigation Test</h1>
    
    <div style="margin-bottom: 2rem;">
        <h2 style="color: #1e3a8a; margin-bottom: 1rem;">Test Instructions</h2>
        <p>Hover over the "الأجهزة" menu item in the navigation bar above. The dropdown menu should appear with brand categories.</p>
        <p>If this works, the navigation issue has been successfully resolved.</p>
    </div>
    
    <div style="margin-bottom: 2rem;">
        <h2 style="color: #1e3a8a; margin-bottom: 1rem;">Debug Information</h2>
        <div id="debug-info" style="background: #f8fafc; padding: 1rem; border-radius: 8px; font-family: monospace;">
            Loading debug information...
        </div>
    </div>
    
    <div style="margin-bottom: 2rem;">
        <h2 style="color: #1e3a8a; margin-bottom: 1rem;">AJAX Test</h2>
        <button id="test-ajax" style="background: #3b82f6; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 600;">Test AJAX Endpoints</button>
        <div id="ajax-result" style="background: #f8fafc; padding: 1rem; border-radius: 8px; font-family: monospace; margin-top: 1rem; display: none;"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const debugInfo = document.getElementById('debug-info');
    
    // Display debug information
    let info = '=== Navigation Debug Info ===\n\n';
    
    info += 'loadBrandCategories function: ' + (typeof loadBrandCategories !== 'undefined' ? 'Available' : 'Not Available') + '\n';
    info += 'loadCategoryModels function: ' + (typeof loadCategoryModels !== 'undefined' ? 'Available' : 'Not Available') + '\n';
    info += 'initTopBarNav function: ' + (typeof initTopBarNav !== 'undefined' ? 'Available' : 'Not Available') + '\n';
    info += 'initSiteWide function: ' + (typeof initSiteWide !== 'undefined' ? 'Available' : 'Not Available') + '\n';
    
    // Check for navigation elements
    const devicesNavItem = document.querySelector('.nav-devices');
    const megaMenu = document.querySelector('.mega-menu');
    const brandItems = document.querySelectorAll('.brand-item');
    
    info += '\nNavigation Elements:\n';
    info += 'Devices Nav Item: ' + (devicesNavItem ? 'Found' : 'Not Found') + '\n';
    info += 'Mega Menu: ' + (megaMenu ? 'Found' : 'Not Found') + '\n';
    info += 'Brand Items: ' + brandItems.length + ' found\n';
    
    debugInfo.textContent = info;
    
    // Test AJAX endpoints
    document.getElementById('test-ajax').addEventListener('click', function() {
        const resultDiv = document.getElementById('ajax-result');
        resultDiv.style.display = 'block';
        resultDiv.textContent = 'Testing AJAX endpoints...\n';
        
        let result = '';
        let testsCompleted = 0;
        
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
                }
                
                if (testsCompleted === 2) {
                    resultDiv.textContent = result;
                }
            })
            .catch(error => {
                testsCompleted++;
                result += 'Brand Categories Error: ' + error.message + '\n';
                
                if (testsCompleted === 2) {
                    resultDiv.textContent = result;
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
                }
                
                if (testsCompleted === 2) {
                    resultDiv.textContent = result;
                }
            })
            .catch(error => {
                testsCompleted++;
                result += 'Category Models Error: ' + error.message + '\n';
                
                if (testsCompleted === 2) {
                    resultDiv.textContent = result;
                }
            });
    });
});
</script>

<?php include 'inc/modern-footer.php'; ?>
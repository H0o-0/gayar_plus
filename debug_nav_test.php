<?php
$pageTitle = "Debug Navigation Test - Gayar Plus";
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
</style>

<div class="debug-container">
    <h1>Debug Navigation Test</h1>
    
    <div class="debug-section">
        <h2>Base URL Check</h2>
        <div class="debug-output">
            Base URL: <?php echo base_url; ?>
            
            Document Root: <?php echo $_SERVER['DOCUMENT_ROOT']; ?>
            
            Script Filename: <?php echo $_SERVER['SCRIPT_FILENAME']; ?>
            
            Request URI: <?php echo $_SERVER['REQUEST_URI']; ?>
        </div>
    </div>
    
    <div class="debug-section">
        <h2>AJAX Endpoint Test</h2>
        <button id="test-brands">Test Brand Categories AJAX</button>
        <div id="brands-output" class="debug-output"></div>
    </div>
    
    <div class="debug-section">
        <h2>Navigation Elements Check</h2>
        <button id="check-elements">Check Navigation Elements</button>
        <div id="elements-output" class="debug-output"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test AJAX endpoint
    document.getElementById('test-brands').addEventListener('click', function() {
        const output = document.getElementById('brands-output');
        output.textContent = 'Testing AJAX call...';
        
        // Try to fetch brand categories for brand ID 1
        fetch('./ajax/get_brand_categories.php?brand_id=1')
            .then(response => {
                output.textContent += '\nResponse status: ' + response.status;
                output.textContent += '\nResponse URL: ' + response.url;
                return response.text();
            })
            .then(data => {
                output.textContent += '\nResponse data:\n' + data;
            })
            .catch(error => {
                output.textContent += '\nError: ' + error.message;
            });
    });
    
    // Check navigation elements
    document.getElementById('check-elements').addEventListener('click', function() {
        const output = document.getElementById('elements-output');
        
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
});
</script>

<?php include 'inc/modern-footer.php'; ?>
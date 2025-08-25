<?php
$pageTitle = "Simple Navigation Test - Gayar Plus";
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
</style>

<div class="test-container">
    <h1>Simple Navigation Test</h1>
    
    <div class="test-section">
        <h2>Test Instructions</h2>
        <p>1. Try hovering over the "الأجهزة" menu item in the navigation bar above</p>
        <p>2. If the dropdown menu appears, the navigation is working correctly</p>
        <p>3. If not, click the button below to run automated tests</p>
    </div>
    
    <div class="test-section">
        <h2>Automated Test</h2>
        <button id="run-test" class="test-button">Run Navigation Test</button>
        <div id="test-result" class="result" style="display: none;"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('run-test').addEventListener('click', function() {
        const resultDiv = document.getElementById('test-result');
        resultDiv.style.display = 'block';
        
        try {
            // Check for navigation elements
            const devicesNavItem = document.querySelector('.nav-devices');
            const megaMenu = document.querySelector('.mega-menu');
            const brandItems = document.querySelectorAll('.brand-item');
            
            if (!devicesNavItem) {
                resultDiv.className = 'result error';
                resultDiv.textContent = 'ERROR: Navigation devices item not found!';
                return;
            }
            
            if (!megaMenu) {
                resultDiv.className = 'result error';
                resultDiv.textContent = 'ERROR: Mega menu not found!';
                return;
            }
            
            if (brandItems.length === 0) {
                resultDiv.className = 'result error';
                resultDiv.textContent = 'ERROR: No brand items found!';
                return;
            }
            
            // Test hover functionality
            devicesNavItem.classList.add('show');
            
            // Check if menu is visible
            setTimeout(() => {
                const isVisible = megaMenu.offsetParent !== null;
                
                if (isVisible) {
                    resultDiv.className = 'result success';
                    resultDiv.textContent = 'SUCCESS: Navigation is working correctly! The mega menu is visible.';
                } else {
                    resultDiv.className = 'result error';
                    resultDiv.textContent = 'ERROR: Navigation elements found but menu is not visible!';
                }
                
                // Remove show class
                devicesNavItem.classList.remove('show');
            }, 100);
            
        } catch (error) {
            resultDiv.className = 'result error';
            resultDiv.textContent = 'ERROR: ' + error.message;
            console.error('Test error:', error);
        }
    });
});
</script>

<?php include 'inc/modern-footer.php'; ?>
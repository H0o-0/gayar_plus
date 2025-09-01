<?php
$pageTitle = "Device Menu Test";
require_once 'initialize.php';
include 'inc/header.php';
?>

<style>
/* Simple test styles */
.test-container {
    padding: 20px;
    max-width: 800px;
    margin: 0 auto;
}

.test-section {
    margin: 20px 0;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.test-button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.test-button:hover {
    background-color: #0056b3;
}

#result {
    margin-top: 20px;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 5px;
}
</style>

<div class="test-container">
    <h1>Device Menu Test</h1>
    
    <div class="test-section">
        <h2>Test Brand Categories</h2>
        <button class="test-button" onclick="testBrandCategories()">Test Brand Categories (Brand ID: 1)</button>
        <div id="result"></div>
    </div>
</div>

<script>
function testBrandCategories() {
    const brandId = 1; // Test with brand ID 1
    const baseUrl = typeof _base_url_ !== 'undefined' ? _base_url_ : '/';
    const ajaxUrl = baseUrl + 'ajax/get_brand_categories.php?brand_id=' + brandId;
    
    console.log('Testing AJAX URL:', ajaxUrl);
    
    document.getElementById('result').innerHTML = '<p>Loading...</p>';
    
    fetch(ajaxUrl)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('result').innerHTML = '<p>Error: ' + error.message + '</p>';
        });
}
</script>

<?php include 'inc/modern-footer.php'; ?>
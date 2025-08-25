<?php
require_once 'initialize.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple AJAX Test</title>
    <script>
        var _base_url_ = '<?php echo base_url; ?>';
    </script>
</head>
<body>
    <h1>Simple AJAX Test</h1>
    
    <div>
        <h2>Test Brand Categories</h2>
        <button onclick="testBrandCategories()">Test with Brand ID 1</button>
        <div id="brand-categories-result" style="margin-top: 10px; padding: 10px; border: 1px solid #ccc;"></div>
    </div>
    
    <div style="margin-top: 20px;">
        <h2>Test Category Models</h2>
        <button onclick="testCategoryModels()">Test with Category ID 1</button>
        <div id="category-models-result" style="margin-top: 10px; padding: 10px; border: 1px solid #ccc;"></div>
    </div>
    
    <script>
        function testBrandCategories() {
            const brandId = 1;
            const baseUrl = typeof _base_url_ !== 'undefined' ? _base_url_ : '/';
            const ajaxUrl = baseUrl + 'ajax/get_brand_categories.php?brand_id=' + brandId;
            
            document.getElementById('brand-categories-result').innerHTML = '<p>Loading...</p>';
            
            fetch(ajaxUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('brand-categories-result').innerHTML = 
                        '<h3>Response:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
                })
                .catch(error => {
                    document.getElementById('brand-categories-result').innerHTML = 
                        '<p style="color: red;">Error: ' + error.message + '</p>';
                    console.error('Error:', error);
                });
        }
        
        function testCategoryModels() {
            const categoryId = 1;
            const baseUrl = typeof _base_url_ !== 'undefined' ? _base_url_ : '/';
            const ajaxUrl = baseUrl + 'ajax/get_category_models.php?category_id=' + categoryId;
            
            document.getElementById('category-models-result').innerHTML = '<p>Loading...</p>';
            
            fetch(ajaxUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('category-models-result').innerHTML = 
                        '<h3>Response:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
                })
                .catch(error => {
                    document.getElementById('category-models-result').innerHTML = 
                        '<p style="color: red;">Error: ' + error.message + '</p>';
                    console.error('Error:', error);
                });
        }
    </script>
</body>
</html>
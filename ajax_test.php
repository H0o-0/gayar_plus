<?php
require_once 'initialize.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>AJAX Test</title>
    <script>
        var _base_url_ = '<?php echo base_url; ?>';
    </script>
</head>
<body>
    <h1>AJAX Test</h1>
    
    <button onclick="testBrandCategories()">Test Brand Categories</button>
    <div id="result"></div>
    
    <script>
        function testBrandCategories() {
            const brandId = 1; // Test with brand ID 1
            const baseUrl = typeof _base_url_ !== 'undefined' ? _base_url_ : '/';
            const ajaxUrl = baseUrl + 'ajax/get_brand_categories.php?brand_id=' + brandId;
            
            console.log('Testing AJAX URL:', ajaxUrl);
            
            fetch(ajaxUrl)
                .then(response => {
                    console.log('Response status:', response.status);
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
</body>
</html>
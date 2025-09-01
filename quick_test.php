<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Test - Gayar Plus</title>
</head>
<body>

<?php
require_once 'config.php';

echo "<h2>🛒 Quick Cart Test</h2>";

// Test Arabic text encoding
echo "<h3>📝 Arabic Text Test:</h3>";
echo "<p>النص العربي: مرحباً بكم في متجر Gayar Plus</p>";

// Test database connection
echo "<h3>📊 Database Status:</h3>";
if ($conn) {
    echo "✅ Database connected successfully<br>";
    
    // Check products
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 1");
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "📦 Active products: $count<br>";
        
        // Get first 3 products
        $products = $conn->query("SELECT id, product_name FROM products WHERE status = 1 LIMIT 3");
        if ($products && $products->num_rows > 0) {
            echo "<h3>🎯 Test Products:</h3>";
            while ($product = $products->fetch_assoc()) {
                echo "<div style='margin: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 8px;'>";
                echo "<strong>ID: {$product['id']}</strong> - {$product['product_name']}<br>";
                echo "<button onclick='testAddToCart({$product['id']})' style='background: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 5px;'>🛒 Test Add to Cart</button>";
                echo "</div>";
            }
        }
    }
} else {
    echo "❌ Database connection failed<br>";
}

// Test add_to_cart.php endpoint
echo "<h3>🧪 Endpoint Test:</h3>";
echo "<button onclick='testEndpoint()' style='background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;'>Test add_to_cart.php</button>";

echo "<div id='test-result' style='margin-top: 20px; padding: 15px; background: #f3f4f6; border-radius: 8px; display: none;'></div>";
?>

<script>
async function testAddToCart(productId) {
    const resultDiv = document.getElementById('test-result');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<p style="color: #f59e0b;">🔄 Testing product ID ' + productId + '...</p>';
    
    try {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', 1);
        
        const response = await fetch('ajax/add_to_cart.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            resultDiv.innerHTML = `
                <h4 style="color: #10b981;">✅ Success!</h4>
                <p><strong>Message:</strong> ${data.message}</p>
                <p><strong>Product:</strong> ${data.product_name}</p>
                <p><strong>Cart Count:</strong> ${data.cart_count}</p>
            `;
        } else {
            resultDiv.innerHTML = `<h4 style="color: #ef4444;">❌ Failed</h4><p>${data.message}</p>`;
        }
    } catch (error) {
        resultDiv.innerHTML = `<h4 style="color: #ef4444;">❌ Error</h4><p>${error.message}</p>`;
    }
}

async function testEndpoint() {
    const resultDiv = document.getElementById('test-result');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<p style="color: #f59e0b;">🔄 Testing endpoint directly...</p>';
    
    try {
        const response = await fetch('ajax/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=1&quantity=1'
        });
        
        const text = await response.text();
        console.log('Response text:', text);
        
        const data = JSON.parse(text);
        
        if (data.success) {
            resultDiv.innerHTML = `
                <h4 style="color: #10b981;">✅ Endpoint works!</h4>
                <p><strong>Response:</strong> ${JSON.stringify(data, null, 2)}</p>
            `;
        } else {
            resultDiv.innerHTML = `<h4 style="color: #ef4444;">❌ Endpoint failed</h4><p>${data.message}</p>`;
        }
    } catch (error) {
        resultDiv.innerHTML = `<h4 style="color: #ef4444;">❌ Endpoint error</h4><p>${error.message}</p>`;
    }
}
</script>

<style>
body { font-family: Arial, sans-serif; padding: 20px; line-height: 1.6; }
h2 { color: #1f2937; }
h3 { color: #374151; margin-top: 20px; }
</style>

</body>
</html>
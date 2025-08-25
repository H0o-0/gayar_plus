<?php
require_once 'initialize.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Brand Items Test</title>
    <style>
        .brand-item {
            padding: 10px;
            margin: 5px;
            background-color: #f0f0f0;
            cursor: pointer;
        }
        .brand-item:hover {
            background-color: #e0e0e0;
        }
        .brand-item.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Brand Items Test</h1>
    
    <div id="brands-container">
        <?php
        // Load brands from database
        if (isset($conn)) {
            $brands_query = "SELECT id, name FROM brands WHERE status = 1 ORDER BY name ASC LIMIT 8";
            $brands_result = $conn->query($brands_query);
            
            if ($brands_result && $brands_result->num_rows > 0) {
                while ($brand = $brands_result->fetch_assoc()) {
                    echo '<div class="brand-item" data-brand="' . $brand['id'] . '">';
                    echo '<i class="fas fa-mobile brand-icon"></i>';
                    echo htmlspecialchars($brand['name']);
                    echo '</div>';
                }
            } else {
                echo '<div class="menu-item">لا توجد شركات</div>';
            }
        } else {
            echo '<div class="menu-item">خطأ في الاتصال بقاعدة البيانات</div>';
        }
        ?>
    </div>
    
    <div id="categories-section">
        <div class="menu-item">اختر شركة أولاً</div>
    </div>
    
    <div id="phones-section">
        <div class="menu-item">اختر فئة لعرض الموديلات</div>
    </div>
    
    <script>
        // Test the brand item selection
        document.addEventListener('DOMContentLoaded', function() {
            const brandItems = document.querySelectorAll('.brand-item');
            console.log('Found brand items:', brandItems.length);
            
            brandItems.forEach(item => {
                item.addEventListener('mouseenter', (e) => {
                    const brandId = item.dataset.brand;
                    console.log('Brand item hovered:', brandId);
                    
                    // Remove active class from all items
                    document.querySelectorAll('.brand-item').forEach(i => i.classList.remove('active'));
                    
                    // Add active class to hovered item
                    item.classList.add('active');
                    
                    // Load categories for this brand
                    loadBrandCategories(brandId);
                });
            });
        });
        
        function loadBrandCategories(brandId) {
            const categoriesSection = document.getElementById('categories-section');
            if (!categoriesSection) {
                console.log('Categories section not found');
                return;
            }
            
            // Show loading
            categoriesSection.innerHTML = '<div class="menu-item">جاري التحميل...</div>';
            console.log('Loading categories for brand:', brandId);
            
            // Use absolute path based on base URL to ensure it works on all pages
            const baseUrl = 'http://localhost/gayar_plus/';
            const ajaxUrl = baseUrl + 'ajax/get_brand_categories.php?brand_id=' + brandId;
            console.log('AJAX URL for categories:', ajaxUrl);
            
            // Fetch categories
            fetch(ajaxUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Brand categories response:', data);
                    if (data.success && data.categories.length > 0) {
                        categoriesSection.innerHTML = '';
                        data.categories.forEach(category => {
                            const element = document.createElement('div');
                            element.className = 'menu-item category-item';
                            element.textContent = category.name;
                            element.dataset.categoryId = category.id;
                            
                            categoriesSection.appendChild(element);
                        });
                    } else {
                        categoriesSection.innerHTML = '<div class="menu-item">لا توجد فئات</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading brand categories:', error);
                    categoriesSection.innerHTML = '<div class="menu-item">حدث خطأ في التحميل</div>';
                });
        }
    </script>
</body>
</html>
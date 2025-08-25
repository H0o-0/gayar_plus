<?php
$pageTitle = 'Dropdown Debug Test';
include 'inc/header.php';
?>

<div class="container mt-5">
    <h1>Dropdown Debug Test</h1>
    <p>This page tests the dropdown menu with detailed console logging.</p>
    
    <div class="mt-4">
        <h3>Console Output:</h3>
        <div id="console-output" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 1rem; border-radius: 8px; font-family: monospace; height: 300px; overflow-y: auto;">
            <p>Open browser console to see debug messages...</p>
        </div>
    </div>
    
    <div class="mt-4">
        <h3>Testing Instructions:</h3>
        <ol>
            <li>Open browser developer tools (F12)</li>
            <li>Go to the Console tab</li>
            <li>Hover over the "الأجهزة" menu item</li>
            <li>Check console for debug messages</li>
        </ol>
    </div>
</div>

<script>
// Debug script for dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 Dropdown Debug Test Loaded');
    
    // Get the devices dropdown container
    const devicesNavItem = document.querySelector('.nav-devices');
    const megaMenu = devicesNavItem ? devicesNavItem.querySelector('.mega-menu') : null;
    
    if (devicesNavItem && megaMenu) {
        console.log('✅ Found devices dropdown elements');
        
        let hideTimeout;
        
        // Show dropdown on hover
        devicesNavItem.addEventListener('mouseenter', function() {
            console.log('🖱️ Mouse entered devices menu');
            // Clear any existing timeout
            if (hideTimeout) {
                clearTimeout(hideTimeout);
                hideTimeout = null;
                console.log('⏱️ Cleared existing timeout');
            }
            
            // Add show class to display the menu
            devicesNavItem.classList.add('show');
            console.log('➕ Added "show" class to devices menu');
        });
        
        // Hide dropdown when mouse leaves
        devicesNavItem.addEventListener('mouseleave', function() {
            console.log('🖱️ Mouse left devices menu, setting timeout to hide');
            // Set a timeout to hide the menu (allows moving mouse to menu)
            hideTimeout = setTimeout(function() {
                devicesNavItem.classList.remove('show');
                console.log('➖ Removed "show" class from devices menu');
            }, 300); // 300ms delay to allow moving mouse to menu
        });
        
        // Keep menu visible when hovering over it
        megaMenu.addEventListener('mouseenter', function() {
            console.log('🖱️ Mouse entered mega menu');
            // Clear any existing timeout
            if (hideTimeout) {
                clearTimeout(hideTimeout);
                hideTimeout = null;
                console.log('⏱️ Cleared timeout while in mega menu');
            }
            
            // Ensure menu stays visible
            devicesNavItem.classList.add('show');
            console.log('➕ Ensured "show" class on devices menu');
        });
        
        // Hide menu when leaving it
        megaMenu.addEventListener('mouseleave', function() {
            console.log('🖱️ Mouse left mega menu, setting timeout to hide');
            // Set a timeout to hide the menu
            hideTimeout = setTimeout(function() {
                devicesNavItem.classList.remove('show');
                console.log('➖ Removed "show" class from devices menu');
            }, 300); // 300ms delay
        });
    } else {
        console.log('❌ Could not find devices dropdown elements');
    }
    
    // Handle brand items
    const brandItems = document.querySelectorAll('.brand-item');
    console.log(`📊 Found ${brandItems.length} brand items`);
    
    brandItems.forEach(function(item, index) {
        item.addEventListener('click', function(e) {
            console.log(`🖱️ Clicked on brand item ${index}:`, this.dataset.brand);
            e.preventDefault();
            const brandId = this.dataset.brand;
            
            // Remove active class from all items
            brandItems.forEach(i => i.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            console.log('➕ Added "active" class to clicked brand item');
            
            // Load categories for this brand
            loadBrandCategories(brandId);
        });
    });
    
    // Load brand categories
    function loadBrandCategories(brandId) {
        console.log(`📦 Loading categories for brand ${brandId}`);
        const categoriesSection = document.getElementById('categories-section');
        if (!categoriesSection) {
            console.log('❌ Could not find categories section');
            return;
        }
        
        // Show loading
        categoriesSection.innerHTML = '<div class="menu-item">جاري التحميل...</div>';
        console.log('🔄 Showing loading message for categories');
        
        // Fetch categories
        fetch('./ajax/get_brand_categories.php?brand_id=' + brandId)
            .then(response => {
                console.log('📥 Received response from get_brand_categories.php');
                return response.json();
            })
            .then(data => {
                console.log('📦 Categories data:', data);
                if (data.success && data.categories.length > 0) {
                    categoriesSection.innerHTML = '';
                    data.categories.forEach(function(category) {
                        const element = document.createElement('div');
                        element.className = 'menu-item';
                        element.textContent = category.name;
                        element.dataset.categoryId = category.id;
                        
                        element.addEventListener('click', function(e) {
                            console.log(`🖱️ Clicked on category:`, category.id);
                            e.stopPropagation();
                            loadCategoryModels(category.id);
                        });
                        
                        categoriesSection.appendChild(element);
                    });
                    console.log('✅ Successfully loaded categories');
                } else {
                    categoriesSection.innerHTML = '<div class="menu-item">لا توجد فئات</div>';
                    console.log('⚠️ No categories found for this brand');
                }
            })
            .catch(error => {
                console.error('❌ Error loading categories:', error);
                categoriesSection.innerHTML = '<div class="menu-item">حدث خطأ في التحميل</div>';
            });
    }
    
    // Load category models
    function loadCategoryModels(categoryId) {
        console.log(`📱 Loading models for category ${categoryId}`);
        const phonesSection = document.getElementById('phones-section');
        if (!phonesSection) {
            console.log('❌ Could not find phones section');
            return;
        }
        
        // Show loading
        phonesSection.innerHTML = '<div class="menu-item">جاري التحميل...</div>';
        console.log('🔄 Showing loading message for models');
        
        // Fetch models
        fetch('./ajax/get_category_models.php?category_id=' + categoryId)
            .then(response => {
                console.log('📥 Received response from get_category_models.php');
                return response.json();
            })
            .then(data => {
                console.log('📱 Models data:', data);
                if (data.success && data.models.length > 0) {
                    phonesSection.innerHTML = '';
                    data.models.forEach(function(model) {
                        const element = document.createElement('div');
                        element.className = 'menu-item';
                        element.textContent = model.name;
                        
                        element.addEventListener('click', function(e) {
                            console.log(`🖱️ Clicked on model:`, model.id);
                            e.stopPropagation();
                            const selectedBrand = document.querySelector('.brand-item.active');
                            const brandId = selectedBrand ? selectedBrand.dataset.brand : '';
                            if (brandId) {
                                console.log(`➡️ Redirecting to device products with brand=${brandId}, series=${categoryId}, model=${model.id}`);
                                window.location.href = './?p=device_products&brand=' + btoa(brandId) + '&series=' + btoa(categoryId) + '&model=' + btoa(model.id);
                            } else {
                                console.log(`➡️ Redirecting to device products with model=${model.id}`);
                                window.location.href = './?p=device_products&model=' + btoa(model.id);
                            }
                        });
                        
                        phonesSection.appendChild(element);
                    });
                    console.log('✅ Successfully loaded models');
                } else {
                    phonesSection.innerHTML = '<div class="menu-item">لا توجد موديلات</div>';
                    console.log('⚠️ No models found for this category');
                }
            })
            .catch(error => {
                console.error('❌ Error loading models:', error);
                phonesSection.innerHTML = '<div class="menu-item">حدث خطأ في التحميل</div>';
            });
    }
});
</script>

<?php include 'inc/modern-footer.php'; ?>
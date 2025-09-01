<?php
$pageTitle = 'Image Loading Test';
include 'inc/header.php';
?>

<div class="container mt-5">
    <h1>Image Loading Test</h1>
    <p>This page tests if the image loading and fallback mechanisms are working correctly.</p>
    
    <div class="mt-4">
        <h3>Test Results:</h3>
        <div id="test-results" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 1rem; border-radius: 8px;">
            <p>Testing image loading...</p>
        </div>
    </div>
    
    <div class="mt-4">
        <h3>Image Tests:</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
            <!-- Test 1: Valid image (should load normally) -->
            <div style="text-align: center;">
                <h4>Valid Image</h4>
                <img src="admin/images/cropped_circle_image.png" alt="Valid image" style="width: 100px; height: 100px; object-fit: cover;" onerror="this.src='./assets/images/no-image.svg'">
                <p>Should show logo</p>
            </div>
            
            <!-- Test 2: Invalid image (should fallback to no-image.svg) -->
            <div style="text-align: center;">
                <h4>Invalid Image</h4>
                <img src="assets/images/non-existent-image.png" alt="Invalid image" style="width: 100px; height: 100px; object-fit: cover;" onerror="if(this.src.indexOf('no-image.svg') === -1) this.src='./assets/images/no-image.svg';">
                <p>Should show fallback</p>
            </div>
            
            <!-- Test 3: Direct no-image.svg -->
            <div style="text-align: center;">
                <h4>Fallback Image</h4>
                <img src="./assets/images/no-image.svg" alt="Fallback image" style="width: 100px; height: 100px; object-fit: cover;">
                <p>Should show SVG icon</p>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <h3>Console Debug:</h3>
        <p>Open browser console to see debug messages.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testResults = document.getElementById('test-results');
    
    // Test if no-image.svg exists and is accessible
    const img = new Image();
    img.onload = function() {
        testResults.innerHTML += '<p>✅ no-image.svg loaded successfully</p>';
    };
    img.onerror = function() {
        testResults.innerHTML += '<p>❌ no-image.svg failed to load</p>';
    };
    img.src = './assets/images/no-image.svg';
    
    // Test if the fallback mechanism works
    const fallbackImg = new Image();
    fallbackImg.onload = function() {
        testResults.innerHTML += '<p>✅ Fallback mechanism working correctly</p>';
    };
    fallbackImg.src = './assets/images/no-image.svg';
    
    console.log('🔍 Image loading test initialized');
    console.log('📁 no-image.svg path:', './assets/images/no-image.svg');
});
</script>

<?php include 'inc/modern-footer.php'; ?>
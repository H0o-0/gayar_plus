<?php
$pageTitle = 'Infinite Loop Test';
include 'inc/header.php';
?>

<div class="container mt-5">
    <h1>Infinite Loop Test</h1>
    <p>This page tests if there are any infinite loops in the image loading mechanism.</p>
    
    <div class="mt-4">
        <h3>Test Results:</h3>
        <div id="test-results" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 1rem; border-radius: 8px;">
            <p>Testing for infinite loops...</p>
        </div>
    </div>
    
    <div class="mt-4">
        <h3>Image Loading Tests:</h3>
        <div id="image-tests" style="display: flex; flex-wrap: wrap; gap: 20px;">
            <!-- We'll add test images dynamically -->
        </div>
    </div>
    
    <div class="mt-4">
        <h3>Loop Detection:</h3>
        <div id="loop-detection" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 1rem; border-radius: 8px;">
            <p>Monitoring for potential infinite loops...</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testResults = document.getElementById('test-results');
    const imageTests = document.getElementById('image-tests');
    const loopDetection = document.getElementById('loop-detection');
    
    // Track image loading attempts to detect loops
    const imageLoadAttempts = {};
    const maxAttempts = 3; // Maximum attempts before considering it a loop
    
    // Function to safely load an image with loop detection
    function safeLoadImage(src, alt, container) {
        const img = document.createElement('img');
        img.src = src;
        img.alt = alt;
        img.style.width = '100px';
        img.style.height = '100px';
        img.style.objectFit = 'cover';
        
        // Initialize attempt counter for this image
        if (!imageLoadAttempts[src]) {
            imageLoadAttempts[src] = 0;
        }
        
        // Track loading attempts
        img.addEventListener('load', function() {
            console.log('✅ Image loaded successfully:', src);
            imageLoadAttempts[src] = 0; // Reset counter on successful load
        });
        
        img.addEventListener('error', function() {
            imageLoadAttempts[src]++;
            
            console.log('❌ Image failed to load (attempt ' + imageLoadAttempts[src] + '):', src);
            
            // Check for potential loop
            if (imageLoadAttempts[src] >= maxAttempts) {
                console.error('🚨 Potential infinite loop detected for image:', src);
                loopDetection.innerHTML += '<p style="color: red;">🚨 Potential infinite loop detected for image: ' + src + '</p>';
                // Stop trying to load this image
                img.src = ''; // Clear src to stop loading
                return;
            }
            
            // Try fallback image if not already trying to load it
            if (src.indexOf('no-image.svg') === -1) {
                console.log('🔄 Trying fallback image for:', src);
                img.src = './assets/images/no-image.svg';
            } else {
                console.log('🔄 Already using fallback, stopping retries for:', src);
            }
        });
        
        container.appendChild(img);
    }
    
    // Create test images
    const testImages = [
        { src: 'admin/images/cropped_circle_image.png', alt: 'Valid image' },
        { src: 'assets/images/non-existent-image.png', alt: 'Invalid image' },
        { src: './assets/images/no-image.svg', alt: 'Fallback image' }
    ];
    
    testImages.forEach((imageData, index) => {
        const container = document.createElement('div');
        container.style.textAlign = 'center';
        
        const title = document.createElement('h4');
        title.textContent = imageData.alt;
        container.appendChild(title);
        
        safeLoadImage(imageData.src, imageData.alt, container);
        
        const description = document.createElement('p');
        description.textContent = imageData.alt;
        container.appendChild(description);
        
        imageTests.appendChild(container);
    });
    
    testResults.innerHTML += '<p>✅ Infinite loop detection initialized</p>';
    testResults.innerHTML += '<p>📊 Monitoring ' + testImages.length + ' images for potential loops</p>';
    
    console.log('🔍 Infinite loop test initialized');
    console.log('🔄 Max attempts before loop detection:', maxAttempts);
});
</script>

<?php include 'inc/modern-footer.php'; ?>
<?php
$pageTitle = 'Dropdown Fix Test';
include 'inc/header.php';
?>

<div class="container mt-5">
    <h1>Dropdown Fix Test</h1>
    <p>This page tests if the dropdown menu fix is working correctly.</p>
    
    <div class="mt-4">
        <h3>Testing Instructions:</h3>
        <ol>
            <li>Hover over the "الأجهزة" menu item in the navigation bar</li>
            <li>The dropdown menu should appear and stay visible</li>
            <li>Move your mouse to different brands to see if categories load</li>
            <li>Click on a brand to see if categories appear</li>
            <li>Click on a category to see if models appear</li>
        </ol>
    </div>
    
    <div class="mt-4">
        <h3>Expected Behavior:</h3>
        <ul>
            <li>Dropdown menu should stay visible when hovering over it</li>
            <li>Menu should have a small delay before hiding to allow moving mouse to it</li>
            <li>Brand selection should load categories</li>
            <li>Category selection should load models</li>
        </ul>
    </div>
</div>

<?php include 'inc/modern-footer.php'; ?>
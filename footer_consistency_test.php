<?php
$pageTitle = "Footer Consistency Test - Gayar Plus";
require_once 'config.php';
include 'inc/header.php';
?>

<style>
.test-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.test-section {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--medium-gray);
}

.test-section h2 {
    color: var(--primary-navy);
    margin-bottom: 1rem;
}

.test-section p {
    color: var(--text-secondary);
    line-height: 1.6;
}
</style>

<div class="test-container">
    <div class="test-section">
        <h2>Footer Consistency Test</h2>
        <p>This page tests if the footer is displaying correctly with consistent styling across all pages.</p>
    </div>
    
    <div class="test-section">
        <h2>Test Content</h2>
        <p>This is a test page to verify that the footer displays consistently across all pages of the website.</p>
    </div>
</div>

<?php include 'inc/modern-footer.php'; ?>
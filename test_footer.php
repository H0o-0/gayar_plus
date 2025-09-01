<?php
$pageTitle = "Footer Test - Gayar Plus";
require_once 'config.php';
include 'inc/header.php';
?>

<style>
/* Ensure footer is always visible */
.footer {
    display: block !important;
    visibility: visible !important;
    position: relative !important;
    z-index: 1 !important;
    opacity: 1 !important;
    background: var(--light-gray) !important;
    padding: 4rem 0 2rem !important;
    margin-top: 6rem !important;
    border-top: 1px solid var(--medium-gray) !important;
}

.footer-content {
    max-width: 1400px !important;
    margin: 0 auto !important;
    padding: 0 2rem !important;
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important;
    gap: 2rem !important;
}

/* Make sure no other CSS is hiding the footer */
.footer, .footer-bottom, .footer-content, .footer-section {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}
</style>

<div style="min-height: 60vh; padding: 2rem;">
    <h1>Footer Visibility Test</h1>
    <p>This page is for testing footer visibility.</p>
</div>

<?php include 'inc/modern-footer.php'; ?>
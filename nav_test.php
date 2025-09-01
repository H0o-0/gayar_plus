<?php
require_once 'config.php';
$pageTitle = 'Navigation Test';
include 'inc/header.php';
?>

<style>
/* Simple CSS for testing */
.mega-menu {
    display: block !important;
    position: relative !important;
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) !important;
    width: 800px !important;
    background: #f8f9fa !important;
    border: 1px solid #dee2e6 !important;
    padding: 2rem !important;
    margin-top: 1rem !important;
}

.nav-item:first-child .mega-menu {
    display: block !important;
}
</style>

<div class="container mt-5">
    <h1>Navigation Test Page</h1>
    <p>This page is for testing the navigation functionality.</p>
    
    <div class="mt-4">
        <h3>Testing Navigation:</h3>
        <p>Hover over the "الأجهزة" menu item above to test the navigation.</p>
        <p>Check the browser console for any JavaScript errors.</p>
    </div>
    
    <div class="mt-4">
        <h3>Debug Information:</h3>
        <p>Base URL: <?php echo base_url; ?></p>
        <p>Brands in database:</p>
        <ul>
            <?php
            $brands = $conn->query("SELECT id, name FROM brands WHERE status = 1 ORDER BY name ASC");
            if($brands && $brands->num_rows > 0):
                while($brand = $brands->fetch_assoc()):
                    echo "<li>ID: " . $brand['id'] . " - Name: " . htmlspecialchars($brand['name']) . "</li>";
                endwhile;
            endif;
            ?>
        </ul>
    </div>
</div>

<?php include 'inc/modern-footer.php'; ?>
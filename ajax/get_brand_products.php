<?php
// Use a more robust path resolution
$root_path = dirname(__DIR__);
require_once $root_path . '/initialize.php';
require_once $root_path . '/classes/TextCleaner.php';

// Fetch products by brand_id since we've added the column to the products table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['brand_id'])) {
    $brand_id = TextCleaner::sanitize($_POST['brand_id']);
    
    // Validate that brand_id is numeric
    if (!is_numeric($brand_id)) {
        echo '<div class="alert alert-danger">طلب غير صحيح.</div>';
        exit;
    }
    
    // Fetch products specifically published under this brand
    $query = "SELECT p.*, c.category as category_name, sc.sub_category as sub_category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id 
              WHERE p.status = 1 AND p.brand_id = ? 
              ORDER BY p.date_created DESC";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($product = $result->fetch_assoc()) {
                echo '<div class="product-card">';
                echo '<h3>' . htmlspecialchars($product['product_name']) . '</h3>';
                echo '<p>' . htmlspecialchars(strip_tags($product['description'])) . '</p>';
                echo '<div class="product-meta">';
                echo '<span>الفئة: ' . htmlspecialchars($product['category_name']) . '</span>';
                echo '<span>التصنيف الفرعي: ' . htmlspecialchars($product['sub_category_name']) . '</span>';
                echo '</div>';
                echo '<a href="./?p=product_view&id=' . md5($product['id']) . '" class="btn-view-product">عرض التفاصيل</a>';
                echo '</div>';
            }
        } else {
            echo '<div class="alert alert-info text-center" style="padding: 2rem; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 10px; color: #6c757d;">';
            echo '<i class="fas fa-info-circle" style="font-size: 2rem; color: #17a2b8; margin-bottom: 1rem;"></i><br>';
            echo '<h5 style="margin-bottom: 1rem;">لم يتم نشر منتجات بعد</h5>';
            echo '<p>لم يتم نشر أي منتجات تحت هذه العلامة التجارية حتى الآن.</p>';
            echo '</div>';
        }
        
        $stmt->close();
    } else {
        echo '<div class="alert alert-danger">حدث خطأ أثناء جلب المنتجات. يرجى المحاولة لاحقاً.</div>';
    }
} else {
    echo '<div class="alert alert-warning">طلب غير صحيح.</div>';
}
?>
<?php
require_once('initialize.php');

echo "<h2>اختبار الفئات في قاعدة البيانات</h2>";

// جلب البراندات مع عدد الفئات
$brands_query = "
    SELECT 
        b.id,
        b.name,
        b.name_ar,
        COUNT(s.id) as categories_count
    FROM brands b 
    LEFT JOIN series s ON b.id = s.brand_id AND s.status = 1
    WHERE b.status = 1 
    GROUP BY b.id, b.name, b.name_ar
    ORDER BY b.name ASC
";

$brands_result = $conn->query($brands_query);

if($brands_result && $brands_result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Brand ID</th><th>Brand Name</th><th>Arabic Name</th><th>Categories Count</th><th>Categories</th></tr>";
    
    while($brand = $brands_result->fetch_assoc()) {
        $display_name = !empty($brand['name_ar']) ? $brand['name_ar'] : $brand['name'];
        
        echo "<tr>";
        echo "<td>" . $brand['id'] . "</td>";
        echo "<td>" . htmlspecialchars($brand['name']) . "</td>";
        echo "<td>" . htmlspecialchars($brand['name_ar']) . "</td>";
        echo "<td>" . $brand['categories_count'] . "</td>";
        echo "<td>";
        
        if($brand['categories_count'] > 0) {
            $categories_query = "
                SELECT 
                    s.id,
                    s.name,
                    s.name_ar,
                    COALESCE(NULLIF(s.name_ar, ''), s.name) as category_name
                FROM series s 
                WHERE s.brand_id = " . intval($brand['id']) . " 
                AND s.status = 1 
                ORDER BY s.sort_order ASC, s.name ASC
            ";
            
            $categories_result = $conn->query($categories_query);
            if($categories_result) {
                while($cat = $categories_result->fetch_assoc()) {
                    echo "- " . htmlspecialchars($cat['category_name']) . "<br>";
                }
            }
        } else {
            echo "لا توجد فئات";
        }
        
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "لا توجد براندات";
}
?>

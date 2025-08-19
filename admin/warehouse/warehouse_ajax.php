<?php
require_once('../../config.php');

if (isset($_GET['action']) && $_GET['action'] == 'get_brand_stats') {
    $stats_query = "
        SELECT c.category, c.emoji, COUNT(tw.id) as count 
        FROM categories c 
        LEFT JOIN temp_warehouse tw ON c.id = tw.category_id 
        WHERE c.category_type = 'devices' AND c.status = 1
        GROUP BY c.id, c.category, c.emoji 
        HAVING count > 0
        ORDER BY count DESC, c.category
    ";
    
    $result = $conn->query($stats_query);
    $stats = [];
    
    while ($row = $result->fetch_assoc()) {
        $stats[] = [
            'category' => $row['category'],
            'emoji' => $row['emoji'],
            'count' => intval($row['count'])
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($stats);
    exit;
}
?>
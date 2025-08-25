<?php
require_once 'config.php';

echo "<h2>Brands:</h2>\n";
$brands = $conn->query("SELECT id, name FROM brands WHERE status = 1 ORDER BY name");
if($brands) {
    while($brand = $brands->fetch_assoc()) {
        echo "ID: " . $brand['id'] . " - Name: " . $brand['name'] . "<br>\n";
    }
}

echo "<h2>Series:</h2>\n";
$series = $conn->query("SELECT id, name, brand_id FROM series WHERE status = 1 ORDER BY brand_id, name");
if($series) {
    while($s = $series->fetch_assoc()) {
        echo "ID: " . $s['id'] . " - Name: " . $s['name'] . " - Brand ID: " . $s['brand_id'] . "<br>\n";
    }
}

echo "<h2>Models:</h2>\n";
$models = $conn->query("SELECT id, name, series_id FROM models WHERE status = 1 ORDER BY series_id, name");
if($models) {
    while($m = $models->fetch_assoc()) {
        echo "ID: " . $m['id'] . " - Name: " . $m['name'] . " - Series ID: " . $m['series_id'] . "<br>\n";
    }
}
?>
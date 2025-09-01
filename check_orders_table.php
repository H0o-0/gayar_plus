<?php
require_once 'initialize.php';

// Show structure of orders table
echo "Structure of 'orders' table:\n";
$structure = $conn->query("DESCRIBE orders");
if ($structure) {
    while ($row = $structure->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}

// Show structure of clients table
echo "\nStructure of 'clients' table:\n";
$structure = $conn->query("DESCRIBE clients");
if ($structure) {
    while ($row = $structure->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}

// Check if there are any orders in the database
echo "\nChecking for existing orders:\n";
$orders = $conn->query("SELECT COUNT(*) as count FROM orders");
if ($orders) {
    $row = $orders->fetch_assoc();
    echo "Total orders: " . $row['count'] . "\n";
}

// Check if there are any clients in the database
echo "\nChecking for existing clients:\n";
$clients = $conn->query("SELECT COUNT(*) as count FROM clients");
if ($clients) {
    $row = $clients->fetch_assoc();
    echo "Total clients: " . $row['count'] . "\n";
}
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>
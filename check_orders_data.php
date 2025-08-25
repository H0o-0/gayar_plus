<?php
require_once 'initialize.php';

// Check orders with their client information
echo "Orders with client information:\n";
$qry = $conn->query("SELECT o.*, c.firstname, c.lastname, c.contact from `orders` o left join clients c on c.id = o.client_id order by o.date_created desc LIMIT 10");
if ($qry) {
    while ($row = $qry->fetch_assoc()) {
        echo "Order ID: " . $row['id'] . " | Client ID: " . $row['client_id'] . " | Client Name: " . ($row['firstname'] ?? 'N/A') . " " . ($row['lastname'] ?? '') . " | Phone: " . ($row['contact'] ?? 'N/A') . " | Amount: " . $row['amount'] . " | Date: " . $row['date_created'] . "\n";
    }
}

// Check for orders with invalid client_id
echo "\nChecking for orders with invalid client_id:\n";
$qry = $conn->query("SELECT o.* from `orders` o left join clients c on c.id = o.client_id where c.id is null");
if ($qry) {
    $count = $qry->num_rows;
    echo "Orders with invalid client_id: " . $count . "\n";
    if ($count > 0) {
        while ($row = $qry->fetch_assoc()) {
            echo "  Order ID: " . $row['id'] . " | Client ID: " . $row['client_id'] . " | Amount: " . $row['amount'] . " | Date: " . $row['date_created'] . "\n";
        }
    }
}

// Check all clients
echo "\nAll clients in database:\n";
$qry = $conn->query("SELECT id, firstname, lastname, contact from `clients`");
if ($qry) {
    while ($row = $qry->fetch_assoc()) {
        echo "  Client ID: " . $row['id'] . " | Name: " . $row['firstname'] . " " . $row['lastname'] . " | Phone: " . $row['contact'] . "\n";
    }
}
?>
<?php
session_start();
if (!isset($_SESSION['first_name']) || !isset($_SESSION['last_name'])) {
    header('Location: login.php');
    exit();
}

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'inventory';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Determine the export type
$export_type = isset($_GET['export']) ? $_GET['export'] : 'products';

if ($export_type == 'products') {
    $sql = "
        SELECT 
            p.id, 
            p.product_name, 
            p.description, 
            p.img, 
            p.stock, 
            CONCAT(u.first_name, ' ', u.last_name) AS created_by, 
            DATE_FORMAT(p.created_at, '%b %d, %Y %h:%i:%s %p') AS created_at, 
            DATE_FORMAT(p.updated_at, '%b %d, %Y %h:%i:%s %p') AS updated_at 
        FROM 
            products p 
        JOIN 
            users u 
        ON 
            p.created_by = u.id";
} elseif ($export_type == 'deliveries') {
    $sql = "
        SELECT 
            o.created_at as date_received, 
            o.quantity_received, 
            p.product_name as product, 
            s.supplier_name as supplier, 
            o.batch, 
            CONCAT(u.first_name, ' ', u.last_name) AS created_by 
        FROM 
            order_product o 
        JOIN 
            users u 
        ON 
            o.created_by = u.id
        JOIN 
            products p 
        ON 
            o.product = p.id
        JOIN 
            suppliers s 
        ON 
            o.supplier = s.id";
} elseif ($export_type == 'purchase_orders') {
    $sql = "
        SELECT 
            o.id, 
            o.quantity_ordered, 
            o.quantity_received, 
            (o.quantity_ordered - o.quantity_received) AS quantity_remaining, 
            o.status, 
            o.batch, 
            s.supplier_name as supplier, 
            p.product_name as product, 
            CONCAT(u.first_name, ' ', u.last_name) AS created_by 
        FROM 
            order_product o 
        JOIN 
            users u 
        ON 
            o.created_by = u.id
        JOIN 
            products p 
        ON 
            o.product = p.id
        JOIN 
            suppliers s 
        ON 
            o.supplier = s.id";
} elseif ($export_type == 'suppliers') {
    $sql = "
        SELECT 
            s.id as sid, 
            DATE_FORMAT(s.created_at, '%d/%m/%Y %H:%i') as created_at, 
            s.supplier_name, 
            s.supplier_location, 
            s.email, 
            CONCAT(u.first_name, ' ', u.last_name) AS created_by 
        FROM 
            suppliers s 
        JOIN 
            users u 
        ON 
            s.created_by = u.id";
}

$result = $conn->query($sql);

if (!$result) {
    die('Error: ' . $conn->error);
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $export_type . '.csv');
$output = fopen('php://output', 'w');

if ($export_type == 'products') {
    fputcsv($output, ['id', 'product_name', 'description', 'img', 'stock', 'created_by', 'created_at', 'updated_at']);
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
} elseif ($export_type == 'deliveries') {
    fputcsv($output, ['date_received', 'quantity_received', 'product', 'supplier', 'batch', 'created_by']);
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
} elseif ($export_type == 'purchase_orders') {
    fputcsv($output, ['id', 'quantity_ordered', 'quantity_received', 'quantity_remaining', 'status', 'batch', 'supplier', 'product', 'created_by']);
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
} elseif ($export_type == 'suppliers') {
    fputcsv($output, ['sid', 'created_at', 'supplier_name', 'supplier_location', 'email', 'created_by']);
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
$conn->close();
?>

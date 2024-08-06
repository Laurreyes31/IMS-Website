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

if (isset($_GET['id'])) {
    $supplier_id = $_GET['id'];

    // First, delete the entries in the productsuppliers table that reference this supplier
    $sql = "DELETE FROM productsuppliers WHERE supplier = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $supplier_id);
    $stmt->execute();
    $stmt->close();

    // Then, delete the supplier
    $sql = "DELETE FROM suppliers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $supplier_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['delete_message'] = 'Supplier successfully deleted.';
    header('Location: view_suppliers.php');
    exit();
} else {
    $_SESSION['delete_message'] = 'No supplier ID provided.';
    header('Location: view_suppliers.php');
    exit();
}

$conn->close();
?>

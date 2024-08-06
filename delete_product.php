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
    $product_id = $_GET['id'];

    // Delete the product's order history
    $conn->query("DELETE FROM order_product_history WHERE order_product_id IN (SELECT id FROM order_product WHERE product = $product_id)");

    // Delete the product's orders
    $conn->query("DELETE FROM order_product WHERE product = $product_id");

    // Delete the product's suppliers
    $conn->query("DELETE FROM productsuppliers WHERE product = $product_id");

    // Delete the product
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['delete_message'] = 'Product successfully deleted.';
}

$conn->close();
header('Location: view_products.php');
exit();
?>

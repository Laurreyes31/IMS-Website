<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'inventory';

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    $orders = $_POST['orders'];
    $user_id = $_SESSION['user_id'];
    $batch = time(); // Generate a unique batch number using the current timestamp

    $stmt = $conn->prepare("INSERT INTO order_product (supplier, product, quantity_ordered, quantity_received, quantity_remaining, status, batch, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

    foreach ($orders as $order) {
        $product_id = $order['product_id'];
        foreach ($order['quantity'] as $supplier_id => $quantity_ordered) {
            $quantity_received = 0;
            $quantity_remaining = 0; // Set initial quantity_remaining to 0
            $status = 'PENDING';

            $stmt->bind_param('iiiiisii', $supplier_id, $product_id, $quantity_ordered, $quantity_received, $quantity_remaining, $status, $batch, $user_id);
            $stmt->execute();
        }
    }

    $stmt->close();
    $conn->close();

    $_SESSION['success_message'] = 'Order successfully created with batch number ' . $batch;
    header('Location: create_order.php');
    exit();
}
?>

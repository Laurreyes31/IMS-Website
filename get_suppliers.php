<?php
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'inventory';

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    $sql = "SELECT s.id as supplier_id, s.supplier_name
            FROM suppliers s
            JOIN productsuppliers ps ON s.id = ps.supplier
            WHERE ps.product = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $suppliers = [];
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode($suppliers);
}
?>

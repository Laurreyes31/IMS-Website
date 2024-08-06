<?php
session_start();
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'inventory';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['batch'])) {
    $batch = intval($_GET['batch']);

    $sql = "
        SELECT 
            op.id,
            p.product_name, 
            op.quantity_ordered, 
            op.quantity_received,
            s.supplier_name, 
            op.status
        FROM 
            order_product op
        JOIN 
            products p ON op.product = p.id
        JOIN 
            suppliers s ON op.supplier = s.id
        WHERE 
            op.batch = ?
        ORDER BY
            op.product ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $batch);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['product_name'] . '</td>';
        echo '<td>' . $row['quantity_ordered'] . '</td>';
        echo '<td><input type="number" name="orders[' . $row['id'] . '][quantity_received]" value="' . $row['quantity_received'] . '" class="form-control" readonly></td>';
        echo '<td><input type="number" name="orders[' . $row['id'] . '][quantity_delivered]" value="0" class="form-control"></td>';
        echo '<td>' . $row['supplier_name'] . '</td>';
        echo '<td><select name="orders[' . $row['id'] . '][status]" class="form-control status-select">';
        echo '<option value="PENDING"' . ($row['status'] == 'PENDING' ? ' selected' : '') . '>PENDING</option>';
        echo '<option value="INCOMPLETE"' . ($row['status'] == 'INCOMPLETE' ? ' selected' : '') . '>INCOMPLETE</option>';
        echo '<option value="COMPLETED"' . ($row['status'] == 'COMPLETED' ? ' selected' : '') . '>COMPLETED</option>';
        echo '</select></td>';
        echo '</tr>';
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['orders'])) {
    foreach ($_POST['orders'] as $order_id => $order_data) {
        $quantity_received = intval($order_data['quantity_received']);
        $quantity_delivered = intval($order_data['quantity_delivered']);
        $status = $order_data['status'];

        if ($quantity_delivered > 0) {
            // Update quantity received
            $quantity_received += $quantity_delivered;

            // Update order product table
            $sql = "UPDATE order_product SET quantity_received = ?, status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('isi', $quantity_received, $status, $order_id);
            $stmt->execute();
            $stmt->close();

            // Insert into order product history
            $sql = "INSERT INTO order_product_history (order_product_id, qty_received, date_received, date_updated) VALUES (?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $order_id, $quantity_delivered);
            $stmt->execute();
            $stmt->close();

            // Update stock in products table
            $sql = "SELECT product FROM order_product WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $order_id);
            $stmt->execute();
            $stmt->bind_result($product_id);
            $stmt->fetch();
            $stmt->close();

            $sql = "SELECT SUM(quantity_received) FROM order_product WHERE product = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $stmt->bind_result($total_quantity_received);
            $stmt->fetch();
            $stmt->close();

            $sql = "UPDATE products SET stock = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $total_quantity_received, $product_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Update status only in order product table
            $sql = "UPDATE order_product SET status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $status, $order_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    $_SESSION['update_message'] = 'Order updated successfully';
    header('Location: view_orders.php');
    exit();
}

$conn->close();
?>

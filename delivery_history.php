<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'inventory';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    $sql = "SELECT id, date_received, qty_received FROM order_product_history WHERE order_product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<table class="table">';
    echo '<thead><tr><th>#</th><th>Date Received</th><th>Quantity Received</th></tr></thead>';
    echo '<tbody>';
    $index = 1;
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $index++ . '</td>';
        echo '<td>' . date('D, d M Y H:i:s T', strtotime($row['date_received'])) . '</td>';
        echo '<td>' . $row['qty_received'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';

    $stmt->close();
}

$conn->close();
?>

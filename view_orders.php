<?php
session_start();
if (!isset($_SESSION['first_name']) || !isset($_SESSION['last_name'])) {
    header('Location: login.php');
    exit();
}

$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$initials = strtoupper($first_name[0] . $last_name[0]);

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'inventory';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$sql = "
    SELECT 
        op.batch, 
        op.id AS order_id, 
        p.product_name, 
        op.quantity_ordered, 
        op.quantity_received,
        s.supplier_name, 
        op.status, 
        CONCAT(u.first_name, ' ', u.last_name) AS ordered_by, 
        op.created_at 
    FROM 
        order_product op
    JOIN 
        products p ON op.product = p.id
    JOIN 
        suppliers s ON op.supplier = s.id
    JOIN 
        users u ON op.created_by = u.id
    ORDER BY 
        op.batch, op.product ASC, op.created_at DESC";
$result = $conn->query($sql);

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['batch']][] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php include 'db-styles.php'; ?>
    <style>
        .batch-header {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .table thead th {
            background-color: #f8f9fa;
        }
        .status-pending {
            background-color: red;
            color: white;
            padding: 5px;
            border-radius: 5px;
            text-align: center;
        }
        .status-completed {
            background-color: green;
            color: white;
            padding: 5px;
            border-radius: 5px;
            text-align: center;
        }
        .status-incomplete {
            background-color: #FFD300;
            color: white;
            padding: 5px;
            border-radius: 5px;
            text-align: center;
        }
        .delivery-history-btn {
            background-color: #343a40;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .update-btn {
            background-color: #343a40;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .alert-success {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'db-sidebar.php'; ?>
    <div class="content">
        <span class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></span>
        <div class="custom-form-container">
            <h2>Purchase Orders List</h2>
            <?php if (isset($_SESSION['update_message'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['update_message'];
                    unset($_SESSION['update_message']);
                    ?>
                </div>
            <?php endif; ?>
            <?php foreach ($orders as $batch => $orderList): ?>
                <div class="batch-header">
                    <h5>BATCH #: <?php echo $batch; ?></h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Qty Ordered</th>
                                <th>Qty Received</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Ordered By</th>
                                <th>Created Date</th>
                                <th>Delivery History</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderList as $index => $order): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo $order['product_name']; ?></td>
                                    <td><?php echo $order['quantity_ordered']; ?></td>
                                    <td><?php echo $order['quantity_received']; ?></td>
                                    <td><?php echo $order['supplier_name']; ?></td>
                                    <td class="<?php 
                                        if ($order['status'] == 'PENDING') {
                                            echo 'status-pending';
                                        } elseif ($order['status'] == 'COMPLETED') {
                                            echo 'status-completed';
                                        } elseif ($order['status'] == 'INCOMPLETE') {
                                            echo 'status-incomplete';
                                        } 
                                    ?>">
                                        <?php echo $order['status']; ?>
                                    </td>
                                    <td><?php echo $order['ordered_by']; ?></td>
                                    <td><?php echo $order['created_at']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-secondary delivery-history-btn" data-order-id="<?php echo $order['order_id']; ?>">Deliveries</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-secondary update-btn" data-batch="<?php echo $batch; ?>">Update</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Update Order Modal -->
    <div class="modal fade" id="updateOrderModal" tabindex="-1" role="dialog" aria-labelledby="updateOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateOrderModalLabel">Update Purchase Order: Batch #<span id="batchNumber"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateOrderForm">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Qty Ordered</th>
                                    <th>Qty Received</th>
                                    <th>Qty Delivered</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="updateOrderTableBody">
                                <!-- Dynamic content will be loaded here -->
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary">OK</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery History Modal -->
    <div class="modal fade" id="deliveryHistoryModal" tabindex="-1" role="dialog" aria-labelledby="deliveryHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deliveryHistoryModalLabel">Delivery History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="deliveryHistoryTableBody">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include 'db-toggle.php'; ?>
    <script>
        $(document).ready(function() {
            $('.update-btn').click(function() {
                var batch = $(this).data('batch');
                $('#batchNumber').text(batch);

                $.ajax({
                    url: 'update_order.php',
                    method: 'GET',
                    data: { batch: batch },
                    success: function(response) {
                        $('#updateOrderTableBody').html(response);
                        $('#updateOrderModal').modal('show');
                    }
                });
            });

            $('.delivery-history-btn').click(function() {
                var orderId = $(this).data('order-id');
                $.ajax({
                    url: 'delivery_history.php',
                    method: 'GET',
                    data: { order_id: orderId },
                    success: function(response) {
                        $('#deliveryHistoryTableBody').html(response);
                        $('#deliveryHistoryModal').modal('show');
                    }
                });
            });

            $('#updateOrderForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'update_order.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#updateOrderModal').modal('hide');
                        location.reload();
                    }
                });
            });
        });
    </script>
</body>
</html>

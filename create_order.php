<?php
session_start();
if (!isset($_SESSION['first_name']) || !isset($_SESSION['last_name'])) {
    header('Location: login.php');
    exit();
}

$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$user_id = $_SESSION['user_id'];

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'inventory';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$products = $conn->query("SELECT id, product_name FROM products");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php include 'order-styles.php'; ?>
</head>
<body>
    <?php include 'db-sidebar.php'; ?>
    <div class="content">
        <span class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></span>
        <div class="custom-form-container">
            <h2>Create Order</h2>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>
            <form id="order-form" method="POST" action="submit_order.php">
                <div class="order-section">
                    <div class="custom-form-group">
                        <label for="product_id_0">Product Name</label>
                        <select class="form-control product-select" id="product_id_0" name="orders[0][product_id]" required>
                            <option value="">Select Product</option>
                            <?php while ($product = $products->fetch_assoc()): ?>
                                <option value="<?php echo $product['id']; ?>"><?php echo $product['product_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="supplier-container" id="supplier-container_0"></div>
                </div>
                <button type="submit" class="btn btn-primary" style="background-color: #DE2910; border: none;">Submit Order</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include 'db-toggle.php'; ?>
    <script>
        $(document).ready(function() {
            function updateSuppliers(productSelect, index) {
                var product_id = productSelect.val();
                console.log('Product ID:', product_id);
                if (product_id) {
                    $.ajax({
                        url: 'get_suppliers.php',
                        method: 'GET',
                        data: { product_id: product_id },
                        success: function(response) {
                            var suppliers = JSON.parse(response);
                            var supplierContainer = $('#supplier-container_' + index);
                            supplierContainer.empty();
                            if (suppliers.length > 0) {
                                for (var i = 0; i < suppliers.length; i++) {
                                    var supplier = suppliers[i];
                                    var html = '<div class="custom-form-group">' +
                                                    '<label>Supplier ' + (i + 1) + '</label>' +
                                                    '<div class="custom-supplier-quantity">' +
                                                        '<input type="text" class="form-control" value="' + supplier.supplier_name + '" readonly>' +
                                                        '<input type="number" class="form-control" name="orders[' + index + '][quantity][' + supplier.supplier_id + ']" placeholder="Enter quantity...">' +
                                                    '</div>' +
                                                '</div>';
                                    supplierContainer.append(html);
                                }
                            } else {
                                supplierContainer.append('<p>No suppliers found for this product.</p>');
                            }
                        }
                    });
                } else {
                    $('#supplier-container_' + index).empty();
                }
            }

            $('#product_id_0').change(function() {
                updateSuppliers($(this), 0);
            });
        });
    </script>
</body>
</html>

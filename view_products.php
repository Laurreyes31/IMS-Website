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

$products = $conn->query("SELECT p.*, u.first_name, u.last_name FROM products p JOIN users u ON p.created_by = u.id");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php include 'db-styles.php'; ?>
</head>

<body>
    <?php include 'db-sidebar.php'; ?>
    <div class="content">
        <span class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></span>
        <div class="products-list mt-4">
            <h3>List of Products</h3>
            <?php if (isset($_SESSION['edit_message'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['edit_message'];
                    unset($_SESSION['edit_message']);
                    ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['delete_message'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['delete_message'];
                    unset($_SESSION['delete_message']);
                    ?>
                </div>
            <?php endif; ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Stock</th>
                        <th>Description</th>
                        <th>Suppliers</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $products->fetch_assoc()): ?>
                        <?php
                        // Fetch suppliers for the product
                        $product_id = $product['id'];
                        $conn = new mysqli($host, $user, $password, $dbname);
                        $supplier_result = $conn->query("SELECT s.supplier_name FROM suppliers s JOIN productsuppliers ps ON s.id = ps.supplier WHERE ps.product = $product_id");
                        $suppliers = [];
                        while ($supplier_row = $supplier_result->fetch_assoc()) {
                            $suppliers[] = $supplier_row['supplier_name'];
                        }
                        $supplier_names = implode(', ', $suppliers);
                        $conn->close();
                        ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><img src="<?php echo $product['img']; ?>" alt="Product Image" style="width: 100px; height: auto;"></td>
                            <td><?php echo $product['product_name']; ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            <td><?php echo $product['description']; ?></td>
                            <td><?php echo $supplier_names; ?></td>
                            <td><?php echo $product['first_name'] . ' ' . $product['last_name']; ?></td>
                            <td><?php echo date('M d, Y @ h:i:s A', strtotime($product['created_at'])); ?></td>
                            <td><?php echo date('M d, Y @ h:i:s A', strtotime($product['updated_at'])); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-product" data-id="<?php echo $product['id']; ?>" data-product_name="<?php echo $product['product_name']; ?>" data-description="<?php echo $product['description']; ?>" data-img="<?php echo $product['img']; ?>">Edit</button>
                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <p class="text-right">Total Products: <?php echo $products->num_rows; ?></p>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm" method="POST" action="edit_product.php" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" id="editProductId">
                        <div class="form-group">
                            <label for="editProductName">Product Name</label>
                            <input type="text" class="form-control" id="editProductName" name="product_name" required>
                        </div>
                        <div class="form-group">
                            <label for="editDescription">Description</label>
                            <textarea class="form-control" id="editDescription" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="editImg">Product Image</label>
                            <input type="file" class="form-control-file" id="editImg" name="img">
                            <img id="currentImg" src="" alt="Current Product Image" style="width: 100px; height: auto; margin-top: 10px;">
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include 'db-toggle.php'; ?>
    <script>
        $(document).on('click', '.edit-product', function() {
            const id = $(this).data('id');
            const productName = $(this).data('product_name');
            const description = $(this).data('description');
            const img = $(this).data('img');

            $('#editProductId').val(id);
            $('#editProductName').val(productName);
            $('#editDescription').val(description);
            $('#currentImg').attr('src', img);

            $('#editProductModal').modal('show');
        });
    </script>
</body>

</html>

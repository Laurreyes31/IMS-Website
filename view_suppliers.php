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

$suppliers = $conn->query("SELECT s.*, u.first_name, u.last_name FROM suppliers s JOIN users u ON s.created_by = u.id");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Suppliers</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php include 'db-styles.php'; ?>
</head>

<body>
    <?php include 'db-sidebar.php'; ?>
    <div class="content">
        <span class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></span>
        <div class="suppliers-list mt-4">
            <h3>List of Suppliers</h3>
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
                        <th>Supplier Name</th>
                        <th>Supplier Location</th>
                        <th>Contact Details</th>
                        <th>Products</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $supplier['id']; ?></td>
                            <td><?php echo $supplier['supplier_name']; ?></td>
                            <td><?php echo $supplier['supplier_location']; ?></td>
                            <td><?php echo $supplier['email']; ?></td>
                            <td>
                                <ul>
                                    <?php
                                    $product_query = $conn->query("SELECT product_name FROM products p JOIN order_product ps ON p.id = ps.product WHERE ps.supplier = " . $supplier['id']);
                                    while ($product = $product_query->fetch_assoc()) {
                                        echo '<li>' . $product['product_name'] . '</li>';
                                    }
                                    ?>
                                </ul>
                            </td>
                            <td><?php echo $supplier['first_name'] . ' ' . $supplier['last_name']; ?></td>
                            <td><?php echo date('M d, Y @ h:i:s A', strtotime($supplier['created_at'])); ?></td>
                            <td><?php echo date('M d, Y @ h:i:s A', strtotime($supplier['updated_at'])); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-supplier" data-id="<?php echo $supplier['id']; ?>" data-supplier_name="<?php echo $supplier['supplier_name']; ?>" data-supplier_location="<?php echo $supplier['supplier_location']; ?>" data-email="<?php echo $supplier['email']; ?>">Edit</button>
                                <a href="delete_supplier.php?id=<?php echo $supplier['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <p class="text-right">Total Suppliers: <?php echo $suppliers->num_rows; ?></p>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSupplierModalLabel">Edit Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editSupplierForm" method="POST" action="edit_supplier.php">
                        <input type="hidden" name="supplier_id" id="editSupplierId">
                        <div class="form-group">
                            <label for="editSupplierName">Supplier Name</label>
                            <input type="text" class="form-control" id="editSupplierName" name="supplier_name" required>
                        </div>
                        <div class="form-group">
                            <label for="editSupplierLocation">Supplier Location</label>
                            <input type="text" class="form-control" id="editSupplierLocation" name="supplier_location" required>
                        </div>
                        <div class="form-group">
                            <label for="editEmail">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
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
        $(document).on('click', '.edit-supplier', function() {
            const id = $(this).data('id');
            const supplierName = $(this).data('supplier_name');
            const supplierLocation = $(this).data('supplier_location');
            const email = $(this).data('email');

            $('#editSupplierId').val(id);
            $('#editSupplierName').val(supplierName);
            $('#editSupplierLocation').val(supplierLocation);
            $('#editEmail').val(email);

            $('#editSupplierModal').modal('show');
        });
    </script>
</body>

</html>

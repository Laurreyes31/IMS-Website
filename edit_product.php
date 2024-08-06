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

$product_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $selected_suppliers = isset($_POST['suppliers']) ? $_POST['suppliers'] : [];

    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $img_name = $_FILES['img']['name'];
        $img_tmp_name = $_FILES['img']['tmp_name'];
        $img_destination = 'uploads/' . $img_name;
        move_uploaded_file($img_tmp_name, $img_destination);

        $sql = "UPDATE products SET product_name = ?, description = ?, img = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $product_name, $description, $img_destination, $product_id);
    } else {
        $sql = "UPDATE products SET product_name = ?, description = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $product_name, $description, $product_id);
    }

    if (!$stmt->execute()) {
        echo "Error updating product: " . $stmt->error;
    }
    $stmt->close();

    $_SESSION['edit_message'] = 'Product details successfully updated.';
    header('Location: view_products.php');
    exit();
}

// Fetch current product details
$product_sql = "SELECT * FROM products WHERE id = ?";
$product_stmt = $conn->prepare($product_sql);
$product_stmt->bind_param('i', $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();
$product = $product_result->fetch_assoc();
$product_stmt->close();

// Fetch all suppliers
$suppliers_sql = "SELECT id, supplier_name FROM suppliers";
$suppliers_result = $conn->query($suppliers_sql);

// Fetch selected suppliers for the product
$selected_suppliers_sql = "SELECT supplier FROM productsuppliers WHERE product = ?";
$selected_suppliers_stmt = $conn->prepare($selected_suppliers_sql);
$selected_suppliers_stmt->bind_param('i', $product_id);
$selected_suppliers_stmt->execute();
$selected_suppliers_result = $selected_suppliers_stmt->get_result();
$selected_suppliers = [];
while ($row = $selected_suppliers_result->fetch_assoc()) {
    $selected_suppliers[] = $row['supplier'];
}
$selected_suppliers_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php include 'db-styles.php'; ?>
</head>

<body>
    <div class="container mt-4">
        <h2>Edit Product</h2>
        <form method="POST" action="edit_product.php?id=<?php echo $product_id; ?>" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <div class="form-group">
                <label for="product_name">Product Name</label>
                <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="suppliers">Suppliers</label>
                <select multiple class="form-control" id="suppliers" name="suppliers[]" required>
                    <?php while ($supplier = $suppliers_result->fetch_assoc()): ?>
                        <option value="<?php echo $supplier['id']; ?>" <?php echo in_array($supplier['id'], $selected_suppliers) ? 'selected' : ''; ?>><?php echo $supplier['supplier_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="img">Product Image</label>
                <input type="file" class="form-control-file" id="img" name="img">
                <img src="<?php echo $product['img']; ?>" alt="Current Product Image" style="width: 100px; height: auto; margin-top: 10px;">
            </div>
            <button type="submit" class="btn btn-primary">Save changes</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

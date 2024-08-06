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

// Fetch suppliers for the dropdown
$suppliers = $conn->query("SELECT id, supplier_name FROM suppliers");

$product_name = '';
$description = '';
$selected_suppliers = [];
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $created_by = $_SESSION['user_id'];
    $selected_suppliers = $_POST['suppliers'];
    $img = '';

    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $img_name = $_FILES['img']['name'];
        $img_tmp_name = $_FILES['img']['tmp_name'];
        $img_type = mime_content_type($img_tmp_name); // Get the MIME type of the uploaded file
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];

        if (in_array($img_type, $allowed_types)) {
            $extension = pathinfo($img_name, PATHINFO_EXTENSION);
            $new_img_name = uniqid() . '.' . $extension;
            $img_folder = 'uploads/' . $new_img_name;
            if (move_uploaded_file($img_tmp_name, $img_folder)) {
                $img = $img_folder;
            } else {
                $error_message = "Failed to upload the file.";
            }
        } else {
            $error_message = "Invalid file type. Only JPG, PNG, and WEBP files are allowed.";
        }
    }

    if (!$error_message) {
        $sql = "INSERT INTO products (product_name, description, img, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $product_name, $description, $img, $created_by);
        $stmt->execute();
        $product_id = $stmt->insert_id;
        $stmt->close();

        // Insert into productsuppliers table
        foreach ($selected_suppliers as $supplier_id) {
            $sql = "INSERT INTO productsuppliers (supplier, product, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $supplier_id, $product_id);
            $stmt->execute();
            $stmt->close();
        }

        $success_message = 'Product successfully added.';
        // Reset form fields
        $product_name = '';
        $description = '';
        $selected_suppliers = [];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php include 'db-styles.php'; ?>
</head>

<body>
    <?php include 'db-sidebar.php'; ?>
    <div class="content">
        <span class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></span>
        <div class="form-container">
            <h2>Create Product</h2>
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="add_product.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="suppliers">Suppliers</label>
                    <select multiple class="form-control" id="suppliers" name="suppliers[]" required>
                        <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                            <option value="<?php echo $supplier['id']; ?>" <?php echo in_array($supplier['id'], $selected_suppliers) ? 'selected' : ''; ?>><?php echo $supplier['supplier_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="img">Product Image</label>
                    <input type="file" class="form-control-file" id="img" name="img">
                </div>
                <button type="submit" class="btn btn-primary" style="background-color: #DE2910; border: none;">Create Product</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include 'db-toggle.php'; ?>
</body>

</html>

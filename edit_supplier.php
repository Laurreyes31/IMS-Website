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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $supplier_name = $_POST['supplier_name'];
    $supplier_location = $_POST['supplier_location'];
    $email = $_POST['email'];

    $sql = "UPDATE suppliers SET supplier_name = ?, supplier_location = ?, email = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $supplier_name, $supplier_location, $email, $supplier_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['edit_message'] = 'Supplier details successfully updated.';
    header('Location: view_suppliers.php');
    exit();
}

// Fetch the supplier details if the ID is provided
if (isset($_GET['id'])) {
    $supplier_id = $_GET['id'];
    $result = $conn->query("SELECT * FROM suppliers WHERE id = $supplier_id");
    $supplier = $result->fetch_assoc();
} else {
    $_SESSION['edit_message'] = 'No supplier ID provided.';
    header('Location: view_suppliers.php');
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier</title>
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
            <h2>Edit Supplier</h2>
            <form method="POST" action="edit_supplier.php">
                <input type="hidden" name="supplier_id" value="<?php echo $supplier['id']; ?>">
                <div class="form-group">
                    <label for="supplier_name">Supplier Name</label>
                    <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="<?php echo htmlspecialchars($supplier['supplier_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="supplier_location">Location</label>
                    <input type="text" class="form-control" id="supplier_location" name="supplier_location" value="<?php echo htmlspecialchars($supplier['supplier_location']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($supplier['email']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" style="background-color: #DE2910; border: none;">Save changes</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include 'db-toggle.php'; ?>
</body>

</html>

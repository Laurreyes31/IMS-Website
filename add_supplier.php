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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $supplier_name = $_POST['supplier_name'];
    $location = $_POST['supplier_location']; 
    $email = $_POST['email'];
    $created_by = $_SESSION['user_id'];

    $sql = "INSERT INTO suppliers (supplier_name, supplier_location, email, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $supplier_name, $location, $email, $created_by);
    $stmt->execute();
    $stmt->close();

    $_SESSION['add_message'] = 'Supplier successfully added.';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
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
            <h2>Create Supplier</h2>
            <?php if (isset($_SESSION['add_message'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['add_message'];
                    unset($_SESSION['add_message']);
                    ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="add_supplier.php">
                <div class="form-group">
                    <label for="supplier_name">Supplier Name</label>
                    <input type="text" class="form-control" id="supplier_name" name="supplier_name" placeholder="Enter supplier name..." required>
                </div>
                <div class="form-group">
                    <label for="supplier_location">Location</label>
                    <input type="text" class="form-control" id="supplier_location" name="supplier_location" placeholder="Enter product supplier location..." required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter supplier email..." required>
                </div>
                <button type="submit" class="btn btn-primary" style="background-color: #DE2910; border: none;">+ Create Supplier</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include 'db-toggle.php'; ?>
</body>

</html>

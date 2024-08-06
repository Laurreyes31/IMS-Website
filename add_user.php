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
    if (isset($_POST['user_id'])) {
        // Update user
        $id = $_POST['user_id'];
        $new_first_name = $_POST['first_name'];
        $new_last_name = $_POST['last_name'];
        $new_email = $_POST['email'];

        // If password is set, hash it
        if (!empty($_POST['password'])) {
            $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssi', $new_first_name, $new_last_name, $new_email, $new_password, $id);
        } else {
            $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssi', $new_first_name, $new_last_name, $new_email, $id);
        }
        
        $stmt->execute();
        $stmt->close();

        $_SESSION['edit_message'] = 'User details successfully updated.';
        header('Location: view_users.php');
        exit();
    } else {
        // Add new user
        $new_first_name = $_POST['first_name'];
        $new_last_name = $_POST['last_name'];
        $new_email = $_POST['email'];
        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

        $sql = "INSERT INTO users (first_name, last_name, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $new_first_name, $new_last_name, $new_email, $new_password);
        $stmt->execute();
        $stmt->close();

        $_SESSION['add_message'] = 'User successfully added.';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
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
            <h2>Create User</h2>
            <?php if (isset($_SESSION['add_message'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['add_message'];
                    unset($_SESSION['add_message']);
                    ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="add_user.php">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="background-color: #DE2910; border: none;">Add User</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include 'db-toggle.php'; ?>
</body>

</html>

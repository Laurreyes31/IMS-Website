<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'inventory';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['delete_message'] = 'User successfully deleted.';
}

$conn->close();

header('Location: view_users.php');
exit();
?>

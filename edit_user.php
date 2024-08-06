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

if (!isset($_GET['id'])) {
    header('Location: add_user.php');
    exit();
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_first_name = $_POST['first_name'];
    $new_last_name = $_POST['last_name'];
    $new_email = $_POST['email'];

    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $new_first_name, $new_last_name, $new_email, $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['edit_message'] = 'User details successfully updated.';
    header('Location: add_user.php');
    exit();
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$user) {
    header('Location: add_user.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php include 'db-styles.php'; ?>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="top-section">
            <div class="logo">
                <img src="icons/logo.jpg" alt="Logo">
            </div>
            <div class="profile">
                <img src="icons/dashboardprofile.jpg" alt="Profile Picture">
                <div class="name">
                    <span class="full-name"><?php echo $first_name . ' ' . $last_name; ?></span>
                    <span class="initials" style="display:none;"><?php echo $initials; ?></span>
                </div>
            </div>
            <div class="separator"></div>
        </div>
        <ul class="menu">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span class="text">Dashboard</span></a></li>
            <li><a href="add_user.php"><i class="fas fa-user-plus"></i><span class="text">Add User</span></a></li>
        </ul>
        <div class="logout">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="text">Log Out</span></a>
        </div>
    </div>
    <div class="content">
        <span class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></span>
        <div class="form-container">
            <h2>Edit User</h2>
            <form method="POST" action="edit_user.php?id=<?php echo $id; ?>">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" style="background-color: #DE2910; border: none;">Update User</button>
            </form>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('expanded');
            document.querySelector('.content').classList.toggle('expanded');
            const fullName = document.querySelector('.full-name');
            const initials = document.querySelector('.initials');
            if (sidebar.classList.contains('expanded')) {
                fullName.style.display = 'inline';
                initials.style.display = 'none';
            } else {
                fullName.style.display = 'none';
                initials.style.display = 'inline';
            }
        });
    </script>
</body>

</html>

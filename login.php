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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];  
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS Login - Inventory Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            font-family: 'Roboto', sans-serif;
        }

        .bg {
            background-image: url('images/login.jpg');
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            height: 100%;
        }

        .form-control {
            font-style: italic;
        }

        .login-container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.8);
            padding: 55px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.2);
        }

        .login-logo {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 150px; 
            height: 150px;
            border-radius: 50%;
            border: 3px solid black; 
            margin-bottom: 20px;
            background-image: url('icons/logo.jpg'); 
            background-size: 100%;
            background-position: center;
            background-repeat: no-repeat;
        }

        .login-subtitle {
            text-align: center;
            color: #DE2910;
            margin-bottom: 30px;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="bg">
        <div class="login-container">
            <div class="login-box">
                <div class="login-logo"></div>
                <div class="login-subtitle">INVENTORY MANAGEMENT SYSTEM</div>
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="email">EMAIL</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">PASSWORD</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" style="background-color: #DE2910; border: none;">Login</button>
                    <?php if (isset($error)) { echo '<div class="error-message">' . $error . '</div>'; } ?>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

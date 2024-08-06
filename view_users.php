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

$users = $conn->query("SELECT * FROM users");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php include 'db-styles.php'; ?>
</head>

<body>
    <?php include 'db-sidebar.php'; ?>
    <div class="content">
        <span class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></span>
        <div class="users-list mt-4">
            <h3>List of Users</h3>
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
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['first_name']; ?></td>
                            <td><?php echo $user['last_name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['created_at']; ?></td>
                            <td><?php echo $user['updated_at']; ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-user" data-id="<?php echo $user['id']; ?>" data-first_name="<?php echo $user['first_name']; ?>" data-last_name="<?php echo $user['last_name']; ?>" data-email="<?php echo $user['email']; ?>">Edit</button>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <p class="text-right">Total Users: <?php echo $users->num_rows; ?></p>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" method="POST" action="add_user.php">
                        <input type="hidden" name="user_id" id="editUserId">
                        <div class="form-group">
                            <label for="editFirstName">First Name</label>
                            <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="editLastName">Last Name</label>
                            <input type="text" class="form-control" id="editLastName" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="editEmail">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="editPassword">Password (leave blank to keep current password)</label>
                            <input type="password" class="form-control" id="editPassword" name="password">
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
        // Edit User Modal
        $(document).on('click', '.edit-user', function() {
            const id = $(this).data('id');
            const firstName = $(this).data('first_name');
            const lastName = $(this).data('last_name');
            const email = $(this).data('email');

            $('#editUserId').val(id);
            $('#editFirstName').val(firstName);
            $('#editLastName').val(lastName);
            $('#editEmail').val(email);

            $('#editUserModal').modal('show');
        });
    </script>
</body>

</html>

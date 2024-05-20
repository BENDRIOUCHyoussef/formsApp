<?php
session_start();
require '../database.php';  // Adjust the path as necessary

// Check if the user is logged in and has admin or vendor access
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Vendor')) {
    header('Location: login.php');
    exit();
}

$error = '';  // To hold error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // Check if username or email already exists
        $check = $db->prepare("SELECT user_id FROM Users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $error = 'Username or Email already exists.';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $stmt = $db->prepare("INSERT INTO Users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
            if ($stmt->execute()) {
                header('Location: manage_users.php');  // Redirect to manage users page on success
                exit();
            } else {
                $error = 'Failed to create user.';
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>  <!-- Including the sidebar -->
    <main>
    <h1>Create New User</h1>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="create_user.php" method="post">
        Username: <input type="text" name="username" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        Role:
        <select name="role">
            <option value="Admin">Admin</option>
            <option value="Vendor">Vendor</option>
            <option value="Client">Client</option>
        </select><br>
        <button type="submit">Create User</button>
    </form>
    </main>
    </div>
</body>
</html>

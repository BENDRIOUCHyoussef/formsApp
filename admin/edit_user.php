<?php
session_start();
require '../database.php';

// Check user permissions and whether GET request contains a user ID
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin' || !isset($_GET['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_GET['user_id'];
$stmt = $db->prepare("SELECT username, email, role, status FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle POST request to update user details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $update_stmt = $db->prepare("UPDATE Users SET username = ?, email = ?, role = ?, status = ? WHERE user_id = ?");
    $update_stmt->bind_param("ssssi", $username, $email, $role, $status, $user_id);
    $update_stmt->execute();

    header('Location: manage_users.php');  // Redirect back to user management page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
<?php include 'sidebar.php'; ?>  <!-- Including the sidebar -->
    <main>
    <h1>Edit User</h1>
    <form method="POST">
        Username: <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>"><br>
        Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"><br>
        Role: <select name="role">
            <option <?= $user['role'] == 'Admin' ? 'selected' : '' ?> value="Admin">Admin</option>
            <option <?= $user['role'] == 'Vendor' ? 'selected' : '' ?> value="Vendor">Vendor</option>
            <option <?= $user['role'] == 'Client' ? 'selected' : '' ?> value="Client">Client</option>
        </select><br>
        Status: <select name="status">
            <option <?= $user['status'] == 'Active' ? 'selected' : '' ?> value="Active">Active</option>
            <option <?= $user['status'] == 'Inactive' ? 'selected' : '' ?> value="Inactive">Inactive</option>
        </select><br>
        <button type="submit">Update User</button>
    </form>
    </main>
</div>
</body>
</html>

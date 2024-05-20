<?php
session_start();
// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');  // Redirect non-admins to the login page
    exit();
}

require '../database.php'; // Database connection setup

// Handle filtering
$status_filter = $_GET['status_filter'] ?? '';
$query = "SELECT user_id, username, email, role, status FROM Users ";
if (!empty($status_filter)) {
    $query .= "WHERE status = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $status_filter);
} else {
    $stmt = $db->prepare($query);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="style.css"> <!-- Adjust the path as necessary -->
</head>
<body>
    <div class="dashboard-container">
    <?php include 'sidebar.php'; ?>  <!-- Including the sidebar -->
        <main>
            <h1>Manage Users</h1>
            <!-- Filter Form -->
            <div class="filter-container">
            <form method="GET" action="manage_users.php">
                <label for="status_filter">Filter by Status:</label>
                <select name="status_filter" id="status_filter">
                    <option value="">All Statuses</option>
                    <option value="Active" <?= $status_filter == 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= $status_filter == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
                <button type="submit">Filter</button>
            </form>
            <a href="create_user.php">Create User</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <a href="edit_user.php?user_id=<?= $row['user_id'] ?>">Edit</a> |
                            <a href="toggle_status.php?user_id=<?= $row['user_id'] ?>" onclick="return confirm('Are you sure?');">
                                <?= $row['status'] == 'Active' ? 'Deactivate' : 'Activate' ?>
                            </a> |
                            <a href="delete_user.php?user_id=<?= $row['user_id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>

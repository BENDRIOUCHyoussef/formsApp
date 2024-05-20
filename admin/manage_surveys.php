<?php
session_start();
require '../database.php'; // Ensure the path to your database.php is correct

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Vendor')) {
    header('Location: login.php');
    exit();
}

// Default sorting
$sort_order = $_GET['sort'] ?? 'created_at';
$sort_direction = $_GET['direction'] ?? 'DESC';

// Validate sort_order to prevent SQL injection
$valid_sort_orders = ['title', 'description', 'status', 'created_at'];
if (!in_array($sort_order, $valid_sort_orders)) {
    $sort_order = 'created_at';
}

// Query to fetch all surveys with dynamic ORDER BY
$query = "SELECT survey_id, title, description, status, created_at FROM Surveys ORDER BY $sort_order $sort_direction";
$result = $db->query($query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Surveys</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>  <!-- Including the sidebar -->
        <main>
            <h1>Manage Surveys</h1>
            <a href="create_survey.php">Create New Survey</a>
            <form action="manage_surveys.php" method="get">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="created_at" <?= $sort_order === 'created_at' ? 'selected' : '' ?>>Creation Date</option>
                    <option value="title" <?= $sort_order === 'title' ? 'selected' : '' ?>>Title</option>
                    <option value="status" <?= $sort_order === 'status' ? 'selected' : '' ?>>Status</option>
                </select>
                <select name="direction" id="direction" onchange="this.form.submit()">
                    <option value="ASC" <?= $sort_direction === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                    <option value="DESC" <?= $sort_direction === 'DESC' ? 'selected' : '' ?>>Descending</option>
                </select>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars(date("Y-m-d H:i", strtotime($row['created_at']))) ?></td>
                        <td>
                            <a href="edit_survey.php?survey_id=<?= $row['survey_id'] ?>">Edit</a> |
                            <a href="delete_survey.php?survey_id=<?= $row['survey_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a> |
                            <a href="view_survey.php?survey_id=<?= $row['survey_id'] ?>">View</a> |
                            <a href="toggle_survey_status.php?survey_id=<?= $row['survey_id'] ?>">
                                <?= $row['status'] == 'Active' ? 'Deactivate' : 'Activate' ?>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
    <footer>
        <p>&copy; <?= date("Y"); ?> Survey Tool. All rights reserved.</p>
    </footer>
</body>
</html>

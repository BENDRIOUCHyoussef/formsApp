<?php
session_start();
// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');  // Redirect non-admins to the login page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
    <?php include 'sidebar.php'; ?>  <!-- Including the sidebar -->
        <main>
            <section>
                <h2>Dashboard Overview</h2>
                <div>Welcome, Admin! Here's a quick overview:</div>
                <ul>
                    <li>Total Users: <!-- PHP code to count users --></li>
                    <li>Active Surveys: <!-- PHP code to count active surveys --></li>
                    <li>Survey Responses: <!-- PHP code to count responses --></li>
                </ul>
            </section>
        </main>
    </div>
</body>
</html>

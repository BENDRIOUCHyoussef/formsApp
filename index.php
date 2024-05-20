<?php
session_start();  // Start the session at the beginning of the script

// Function to check if the user is logged in and has a specific role
function isRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Our Survey Tool</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Survey Tool</h1>
        <nav>
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Show these links if the user is logged in -->
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <!-- Show these links if the user is not logged in -->
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <section>
            <h2>Welcome to Our Survey Tool</h2>
            <p>Engage with your clients or staff efficiently by creating, managing, and analyzing surveys.</p>
        </section>
    </main>
    <footer>
        <p>&copy; <?= date("Y"); ?> Survey Tool. All rights reserved.</p>
    </footer>
</body>
</html>

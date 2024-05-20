<?php
require 'database.php';  // Include your database connection settings

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $username = htmlspecialchars(stripslashes(trim($_POST['username'])));
    $email = htmlspecialchars(stripslashes(trim($_POST['email'])));
    $password = $_POST['password'];  // Get password directly to hash it
    $role = $_POST['role'];

    // Validate input data (basic validation)
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        die('Please fill all required fields!');
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to avoid SQL injection
    $stmt = $db->prepare("INSERT INTO Users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

    // Execute and check for success
    if ($stmt->execute()) {
        echo "Registered successfully!";
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and the database connection
    $stmt->close();
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Register for Survey Tool</h1>
    <form action="register.php" method="POST">
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="Client">Client</option>
                <option value="Vendor">Vendor</option>
                <option value="Admin">Admin</option>
            </select>
        </div>
        <button type="submit">Register</button>
    </form>
</body>
</html>

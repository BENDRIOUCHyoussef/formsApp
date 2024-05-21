<?php
session_start();
require 'database.php'; // Ensure this is the correct path to your database connection settings

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL to prevent SQL injection
    $stmt = $db->prepare("SELECT user_id, password, role, status FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $stored_password, $role, $status);
        $stmt->fetch();

        // Check if the account is active
        if ($status != 'Active') {
            echo "Your account is deactivated.";
        } else {
            // Verify the password
            if (password_verify($password, $stored_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role; // Store the user's role in the session

                // Redirect based on role
                switch ($role) {
                    case 'Admin':
                        header("Location: admin/index.php"); // Redirect to Admin dashboard
                        exit();
                    case 'Vendor':
                        header("Location: vendor/index.php"); // Redirect to Vendor dashboard
                        exit();
                    case 'Client':
                        $param = '1';
                        header("Location: customer/customerDashboard.php?survey_id=" . urlencode($param)); // Redirect Clients back to the homepage
                        exit();
                    default:
                        echo "Unexpected user role."; // Handle unexpected role
                        break;
                }
            } else {
                echo "Invalid username or password";
            }
        }
    } else {
        echo "Invalid username or password";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Login</h1>
    <form action="login.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</body>

</html>
<?php
session_start();
require '../database.php'; // Ensure the path to your database.php is correct

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Survey</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="dashboard-container">
        <main>
            <a href="../logout.php" class="logout-button">Logout</a>

            <h1>Survey submitted successfully</h1>
            <p>You'll get the money shortly!</p>



        </main>
    </div>
</body>

</html>
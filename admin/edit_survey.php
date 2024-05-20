<?php
session_start();
require '../database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Vendor')) {
    header('Location: login.php');
    exit();
}

// Get the survey ID from the query string
$survey_id = $_GET['survey_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    $stmt = $db->prepare("UPDATE Surveys SET title = ?, description = ?, status = ? WHERE survey_id = ?");
    $stmt->bind_param("sssi", $title, $description, $status, $survey_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: manage_surveys.php");
        exit();
    } else {
        echo "No changes made or error updating survey.";
    }
} else {
    // Fetch the current survey details to populate the form
    $stmt = $db->prepare("SELECT title, description, status FROM Surveys WHERE survey_id = ?");
    $stmt->bind_param("i", $survey_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $survey = $result->fetch_assoc();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Survey</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
<?php include 'sidebar.php'; ?>  <!-- Including the sidebar -->
    <main>
    <h1>Edit Survey</h1>
    <form method="post">
        Title: <input type="text" name="title" value="<?= htmlspecialchars($survey['title']) ?>"><br>
        Description: <textarea name="description"><?= htmlspecialchars($survey['description']) ?></textarea><br>
        Status: <select name="status">
            <option value="Active" <?= $survey['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
            <option value="Inactive" <?= $survey['status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
        </select><br>
        <button type="submit">Update Survey</button>
    </form>
    </main>
</div>
</body>
</html>

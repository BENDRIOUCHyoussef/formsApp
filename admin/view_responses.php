<?php
session_start();
require '../database.php';

// Ensure only admins can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: login.php');
    exit();
}

// Fetch all surveys with their response count
$surveys = [];
$stmt = $db->prepare("SELECT Surveys.survey_id, Surveys.title, COUNT(Responses.response_id) AS response_count FROM Surveys LEFT JOIN Responses ON Surveys.survey_id = Responses.survey_id GROUP BY Surveys.survey_id");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $surveys[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - View Responses</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        <main>
            <h1>Survey Responses</h1>
            <table>
                <thead>
                    <tr>
                        <th>Survey Title</th>
                        <th>Response Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($surveys as $survey): ?>
                        <tr>
                            <td><?= htmlspecialchars($survey['title']) ?></td>
                            <td><?= $survey['response_count'] ?></td>
                            <td><a href="view_survey_responses.php?survey_id=<?= $survey['survey_id'] ?>">View Details</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>

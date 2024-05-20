<?php
session_start();
require '../database.php'; // Ensure the path to your database.php is correct

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Vendor')) {
    header('Location: login.php');
    exit();
}

$survey_id = $_GET['survey_id'] ?? null;
if (!$survey_id) {
    echo "Invalid Survey ID.";
    exit();
}

// Fetch the survey details
$survey_query = $db->prepare("SELECT title, description FROM Surveys WHERE survey_id = ?");
$survey_query->bind_param("i", $survey_id);
$survey_query->execute();
$survey_result = $survey_query->get_result();
$survey = $survey_result->fetch_assoc();

if (!$survey) {
    echo "Survey not found.";
    exit();
}

// Fetch questions and their answers
$questions_query = $db->prepare("
    SELECT q.question_id, q.text AS question_text, a.text AS answer_text
    FROM Questions q
    LEFT JOIN Answers a ON q.question_id = a.question_id
    WHERE q.survey_id = ?
    ORDER BY q.question_id ASC, a.answer_id ASC
");
$questions_query->bind_param("i", $survey_id);
$questions_query->execute();
$result = $questions_query->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[$row['question_id']]['text'] = $row['question_text'];
    $questions[$row['question_id']]['answers'][] = $row['answer_text'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Survey</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
    <?php include 'sidebar.php'; ?>  <!-- Including the sidebar -->
        <main>
        <h1><?= htmlspecialchars($survey['title']) ?></h1>
    <p><?= htmlspecialchars($survey['description']) ?></p>

    <?php
    $question_number = 0;
    foreach ($questions as $question_id => $info): ?>
        <div>
            <h3>Question <?= ++$question_number ?>: <?= htmlspecialchars($info['text']) ?></h3>
            <ul>
                <?php foreach ($info['answers'] as $answer): ?>
                    <li><?= htmlspecialchars($answer) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
        </main>
    </div>
</body>
</html>

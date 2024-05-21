<?php
session_start();
require '../database.php'; // Adjust the path as necessary

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Vendor')) {
    header('Location: login.php');
    exit();
}

// Ensure a survey ID is present
$survey_id = $_GET['survey_id'] ?? null;
if (!$survey_id) {
    header("Location: manage_surveys.php");
    exit();
}

// Fetch the current number of questions for this survey
$query = $db->prepare("SELECT COUNT(*) as total FROM Questions WHERE survey_id = ?");
$query->bind_param("i", $survey_id);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$question_count = $row['total'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['question_text'])) {
    // Add new question if less than 10 are present
    if ($question_count < 10) {
        $question_text = $_POST['question_text'];
        $question_type = $_POST['question_type'];

        $stmt = $db->prepare("INSERT INTO Questions (survey_id, text, type) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $survey_id, $question_text, $question_type);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Check if we have now added 10 questions
            if (++$question_count >= 10) {
                header("Location: manage_surveys.php");
                exit();
            }
        } else {
            $error_message = "Error adding question: " . $db->error;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Questions</title>
</head>

<body>
    <h1>Add Questions to Survey #<?= $survey_id ?></h1>
    <?php if (!empty($error_message)) echo "<p>Error: $error_message</p>"; ?>
    <p>Question Count: <?= $question_count ?>/10</p>
    <?php if ($question_count < 10) : ?>
        <form method="post">
            Question Text: <input type="text" name="question_text" required><br>
            Question Type:
            <select name="question_type">
                <option value="Multiple_Choice">Multiple Choice</option>
                <option value="Text">Text</option>
                <option value="Rating">Rating</option>
            </select><br>
            <button type="submit">Add Question</button>
        </form>
    <?php else : ?>
        <p>All 10 questions added. <a href="manage_surveys.php">Return to Manage Surveys</a></p>
    <?php endif; ?>
</body>

</html>
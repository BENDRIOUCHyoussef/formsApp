<?php
session_start();
require '../database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['questions'])) {
    $title = $_POST['title'] ?? 'Untitled Survey';
    $description = $_POST['description'] ?? '';

    // Begin transaction
    $db->begin_transaction();
    try {
        $survey_stmt = $db->prepare("INSERT INTO Surveys (title, description) VALUES (?, ?)");
        $survey_stmt->bind_param("ss", $title, $description);
        $survey_stmt->execute();
        $survey_id = $db->insert_id;

        foreach ($_POST['questions'] as $question) {
            $q_text = $question['text'];
            $q_type = $question['type'];

            $question_stmt = $db->prepare("INSERT INTO Questions (survey_id, text, type) VALUES (?, ?, ?)");
            $question_stmt->bind_param("iss", $survey_id, $q_text, $q_type);
            $question_stmt->execute();
            $question_id = $db->insert_id;

            if (isset($question['options']) && is_array($question['options'])) {
                foreach ($question['options'] as $option) {
                    $option_stmt = $db->prepare("INSERT INTO Answers (question_id, text) VALUES (?, ?)");
                    $option_stmt->bind_param("is", $question_id, $option);
                    $option_stmt->execute();
                }
            }
        }

        $db->commit();
        header("Location: manage_surveys.php");
        exit();
    } catch (Exception $e) {
        $db->rollback();
        echo "Failed to create survey. Error: " . $e->getMessage();
    }
}
?>

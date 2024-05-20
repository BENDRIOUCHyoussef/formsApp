<?php
session_start();
require '../database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: login.php');
    exit();
}

$survey_id = $_GET['survey_id'] ?? null;

if ($survey_id) {
    // Start transaction
    $db->begin_transaction();

    try {
        // First, delete answers linked to questions of this survey
        $stmt = $db->prepare("DELETE FROM Answers WHERE question_id IN (SELECT question_id FROM Questions WHERE survey_id = ?)");
        $stmt->bind_param("i", $survey_id);
        $stmt->execute();

        // Next, delete the questions linked to the survey
        $stmt = $db->prepare("DELETE FROM Questions WHERE survey_id = ?");
        $stmt->bind_param("i", $survey_id);
        $stmt->execute();

        // Finally, delete the survey itself
        $stmt = $db->prepare("DELETE FROM Surveys WHERE survey_id = ?");
        $stmt->bind_param("i", $survey_id);
        $stmt->execute();

        // Commit transaction
        $db->commit();

        if ($stmt->affected_rows > 0) {
            header("Location: manage_surveys.php");
            exit();
        } else {
            echo "Failed to delete survey or no data found.";
        }
    } catch (Exception $e) {
        // An error occurred, rollback any changes
        $db->rollback();
        echo "Failed to delete survey. Error: " . $e->getMessage();
    }
}
?>

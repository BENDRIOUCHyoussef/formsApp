<?php
session_start();
require '../database.php';

// Security check to ensure the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: login.php');
    exit();
}

// Check if the export action has been requested
if (isset($_GET['export']) && $_GET['export'] == 'true') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="all_data_export.csv"');

    $output = fopen('php://output', 'w');

    // Column headers
    fputcsv($output, ['Survey ID', 'Survey Title', 'Question ID', 'Question Text', 'Answer ID', 'Answer Text', 'Response ID', 'User Response']);

    // Query to fetch data
    $stmt = $db->prepare("SELECT s.survey_id, s.title, q.question_id, q.text, a.answer_id, a.text, r.response_id, r.text_response FROM Surveys s LEFT JOIN Questions q ON s.survey_id = q.survey_id LEFT JOIN Answers a ON q.question_id = a.question_id LEFT JOIN Responses r ON a.answer_id = r.answer_id ORDER BY s.survey_id, q.question_id, a.answer_id, r.response_id");
    $stmt->execute();
    $result = $stmt->get_result();

    // Output the data
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}
?>

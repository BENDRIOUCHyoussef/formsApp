<?php
session_start();
require '../database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: login.php');
    exit();
}

$survey_id = $_GET['survey_id'] ?? null;

if ($survey_id) {
    $stmt = $db->prepare("UPDATE Surveys SET status = IF(status='Active', 'Inactive', 'Active') WHERE survey_id = ?");
    $stmt->bind_param("i", $survey_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: manage_surveys.php");
        exit();
    } else {
        echo "Failed to toggle survey status.";
    }
}

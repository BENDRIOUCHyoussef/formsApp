<?php
session_start();
require '../database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin' || !isset($_GET['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_GET['user_id'];
// Delete the user
$stmt = $db->prepare("DELETE FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

header('Location: manage_users.php');
exit();
?>

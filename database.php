<?php
// Database configuration settings
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'formsdb'; 

// Create a new MySQLi connection
$db = new mysqli($host, $username, $password, $database);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Optional: Set the charset to utf8mb4 for full Unicode support
$db->set_charset("utf8mb4");
?>

<?php
<<<<<<< HEAD
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli("localhost", "root", "", "expense_tracker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
=======
$conn = new mysqli("localhost","root","","expense_tracker");
if($conn->connect_error) die("DB Error");
session_start();
?>
>>>>>>> dd2a3ec1827e6eebc1fea3dd9878270b549aa490

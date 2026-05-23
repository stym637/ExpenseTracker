<?php
include("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_POST['add'])) {
    $uid = (int) $_SESSION['user_id'];
    $name = trim($_POST['name'] ?? '');
    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO categories(user_id, name) VALUES(?, ?)");
        $stmt->bind_param("is", $uid, $name);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: ../categories.php");
    exit;
}
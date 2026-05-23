<?php
include("../config/db.php");

<<<<<<< HEAD
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
=======
if(isset($_POST['add'])){
$conn->query("INSERT INTO categories(user_id,name) VALUES({$_SESSION['user_id']},'{$_POST['name']}')");
header("Location: ../categories.php");
>>>>>>> dd2a3ec1827e6eebc1fea3dd9878270b549aa490
}
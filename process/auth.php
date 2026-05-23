<?php
include("../config/db.php");

if (isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $check = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $exists = $check->get_result()->fetch_assoc();
    $check->close();

    if ($exists) {
        header("Location: ../auth/register.php");
        exit;
    }

    $pass = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users(name, email, password) VALUES(?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $pass);
    $stmt->execute();
    $id = (int) $conn->insert_id;
    $stmt->close();

    $cats = ['Food', 'Transport', 'Health', 'Utilities'];
    $stmtCat = $conn->prepare("INSERT INTO categories(user_id, name) VALUES(?, ?)");
    foreach ($cats as $c) {
        $stmtCat->bind_param("is", $id, $c);
        $stmtCat->execute();
    }
    $stmtCat->close();

    header("Location: ../auth/login.php");
    exit;
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($u && password_verify($password, $u['password'])) {
        $_SESSION['user_id'] = (int) $u['id'];
        $_SESSION['user_name'] = trim((string) ($u['name'] ?? ''));
        header("Location: ../dashboard.php");
        exit;
    }

    header("Location: ../auth/login.php");
    exit;
}
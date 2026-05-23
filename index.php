<?php
require_once "config/db.php";

if (isset($_SESSION['user_id'])) {
    // Verify the session user still exists in the DB (guards against stale sessions)
    $uid = (int) $_SESSION['user_id'];
    $check = $conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
    $check->bind_param("i", $uid);
    $check->execute();
    $check->store_result();
    $exists = $check->num_rows > 0;
    $check->close();

    if ($exists) {
        header("Location: dashboard.php");
        exit;
    }

    // Stale session — destroy it and fall through to login
    session_destroy();
}

header("Location: auth/login.php");
exit;

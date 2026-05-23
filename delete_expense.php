<?php
include("config/db.php");
include("includes/auth_guard.php");

$id = (int) ($_GET['id'] ?? 0);
header("Location: process/expenses.php?delete=" . $id);
exit;
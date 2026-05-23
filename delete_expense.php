<<<<<<< HEAD
<?php
include("config/db.php");
include("includes/auth_guard.php");

$id = (int) ($_GET['id'] ?? 0);
header("Location: process/expenses.php?delete=" . $id);
exit;
=======
<?php header("Location: process/expenses.php?delete=".$_GET['id']); ?>
>>>>>>> dd2a3ec1827e6eebc1fea3dd9878270b549aa490

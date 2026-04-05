<?php include("../config/db.php"); ?>
<form action="../process/auth.php" method="POST">
<input name="email" required>
<input type="password" name="password" required>
<button name="login">Login</button>
</form>
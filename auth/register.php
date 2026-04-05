<?php include("../config/db.php"); ?>
<form action="../process/auth.php" method="POST">
<input name="name" placeholder="Name" required>
<input name="email" required>
<input type="password" name="password" required>
<button name="register">Register</button>
</form>
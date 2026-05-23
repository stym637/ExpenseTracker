<?php include("../config/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
<title>Register - Expense Tracker</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-container">
<div class="auth-card">

<div class="auth-head">
<span class="auth-kicker">Expense Tracker</span>
<h2>Create Account</h2>
<p class="sub">Start tracking expenses with a cleaner money routine.</p>
</div>

<form action="../process/auth.php" method="POST">
<input name="name" placeholder="Name" required>
<input name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button name="register" class="btn">Register</button>
</form>

<p class="auth-switch">
Already have an account? <a href="login.php">Login</a>
</p>

</div>
</div>

</body>
</html>
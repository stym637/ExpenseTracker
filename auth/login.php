<?php include("../config/db.php"); ?>
<<<<<<< HEAD
<!DOCTYPE html>
<html>
<head>
<title>Login - Expense Tracker</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-container">
<div class="auth-card">

<div class="auth-head">
<span class="auth-kicker">Expense Tracker</span>
<h2>Welcome Back</h2>
<p class="sub">Sign in to view your money dashboard.</p>
</div>

<form action="../process/auth.php" method="POST">

<input name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>

<button name="login" class="btn">Login</button>

</form>

<p class="auth-switch">
No account? <a href="register.php">Register</a>
</p>

</div>
</div>

</body>
</html>
=======
<form action="../process/auth.php" method="POST">
<input name="email" required>
<input type="password" name="password" required>
<button name="login">Login</button>
</form>
>>>>>>> dd2a3ec1827e6eebc1fea3dd9878270b549aa490

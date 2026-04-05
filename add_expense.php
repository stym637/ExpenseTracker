<?php include("config/db.php"); include("includes/header.php"); include("includes/sidebar.php"); ?>

<div class="main">
<h1>Add Expense</h1>

<form action="http://localhost/ExpenseTracker/process/expenses.php" method="POST" class="form">

<input type="number" name="amount" placeholder="Amount" required>
<input type="date" name="date" required>

<select name="category_id">
<?php
$q=$conn->query("SELECT * FROM categories WHERE user_id=".$_SESSION['user_id']);
while($c=$q->fetch_assoc()){
echo "<option value='{$c['id']}'>{$c['name']}</option>";
}
?>
</select>

<textarea name="description" placeholder="Description"></textarea>

<button name="add">Save Expense</button>

</form>
</div>

<?php include("includes/footer.php"); ?>
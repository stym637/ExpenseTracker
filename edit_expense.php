<?php include("config/db.php"); include("includes/header.php"); include("includes/sidebar.php");
$id=$_GET['id'];
$e=$conn->query("SELECT * FROM expenses WHERE id=$id")->fetch_assoc();
?>

<div class="main">
<form action="process/expenses.php" method="POST" class="form">

<input type="hidden" name="id" value="<?php echo $e['id']; ?>">
<input name="amount" value="<?php echo $e['amount']; ?>">
<input type="date" name="date" value="<?php echo $e['expense_date']; ?>">

<button name="update">Update</button>

</form>
</div>
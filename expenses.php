<?php include("config/db.php"); include("includes/header.php"); include("includes/sidebar.php"); ?>

<div class="main">
<h1>Expenses</h1>

<table>
<?php
$q=$conn->query("SELECT e.*,c.name FROM expenses e JOIN categories c ON e.category_id=c.id");
while($r=$q->fetch_assoc()){
echo "<tr>
<td>{$r['expense_date']}</td>
<td>{$r['name']}</td>
<td>{$r['amount']}</td>
<td>
<a href='edit_expense.php?id={$r['id']}'>Edit</a>
<a href='delete_expense.php?id={$r['id']}'>Delete</a>
</td>
</tr>";
}
?>
</table>
</div>
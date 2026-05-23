<<<<<<< HEAD
<?php
include("config/db.php");
include("includes/auth_guard.php");
include("includes/header.php");
include("includes/sidebar.php");

$uid = (int) $_SESSION['user_id'];
$id = (int) ($_GET['id'] ?? 0);
$hasExpenseDateCol = $conn->query("SHOW COLUMNS FROM expenses LIKE 'expense_date'")->num_rows > 0;
$hasNameCol = $conn->query("SHOW COLUMNS FROM expenses LIKE 'name'")->num_rows > 0;
$dateColumn = $hasExpenseDateCol ? "expense_date" : "date";

$e = $conn->query("SELECT *, `$dateColumn` AS expense_date_view FROM expenses WHERE id=$id AND user_id=$uid")->fetch_assoc();
if (!$e) {
    header("Location: expenses.php");
    exit;
}

$categories = $conn->query("SELECT id, name FROM categories WHERE user_id=$uid ORDER BY name ASC");
?>

<div class="main">
    <div class="page-header">
        <h1>Edit Expense</h1>
        <p>Update transaction details.</p>
    </div>

    <div class="center">
        <form action="process/expenses.php" method="POST" class="form">
            <input type="hidden" name="id" value="<?php echo (int) $e['id']; ?>">

            <div class="form-group">
                <label>Expense Name</label>
                <input name="name" value="<?php echo htmlspecialchars($hasNameCol ? ($e['name'] ?? '') : ''); ?>" <?php echo $hasNameCol ? 'required' : ''; ?>>
            </div>

            <div class="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" min="0.01" name="amount" value="<?php echo htmlspecialchars($e['amount']); ?>" required>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" value="<?php echo htmlspecialchars($e['expense_date_view']); ?>" required>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id" required>
                    <?php while ($c = $categories->fetch_assoc()) { ?>
                        <option value="<?php echo (int) $c['id']; ?>" <?php echo ((int) $e['category_id'] === (int) $c['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"><?php echo htmlspecialchars($e['description'] ?? ''); ?></textarea>
            </div>

            <button name="update" class="btn">Update Expense</button>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>
=======
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
>>>>>>> dd2a3ec1827e6eebc1fea3dd9878270b549aa490

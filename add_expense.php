<<<<<<< HEAD
<?php
include("config/db.php");
include("includes/auth_guard.php");
include("includes/header.php");
include("includes/sidebar.php");

$uid = (int) $_SESSION['user_id'];
$q = $conn->query("SELECT id, name FROM categories WHERE user_id=$uid ORDER BY name ASC");
?>

<div class="main">
    <div class="page-header">
        <h1>Add Expense</h1>
        <p>Create a new expense transaction.</p>
    </div>

    <div class="center">
        <form action="process/expenses.php" method="POST" class="form">
            <div class="form-group">
                <label>Expense Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" min="0.01" name="amount" required>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" required>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id" id="category" required>
                    <?php while ($c = $q->fetch_assoc()) { ?>
                        <option value="<?php echo (int) $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                    <?php } ?>
                    <option value="other">+ Add New Category</option>
                </select>
            </div>

            <div class="form-group" id="newCategoryWrap" style="display:none;">
                <label>New Category Name</label>
                <input type="text" name="new_category" id="newCategory" placeholder="e.g. Utilities">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Optional details"></textarea>
            </div>

            <button name="add" class="btn">Save Expense</button>
        </form>
    </div>
</div>

<script>
const categorySelect = document.getElementById("category");
const newCategoryWrap = document.getElementById("newCategoryWrap");
const newCategoryInput = document.getElementById("newCategory");

categorySelect.addEventListener("change", function () {
    if (this.value === "other") {
        newCategoryWrap.style.display = "block";
        newCategoryInput.required = true;
    } else {
        newCategoryWrap.style.display = "none";
        newCategoryInput.required = false;
        newCategoryInput.value = "";
    }
});
</script>

=======
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

>>>>>>> dd2a3ec1827e6eebc1fea3dd9878270b549aa490
<?php include("includes/footer.php"); ?>
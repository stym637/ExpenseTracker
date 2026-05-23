<?php
include("config/db.php");
include("includes/auth_guard.php");
include("includes/header.php");
include("includes/sidebar.php");

$uid = (int) $_SESSION['user_id'];
$items = $conn->query("SELECT id, name FROM categories WHERE user_id=$uid ORDER BY name ASC");
?>

<div class="main">
    <div class="page-header">
        <h1>Categories</h1>
        <p>Add custom categories for your expenses.</p>
    </div>

    <div class="center">
        <form action="process/category.php" method="POST" class="form">
            <div class="form-group">
                <label>Category Name</label>
                <input name="name" placeholder="e.g. Utilities" required>
            </div>
            <button name="add" class="btn">Add Category</button>
        </form>
    </div>

    <div style="margin-top:24px;">
        <table>
            <thead><tr><th>Category</th></tr></thead>
            <tbody>
                <?php while ($row = $items->fetch_assoc()) { ?>
                    <tr><td><?php echo htmlspecialchars($row['name']); ?></td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>
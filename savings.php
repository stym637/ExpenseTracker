<?php
include("config/db.php");
include("includes/auth_guard.php");
include("includes/header.php");
include("includes/sidebar.php");

$uid = (int) $_SESSION['user_id'];

// Detect actual column names in the savings table
$hasSaveDateCol   = $conn->query("SHOW COLUMNS FROM savings LIKE 'save_date'")->num_rows > 0;
$hasDateCol       = $conn->query("SHOW COLUMNS FROM savings LIKE 'date'")->num_rows > 0;
$saveDateColumn   = $hasSaveDateCol ? "save_date" : ($hasDateCol ? "date" : null);

$hasSavingsDescCol = $conn->query("SHOW COLUMNS FROM savings LIKE 'description'")->num_rows > 0;

// If the date column is missing entirely, add it now
if (!$saveDateColumn) {
    $conn->query("ALTER TABLE savings ADD COLUMN save_date DATE NOT NULL DEFAULT (CURRENT_DATE)");
    $saveDateColumn = "save_date";
}

// If the description column is missing, add it now
if (!$hasSavingsDescCol) {
    $conn->query("ALTER TABLE savings ADD COLUMN description TEXT NULL");
    $hasSavingsDescCol = true;
}

if (isset($_POST['add'])) {
    $amount = (float) ($_POST['amount'] ?? 0);
    $date = $_POST['date'] ?? '';
    $desc = trim($_POST['desc'] ?? '');

    $stmt = $conn->prepare("INSERT INTO savings(user_id, amount, description, `$saveDateColumn`) VALUES(?, ?, ?, ?)");
    $stmt->bind_param("idss", $uid, $amount, $desc, $date);
    $stmt->execute();
    $stmt->close();
    header("Location: savings.php");
    exit;
}

$descSelectSav = $hasSavingsDescCol ? "description" : "NULL AS description";
$items = $conn->query("SELECT amount, $descSelectSav, `$saveDateColumn` AS save_date FROM savings WHERE user_id=$uid ORDER BY `$saveDateColumn` DESC, id DESC");
?>

<div class="main">
    <div class="page-header">
        <h1>Savings</h1>
        <p>Record how much you save over time.</p>
    </div>

    <div class="center">
        <form method="POST" class="form">
            <div class="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" min="0.01" name="amount" required>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="desc" placeholder="Optional note"></textarea>
            </div>
            <button name="add" class="btn">Add Saving</button>
        </form>
    </div>

    <div style="margin-top:24px;">
        <table>
            <thead>
                <tr><th>Date</th><th>Description</th><th>Amount</th></tr>
            </thead>
            <tbody>
                <?php while ($row = $items->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['save_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
                        <td>Rs. <?php echo number_format((float) $row['amount'], 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>
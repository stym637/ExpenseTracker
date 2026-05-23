<?php
include("config/db.php");
include("includes/auth_guard.php");
include("includes/header.php");
include("includes/sidebar.php");

$uid = (int) $_SESSION['user_id'];

/* Verify user exists */
$userCheck = $conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
$userCheck->bind_param("i", $uid);
$userCheck->execute();
$userCheck->store_result();

if ($userCheck->num_rows === 0) {
    $userCheck->close();
    session_destroy();
    header("Location: auth/login.php");
    exit;
}
$userCheck->close();

$error = '';
$selectedMonth = $_GET['month'] ?? date('Y-m');

if (!preg_match('/^\d{4}\-\d{2}$/', $selectedMonth)) {
    $selectedMonth = date('Y-m');
}

$selectedMonthBudget = 0.0;
$migrationError = '';

/* Check if created_at exists */
$hasCreatedAt = $conn->query("SHOW COLUMNS FROM budgets LIKE 'created_at'")->num_rows > 0;

/* Add unique key for one budget per month */
$indexCheck = $conn->query("SHOW INDEX FROM budgets WHERE Key_name = 'uniq_user_month'");
if ($indexCheck && $indexCheck->num_rows === 0) {

    $dedupeSql = "
        DELETE b1
        FROM budgets b1
        INNER JOIN budgets b2
            ON b1.user_id = b2.user_id
            AND b1.month = b2.month
            AND b1.id < b2.id
    ";

    if (!$conn->query($dedupeSql)) {
        $migrationError = "Could not clean duplicate budgets: " . htmlspecialchars($conn->error);
    } else {
        if (!$conn->query("ALTER TABLE budgets ADD UNIQUE KEY uniq_user_month (user_id, month)")) {
            $migrationError = "Could not enforce unique monthly budget rule: " . htmlspecialchars($conn->error);
        }
    }
}

/* SAVE / UPDATE BUDGET */
if (isset($_POST['save'])) {

    $month = $_POST['month'] ?? '';
    $amount = (float) ($_POST['amount'] ?? 0);

    if ($month === '' || $amount <= 0) {
        $error = "Please provide a valid month and amount.";
    } else {

        $upsertStmt = $conn->prepare("
            INSERT INTO budgets(user_id, month, amount)
            VALUES(?, ?, ?)
            ON DUPLICATE KEY UPDATE amount = VALUES(amount)
        ");

        $upsertStmt->bind_param("isd", $uid, $month, $amount);

        if (!$upsertStmt->execute()) {
            $error = "Could not save budget: " . htmlspecialchars($upsertStmt->error);
        }

        $upsertStmt->close();

        if ($error === '') {
            header("Location: budget.php?month=" . urlencode($month));
            exit;
        }
    }
}

/* Selected month budget */
$monthBudgetStmt = $conn->prepare("
    SELECT amount 
    FROM budgets 
    WHERE user_id=? AND month=? 
    ORDER BY id DESC 
    LIMIT 1
");

$monthBudgetStmt->bind_param("is", $uid, $selectedMonth);
$monthBudgetStmt->execute();

$monthBudgetRow = $monthBudgetStmt->get_result()->fetch_assoc();
$selectedMonthBudget = (float) ($monthBudgetRow['amount'] ?? 0);

$monthBudgetStmt->close();

/* Budget list */
if ($hasCreatedAt) {
    $items = $conn->query("
        SELECT month, amount, created_at
        FROM budgets
        WHERE user_id=$uid
        ORDER BY month DESC, id DESC
    ");
} else {
    $items = $conn->query("
        SELECT month, amount
        FROM budgets
        WHERE user_id=$uid
        ORDER BY month DESC, id DESC
    ");
}
?>

<div class="main">

    <div class="page-header">
        <h1>Budget</h1>
        <p>Plan budgets month by month and update any month anytime.</p>
    </div>

    <?php if ($error !== ''): ?>
        <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($migrationError !== ''): ?>
        <div class="error-msg"><?php echo $migrationError; ?></div>
    <?php endif; ?>

    <!-- Month Viewer -->
    <div class="month-toolbar card">
        <form method="GET" class="month-form">
            <label for="budgetMonthView">View Month</label>
            <input 
                type="month" 
                id="budgetMonthView" 
                name="month" 
                value="<?php echo htmlspecialchars($selectedMonth); ?>">

            <button type="submit" class="btn btn-inline">Open</button>
        </form>

        <p class="month-summary">
            Selected month budget:
            <strong>Rs. <?php echo number_format($selectedMonthBudget, 2); ?></strong>
        </p>
    </div>

    <!-- Budget Form -->
    <div class="center">
        <form method="POST" class="form">

            <div class="form-group">
                <label>Month</label>
                <input 
                    type="month" 
                    name="month" 
                    value="<?php echo htmlspecialchars($selectedMonth); ?>" 
                    required>
            </div>

            <div class="form-group">
                <label>Amount</label>
                <input 
                    type="number" 
                    step="0.01" 
                    min="0.01" 
                    name="amount" 
                    required>
            </div>

            <button name="save" class="btn">
                Save / Update Budget
            </button>

        </form>
    </div>

    <!-- Budget History -->
    <div style="margin-top:24px;">
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Amount</th>
                    <?php if ($hasCreatedAt): ?>
                        <th>Saved On</th>
                    <?php endif; ?>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = $items->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['month']); ?></td>

                        <td>
                            Rs. <?php echo number_format((float) $row['amount'], 2); ?>
                        </td>

                        <?php if ($hasCreatedAt): ?>
                            <td><?php echo htmlspecialchars($row['created_at'] ?? ''); ?></td>
                        <?php endif; ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<?php include("includes/footer.php"); ?>
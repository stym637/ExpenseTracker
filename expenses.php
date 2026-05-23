<?php
include("config/db.php");
include("includes/auth_guard.php");
include("includes/header.php");
include("includes/sidebar.php");

$uid = (int) $_SESSION['user_id'];

/* Detect correct date column */
$hasExpenseDateCol = $conn->query("SHOW COLUMNS FROM expenses LIKE 'expense_date'")->num_rows > 0;
$dateColumn = $hasExpenseDateCol ? "expense_date" : "date";

/* Detect description column */
$hasDescCol = $conn->query("SHOW COLUMNS FROM expenses LIKE 'description'")->num_rows > 0;
$descSelect = $hasDescCol ? "e.description" : "NULL AS description";

/* FILTER VALUES */
$singleDate = $_GET['single_date'] ?? '';
$fromDate   = $_GET['from_date'] ?? '';
$toDate     = $_GET['to_date'] ?? '';

$where = "WHERE e.user_id = ?";
$params = [$uid];
$types = "i";

/* Single exact date filter */
if (!empty($singleDate)) {
    $where .= " AND e.`$dateColumn` = ?";
    $params[] = $singleDate;
    $types .= "s";
} else {
    /* Date range filter */
    if (!empty($fromDate)) {
        $where .= " AND e.`$dateColumn` >= ?";
        $params[] = $fromDate;
        $types .= "s";
    }

    if (!empty($toDate)) {
        $where .= " AND e.`$dateColumn` <= ?";
        $params[] = $toDate;
        $types .= "s";
    }
}

/* MAIN QUERY */
$sql = "
    SELECT 
        e.id,
        e.`$dateColumn` AS expense_date,
        e.amount,
        $descSelect,
        c.name AS category_name
    FROM expenses e
    JOIN categories c ON e.category_id = c.id
    $where
    ORDER BY e.`$dateColumn` DESC, e.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$q = $stmt->get_result();

/* TOTAL QUERY */
$totalSql = "
    SELECT COALESCE(SUM(e.amount), 0) AS total_amount
    FROM expenses e
    $where
";

$totalStmt = $conn->prepare($totalSql);
$totalStmt->bind_param($types, ...$params);
$totalStmt->execute();
$totalResult = $totalStmt->get_result()->fetch_assoc();
$totalAmount = (float) ($totalResult['total_amount'] ?? 0);
?>

<div class="main">
    <div class="page-header">
        <h1>Expenses</h1>
        <p>Track and manage all your transactions by date.</p>
    </div>

    <!-- DATE FILTER -->
    <div class="card" style="margin-bottom:20px;">
        <form method="GET" class="filter-form">

            <div>
                <label>Single Date</label>
                <input 
                    type="date" 
                    name="single_date" 
                    value="<?php echo htmlspecialchars($singleDate); ?>">
            </div>

            <div>
                <label>From Date</label>
                <input 
                    type="date" 
                    name="from_date" 
                    value="<?php echo htmlspecialchars($fromDate); ?>">
            </div>

            <div>
                <label>To Date</label>
                <input 
                    type="date" 
                    name="to_date" 
                    value="<?php echo htmlspecialchars($toDate); ?>">
            </div>

            <div>
                <button type="submit" class="btn">Filter</button>
            </div>

            <div>
                <a href="expenses.php" class="btn reset-btn">Reset</a>
            </div>

        </form>
    </div>

    <!-- TOTAL SUMMARY -->
    <div class="card" style="margin-bottom:20px;">
        <h3>Total Spending</h3>
        <p style="font-size:28px; font-weight:800;">
            Rs. <?php echo number_format($totalAmount, 2); ?>
        </p>
    </div>

    <!-- TRANSACTION TABLE -->
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($q->num_rows > 0) { ?>
                <?php while ($r = $q->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['expense_date']); ?></td>
                        <td><?php echo htmlspecialchars($r['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($r['description'] ?? ''); ?></td>
                        <td>Rs. <?php echo number_format((float) $r['amount'], 2); ?></td>
                        <td class="actions">
                            <a href="edit_expense.php?id=<?php echo (int) $r['id']; ?>" class="link-btn">
                                Edit
                            </a>

                            <a href="delete_expense.php?id=<?php echo (int) $r['id']; ?>" 
                               class="link-btn danger"
                               onclick="return confirm('Delete this expense?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="5" style="text-align:center;">
                        No transactions found for selected date.
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
$stmt->close();
$totalStmt->close();
include("includes/footer.php");
?>
<<<<<<< HEAD
<?php
include("config/db.php");
include("includes/auth_guard.php");
include("includes/header.php");
include("includes/sidebar.php");

$uid = (int) $_SESSION['user_id'];
$selectedMonth = $_GET['month'] ?? date('Y-m');
if (!preg_match('/^\d{4}\-\d{2}$/', $selectedMonth)) {
    $selectedMonth = date('Y-m');
}

$monthStart = $selectedMonth . '-01';
$monthEnd = date('Y-m-t', strtotime($monthStart));
$displayMonth = date('F Y', strtotime($monthStart));

$hasExpenseDateCol = $conn->query("SHOW COLUMNS FROM expenses LIKE 'expense_date'")->num_rows > 0;
$expenseDateColumn = $hasExpenseDateCol ? "expense_date" : "date";

$hasSaveDateCol = $conn->query("SHOW COLUMNS FROM savings LIKE 'save_date'")->num_rows > 0;
$hasSavingsDateCol = $conn->query("SHOW COLUMNS FROM savings LIKE 'date'")->num_rows > 0;
$savingsDateColumn = $hasSaveDateCol ? "save_date" : ($hasSavingsDateCol ? "date" : null);

$total = 0.0;
$count = 0;
$expensesStmt = $conn->prepare("SELECT COALESCE(SUM(amount),0) t, COUNT(*) c FROM expenses WHERE user_id=? AND `$expenseDateColumn` BETWEEN ? AND ?");
$expensesStmt->bind_param("iss", $uid, $monthStart, $monthEnd);
$expensesStmt->execute();
$expensesStats = $expensesStmt->get_result()->fetch_assoc();
$total = (float) ($expensesStats['t'] ?? 0);
$count = (int) ($expensesStats['c'] ?? 0);
$expensesStmt->close();

$budget = 0.0;
$budgetStmt = $conn->prepare("SELECT amount FROM budgets WHERE user_id=? AND month=? ORDER BY id DESC LIMIT 1");
$budgetStmt->bind_param("is", $uid, $selectedMonth);
$budgetStmt->execute();
$budgetRow = $budgetStmt->get_result()->fetch_assoc();
$budget = (float) ($budgetRow['amount'] ?? 0);
$budgetStmt->close();

$savings = 0.0;
if ($savingsDateColumn !== null) {
    $savingsStmt = $conn->prepare("SELECT COALESCE(SUM(amount),0) s FROM savings WHERE user_id=? AND `$savingsDateColumn` BETWEEN ? AND ?");
    $savingsStmt->bind_param("iss", $uid, $monthStart, $monthEnd);
    $savingsStmt->execute();
    $savingsStats = $savingsStmt->get_result()->fetch_assoc();
    $savings = (float) ($savingsStats['s'] ?? 0);
    $savingsStmt->close();
}

$transactions = [];
$receiptStmt = $conn->prepare("
    SELECT e.`$expenseDateColumn` AS expense_date, e.amount, e.description, c.name AS category_name
    FROM expenses e
    JOIN categories c ON e.category_id = c.id
    WHERE e.user_id = ? AND e.`$expenseDateColumn` BETWEEN ? AND ?
    ORDER BY e.`$expenseDateColumn` DESC, e.id DESC
");
$receiptStmt->bind_param("iss", $uid, $monthStart, $monthEnd);
$receiptStmt->execute();
$receiptRes = $receiptStmt->get_result();
while ($row = $receiptRes->fetch_assoc()) {
    $transactions[] = $row;
}
$receiptStmt->close();

$balance = $budget - $total - $savings;
?>

<div class="main">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Overview for <?php echo htmlspecialchars($displayMonth); ?>.</p>
    </div>

    <div class="month-toolbar card">
        <form method="GET" class="month-form" id="monthForm">
            <label for="monthPicker">Tracking Month</label>
            <input type="month" id="monthPicker" name="month" value="<?php echo htmlspecialchars($selectedMonth); ?>">
            <button type="submit" class="btn btn-inline" id="applyMonthBtn">Apply</button>
        </form>
    </div>

    <div class="grid">
        <div class="card card-budget">
            <div class="card-icon">💰</div>
            <h3>Current Budget</h3>
            <p>Rs. <?php echo number_format($budget, 2); ?></p>
        </div>
        <div class="card card-expenses card-clickable" id="expensesCard" role="button" tabindex="0" aria-expanded="false" aria-controls="receiptPanel">
            <div class="card-icon">🛒</div>
            <h3>Total Expenses</h3>
            <p>Rs. <?php echo number_format($total, 2); ?></p>
            <span class="card-sub"><?php echo $count; ?> transaction<?php echo $count !== 1 ? 's' : ''; ?> (click for receipt)</span>
        </div>
        <div class="card card-savings">
            <div class="card-icon">🏦</div>
            <h3>Total Savings</h3>
            <p>Rs. <?php echo number_format($savings, 2); ?></p>
        </div>
        <div class="card <?php echo $balance >= 0 ? 'card-balance-pos' : 'card-balance-neg'; ?>">
            <div class="card-icon"><?php echo $balance >= 0 ? '✅' : '⚠️'; ?></div>
            <h3>Current Balance</h3>
            <p>Rs. <?php echo number_format(abs($balance), 2); ?></p>
            <span class="card-sub balance-tag <?php echo $balance >= 0 ? 'pos' : 'neg'; ?>">
                <?php echo $balance >= 0 ? 'Budget - Expenses - Savings' : 'Over budget!'; ?>
            </span>
        </div>
    </div>

    <div class="card receipt-panel" id="receiptPanel" style="display:none;">
        <div class="receipt-head">
            <h3>Transaction Receipt - <?php echo htmlspecialchars($displayMonth); ?></h3>
            <button type="button" id="closeReceipt" class="btn btn-inline receipt-close">Close</button>
        </div>
        <?php if (count($transactions) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $t): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t['expense_date']); ?></td>
                            <td><?php echo htmlspecialchars($t['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($t['description'] ?? ''); ?></td>
                            <td>Rs. <?php echo number_format((float) $t['amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="empty-receipt">No transactions found for this month.</p>
        <?php endif; ?>
    </div>

    <?php if ($budget > 0): ?>
    <div class="budget-bar-wrap card" style="margin-top:28px;">
        <h3 style="margin-bottom:14px; font-size:13px; color:var(--muted);">Budget Utilisation (<?php echo htmlspecialchars($displayMonth); ?>)</h3>
        <?php
            $expPct     = $budget > 0 ? min(100, round($total   / $budget * 100, 1)) : 0;
            $savPct     = $budget > 0 ? min(100 - $expPct, round($savings / $budget * 100, 1)) : 0;
            $balPct     = max(0, 100 - $expPct - $savPct);
        ?>
        <div class="budget-bar">
            <div class="bar-seg bar-exp"   style="width:<?php echo $expPct; ?>%" title="Expenses <?php echo $expPct; ?>%"></div>
            <div class="bar-seg bar-sav"   style="width:<?php echo $savPct; ?>%" title="Savings <?php echo $savPct; ?>%"></div>
            <div class="bar-seg bar-bal"   style="width:<?php echo $balPct; ?>%" title="Remaining <?php echo $balPct; ?>%"></div>
        </div>
        <div class="bar-legend">
            <span><i class="dot dot-exp"></i> Expenses <?php echo $expPct; ?>%</span>
            <span><i class="dot dot-sav"></i> Savings <?php echo $savPct; ?>%</span>
            <span><i class="dot dot-bal"></i> Remaining <?php echo $balPct; ?>%</span>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
const monthForm = document.getElementById("monthForm");
const applyMonthBtn = document.getElementById("applyMonthBtn");
const expensesCard = document.getElementById("expensesCard");
const receiptPanel = document.getElementById("receiptPanel");
const closeReceipt = document.getElementById("closeReceipt");

function isReceiptHidden() {
    return receiptPanel.style.display === "none" || receiptPanel.style.display === "";
}

function showReceipt() {
    receiptPanel.style.display = "block";
    expensesCard.setAttribute("aria-expanded", "true");
}

function hideReceipt() {
    receiptPanel.style.display = "none";
    expensesCard.setAttribute("aria-expanded", "false");
}

if (monthForm && applyMonthBtn) {
    applyMonthBtn.addEventListener("click", function () {
        monthForm.submit();
    });
}

if (expensesCard && receiptPanel) {
    expensesCard.addEventListener("click", function () {
        if (isReceiptHidden()) {
            showReceipt();
        } else {
            hideReceipt();
        }
    });

    expensesCard.addEventListener("keydown", function (event) {
        if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            if (isReceiptHidden()) {
                showReceipt();
            } else {
                hideReceipt();
            }
        }
    });
}

if (closeReceipt && receiptPanel) {
    closeReceipt.addEventListener("click", function (event) {
        event.preventDefault();
        event.stopPropagation();
        hideReceipt();
    });
}
</script>

<?php include("includes/footer.php"); ?>
=======
<?php 
include("config/db.php");
if(!isset($_SESSION['user_id'])) header("Location: auth/login.php");

include("includes/header.php");
include("includes/sidebar.php");

$uid = $_SESSION['user_id'];

/* TOTAL EXPENSE */
$total = $conn->query("SELECT SUM(amount) as t FROM expenses WHERE user_id=$uid")->fetch_assoc()['t'] ?? 0;

/* TOTAL TRANSACTIONS */
$count = $conn->query("SELECT COUNT(*) as c FROM expenses WHERE user_id=$uid")->fetch_assoc()['c'];

/* CATEGORY CHART */
$q = $conn->query("
SELECT c.name, SUM(e.amount) total 
FROM expenses e 
JOIN categories c ON e.category_id=c.id 
WHERE e.user_id=$uid 
GROUP BY c.name
");

$labels=[]; 
$data=[];

while($row=$q->fetch_assoc()){
    $labels[] = $row['name'];
    $data[] = $row['total'];
}

/* MONTHLY */
$q2 = $conn->query("
SELECT DATE_FORMAT(expense_date,'%b') as m, SUM(amount) total 
FROM expenses 
WHERE user_id=$uid 
GROUP BY m
");

$months=[]; 
$monthData=[];

while($r=$q2->fetch_assoc()){
    $months[]=$r['m'];
    $monthData[]=$r['total'];
}
?>

<div class="main">

<div class="topbar">
<h1>Dashboard</h1>
</div>

<!-- CARDS -->
<div class="grid">
    <div class="card">
        <h3>Total Expense</h3>
        <p>Rs. <?php echo $total; ?></p>
    </div>

    <div class="card">
        <h3>Transactions</h3>
        <p><?php echo $count; ?></p>
    </div>

    <div class="card">
        <h3>This Month</h3>
        <p>Rs. <?php echo array_sum($monthData); ?></p>
    </div>
</div>

<!-- CHARTS -->
<div class="grid">

<div class="card">
<h3>Category Breakdown</h3>
<canvas id="pieChart"></canvas>
</div>

<div class="card">
<h3>Monthly Spending</h3>
<canvas id="barChart"></canvas>
</div>

</div>

</div>

<script>
new Chart(document.getElementById("pieChart"), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            data: <?php echo json_encode($data); ?>
        }]
    }
});

new Chart(document.getElementById("barChart"), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Expenses',
            data: <?php echo json_encode($monthData); ?>
        }]
    }
});
</script>

<?php include("includes/footer.php"); ?>
>>>>>>> dd2a3ec1827e6eebc1fea3dd9878270b549aa490

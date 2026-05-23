<?php
$currentPage = basename($_SERVER["PHP_SELF"]);
$displayName = trim((string) ($_SESSION['user_name'] ?? ''));

if ($displayName === '' && isset($conn) && isset($_SESSION['user_id'])) {
    $uid = (int) $_SESSION['user_id'];
    $nameStmt = $conn->prepare("SELECT name FROM users WHERE id=? LIMIT 1");
    if ($nameStmt) {
        $nameStmt->bind_param("i", $uid);
        $nameStmt->execute();
        $nameRow = $nameStmt->get_result()->fetch_assoc();
        $nameStmt->close();
        $displayName = trim((string) ($nameRow['name'] ?? ''));
        if ($displayName !== '') {
            $_SESSION['user_name'] = $displayName;
        }
    }
}

if ($displayName === '') {
    $displayName = 'Expense User';
}

$nameParts = preg_split('/\s+/', $displayName, -1, PREG_SPLIT_NO_EMPTY);
$initials = '';
if (!empty($nameParts)) {
    $initials .= strtoupper(substr($nameParts[0], 0, 1));
    if (count($nameParts) > 1) {
        $initials .= '.' . strtoupper(substr($nameParts[count($nameParts) - 1], 0, 1));
    }
}
if ($initials === '') {
    $initials = 'EU';
}
?>
<aside class="sidebar">
    <div class="brand">
        <span class="brand-mark"><?php echo htmlspecialchars($initials); ?></span>
        <div>
            <h2><?php echo htmlspecialchars($displayName); ?></h2>
            <small>Personal finance panel</small>
        </div>
    </div>

    <div class="nav-section">Overview</div>
    <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
        <span class="nav-emoji">🏠</span> Dashboard
    </a>
    <a href="reports.php" class="<?php echo $currentPage === 'reports.php' ? 'active' : ''; ?>">
        <span class="nav-emoji">📊</span> Reports
    </a>

    <div class="nav-section">Management</div>
    <a href="expenses.php" class="<?php echo $currentPage === 'expenses.php' ? 'active' : ''; ?>">
        <span class="nav-emoji">🧾</span> Expenses
    </a>
    <a href="add_expense.php" class="<?php echo $currentPage === 'add_expense.php' ? 'active' : ''; ?>">
        <span class="nav-emoji">➕</span> Add Expense
    </a>
    <a href="categories.php" class="<?php echo $currentPage === 'categories.php' ? 'active' : ''; ?>">
        <span class="nav-emoji">🏷️</span> Categories
    </a>
    <a href="budget.php" class="<?php echo $currentPage === 'budget.php' ? 'active' : ''; ?>">
        <span class="nav-emoji">🎯</span> Budget
    </a>
    <a href="savings.php" class="<?php echo $currentPage === 'savings.php' ? 'active' : ''; ?>">
        <span class="nav-emoji">🏦</span> Savings
    </a>

    <a href="auth/logout.php" class="logout-link">
        <span class="nav-emoji">🚪</span> Logout
    </a>
</aside>
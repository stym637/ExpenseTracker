<<<<<<< HEAD
<?php
include("config/db.php");
include("includes/auth_guard.php");
include("includes/header.php");
include("includes/sidebar.php");

$uid = (int) $_SESSION['user_id'];
$hasExpenseDateCol = $conn->query("SHOW COLUMNS FROM expenses LIKE 'expense_date'")->num_rows > 0;
$dateColumn = $hasExpenseDateCol ? "expense_date" : "date";

/* MONTHLY BAR DATA */
$dataArr = array_fill(1, 12, 0);
$q = $conn->query("SELECT MONTH(`$dateColumn`) as m, SUM(amount) total FROM expenses WHERE user_id=$uid GROUP BY m");
while ($row = $q->fetch_assoc()) {
    $dataArr[(int)$row['m']] = (float)$row['total'];
}
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

/* PIE CHART — expenses by category */
$pieLabels = [];
$pieData   = [];
$pieColors = ['#6366f1','#f59e0b','#10b981','#ef4444','#3b82f6','#ec4899','#14b8a6','#f97316','#8b5cf6','#84cc16'];
$pq = $conn->query("
    SELECT c.name, SUM(e.amount) total
    FROM expenses e
    JOIN categories c ON e.category_id = c.id
    WHERE e.user_id = $uid
    GROUP BY c.id, c.name
    ORDER BY total DESC
");
while ($row = $pq->fetch_assoc()) {
    $pieLabels[] = $row['name'];
    $pieData[]   = (float)$row['total'];
}
?>

<div class="main">
    <div class="page-header">
        <h1>Reports</h1>
        <p>Visual breakdown of your spending.</p>
    </div>

    <div class="reports-grid">
        <div class="card">
            <h3 class="chart-title">Monthly Expenses</h3>
            <canvas id="barChart"></canvas>
        </div>

        <div class="card">
            <h3 class="chart-title">Spending by Category</h3>
            <?php if (count($pieData) > 0): ?>
                <div class="pie-wrap">
                    <canvas id="pieChart"></canvas>
                </div>
            <?php else: ?>
                <p style="color:var(--muted); font-size:14px; margin-top:20px;">No categorised expenses yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById("barChart"), {
=======
<?php 
include("config/db.php");
include("includes/header.php");
include("includes/sidebar.php");

$uid = $_SESSION['user_id'];

/* GET MONTHLY DATA */
$dataArr = array_fill(1, 12, 0);

$q = $conn->query("
SELECT MONTH(expense_date) as m, SUM(amount) total 
FROM expenses 
WHERE user_id=$uid 
GROUP BY m
");

while($row = $q->fetch_assoc()){
    $dataArr[(int)$row['m']] = $row['total'];
}

$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
?>

<div class="main">

<div class="topbar">
<h1>Reports</h1>
</div>

<div class="card">
<h3>Monthly Expenses</h3>
<canvas id="reportChart"></canvas>
</div>

</div>

<script>
new Chart(document.getElementById("reportChart"), {
>>>>>>> dd2a3ec1827e6eebc1fea3dd9878270b549aa490
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
<<<<<<< HEAD
            label: 'Expenses (Rs.)',
            data: <?php echo json_encode(array_values($dataArr)); ?>,
            borderRadius: 8,
            backgroundColor: 'rgba(99,102,241,0.8)',
            hoverBackgroundColor: '#6366f1'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

<?php if (count($pieData) > 0): ?>
new Chart(document.getElementById("pieChart"), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($pieLabels); ?>,
        datasets: [{
            data: <?php echo json_encode($pieData); ?>,
            backgroundColor: <?php echo json_encode(array_slice($pieColors, 0, count($pieData))); ?>,
            borderWidth: 2,
            borderColor: '#fff',
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 16, font: { size: 12 }, usePointStyle: true }
            },
            tooltip: {
                callbacks: {
                    label: ctx => ' Rs. ' + ctx.parsed.toLocaleString('en-IN', {minimumFractionDigits: 2})
                }
            }
        }
    }
});
<?php endif; ?>
</script>

<?php include("includes/footer.php"); ?>
=======
            label: 'Expenses',
            data: <?php echo json_encode(array_values($dataArr)); ?>,
            borderRadius: 8
        }]
    }
});
</script>

<?php include("includes/footer.php"); ?>
>>>>>>> dd2a3ec1827e6eebc1fea3dd9878270b549aa490

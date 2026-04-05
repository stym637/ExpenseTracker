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
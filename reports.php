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
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Expenses',
            data: <?php echo json_encode(array_values($dataArr)); ?>,
            borderRadius: 8
        }]
    }
});
</script>

<?php include("includes/footer.php"); ?>
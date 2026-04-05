<?php include("config/db.php"); include("includes/header.php"); include("includes/sidebar.php"); ?>

<div class="main">
<h2>Reports</h2>
<canvas id="chart"></canvas>
</div>

<script>
new Chart(document.getElementById("chart"),{
type:'bar',
data:{labels:['Jan','Feb'],datasets:[{data:[200,400]}]}
});
</script>
<?php
include("../config/db.php");

if(isset($_POST['add'])){
$conn->query("INSERT INTO categories(user_id,name) VALUES({$_SESSION['user_id']},'{$_POST['name']}')");
header("Location: ../categories.php");
}
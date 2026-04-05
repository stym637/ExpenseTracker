<?php
include("../config/db.php");

if(isset($_POST['add'])){
$conn->query("INSERT INTO expenses(user_id,category_id,amount,description,expense_date)
VALUES({$_SESSION['user_id']},{$_POST['category_id']},{$_POST['amount']},'{$_POST['description']}','{$_POST['date']}')");
header("Location: ../expenses.php");
}

if(isset($_POST['update'])){
$conn->query("UPDATE expenses SET amount={$_POST['amount']},expense_date='{$_POST['date']}' WHERE id={$_POST['id']}");
header("Location: ../expenses.php");
}

if(isset($_GET['delete'])){
$conn->query("DELETE FROM expenses WHERE id=".$_GET['delete']);
header("Location: ../expenses.php");
}
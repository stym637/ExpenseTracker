<?php
include("../config/db.php");

<<<<<<< HEAD
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$uid = (int) $_SESSION['user_id'];
$hasExpenseDateCol = $conn->query("SHOW COLUMNS FROM expenses LIKE 'expense_date'")->num_rows > 0;
$hasNameCol = $conn->query("SHOW COLUMNS FROM expenses LIKE 'name'")->num_rows > 0;
$dateColumn = $hasExpenseDateCol ? "expense_date" : "date";

if (isset($_POST['add'])) {
    $name = trim($_POST['name'] ?? '');
    $amount = (float) ($_POST['amount'] ?? 0);
    $date = $_POST['date'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $categoryInput = $_POST['category_id'] ?? '';

    if ($categoryInput === "other") {
        $newCategory = trim($_POST['new_category'] ?? '');
        if ($newCategory !== '') {
            $stmt = $conn->prepare("INSERT INTO categories(user_id, name) VALUES(?, ?)");
            $stmt->bind_param("is", $uid, $newCategory);
            $stmt->execute();
            $categoryId = (int) $conn->insert_id;
            $stmt->close();
        } else {
            header("Location: ../add_expense.php");
            exit;
        }
    } else {
        $categoryId = (int) $categoryInput;
    }

    if ($hasNameCol) {
        $stmt = $conn->prepare("INSERT INTO expenses(user_id, category_id, name, amount, description, `$dateColumn`) VALUES(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisdss", $uid, $categoryId, $name, $amount, $description, $date);
    } else {
        $stmt = $conn->prepare("INSERT INTO expenses(user_id, category_id, amount, description, `$dateColumn`) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("iidss", $uid, $categoryId, $amount, $description, $date);
    }
    $stmt->execute();
    $stmt->close();

    header("Location: ../expenses.php");
    exit;
}

if (isset($_POST['update'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $amount = (float) ($_POST['amount'] ?? 0);
    $date = $_POST['date'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);

    if ($hasNameCol) {
        $stmt = $conn->prepare("UPDATE expenses SET category_id=?, name=?, amount=?, description=?, `$dateColumn`=? WHERE id=? AND user_id=?");
        $stmt->bind_param("isdssii", $categoryId, $name, $amount, $description, $date, $id, $uid);
    } else {
        $stmt = $conn->prepare("UPDATE expenses SET category_id=?, amount=?, description=?, `$dateColumn`=? WHERE id=? AND user_id=?");
        $stmt->bind_param("idssii", $categoryId, $amount, $description, $date, $id, $uid);
    }
    $stmt->execute();
    $stmt->close();

    header("Location: ../expenses.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();
    $stmt->close();

    header("Location: ../expenses.php");
    exit;
=======
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
>>>>>>> dd2a3ec1827e6eebc1fea3dd9878270b549aa490
}
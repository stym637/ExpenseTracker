<?php
include("../config/db.php");

if(isset($_POST['register'])){
$pass=password_hash($_POST['password'],PASSWORD_DEFAULT);
$conn->query("INSERT INTO users(name,email,password)
VALUES('{$_POST['name']}','{$_POST['email']}','$pass')");
$id=$conn->insert_id;

$cats=['Food','Transport','Study','Fun'];
foreach($cats as $c){
$conn->query("INSERT INTO categories(user_id,name) VALUES($id,'$c')");
}
header("Location: ../auth/login.php");
}

if(isset($_POST['login'])){
$res=$conn->query("SELECT * FROM users WHERE email='{$_POST['email']}'");
$u=$res->fetch_assoc();

if($u && password_verify($_POST['password'],$u['password'])){
$_SESSION['user_id']=$u['id'];
header("Location: ../dashboard.php");
}
}
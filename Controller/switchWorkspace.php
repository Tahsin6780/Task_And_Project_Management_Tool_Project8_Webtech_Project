<?php
include "../Model/DatabaseConnection.php";
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: ../View/login.php");
    exit();
}

$id = (int)($_GET["id"] ?? 0);
$user_id = $_SESSION["user_id"];

if($id <= 0){
    Header("Location: ../View/dashboard.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$check = $db->isWorkspaceMember($connection, $id, $user_id);
if($check->num_rows == 0){
    Header("Location: ../View/dashboard.php");
    exit();
}

$_SESSION["workspace_id"] = $id;
Header("Location: ../View/dashboard.php");
exit();
?>

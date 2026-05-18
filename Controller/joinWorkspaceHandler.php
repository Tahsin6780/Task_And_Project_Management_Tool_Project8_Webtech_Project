<?php
include "../Model/DatabaseConnection.php";
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: ../View/login.php");
    exit();
}

$code = $_POST["code"] ?? "";
$user_id = $_SESSION["user_id"];

$code = strtoupper(trim($code));

if(strlen($code) != 6){
    $_SESSION["joinError"] = "Invite code must be exactly 6 characters";
    $_SESSION["joinCode"] = $code;
    Header("Location: ../View/joinWorkspace.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$result = $db->findWorkspaceByCode($connection, "workspaces", $code);
if($result->num_rows == 0){
    $_SESSION["joinError"] = "Invite code not found";
    $_SESSION["joinCode"] = $code;
    Header("Location: ../View/joinWorkspace.php");
    exit();
}

$workspace = $result->fetch_assoc();
$workspace_id = $workspace["id"];

$memberCheck = $db->isWorkspaceMember($connection, $workspace_id, $user_id);
if($memberCheck->num_rows > 0){
    $_SESSION["workspace_id"] = (int)$workspace_id;
    Header("Location: ../View/dashboard.php");
    exit();
}

$add = $db->addWorkspaceMember($connection, "workspace_members", $workspace_id, $user_id);
if($add){
    $_SESSION["workspace_id"] = (int)$workspace_id;
    Header("Location: ../View/dashboard.php");
    exit();
}else{
    $_SESSION["joinError"] = "Failed to join workspace";
    $_SESSION["joinCode"] = $code;
    Header("Location: ../View/joinWorkspace.php");
    exit();
}
?>

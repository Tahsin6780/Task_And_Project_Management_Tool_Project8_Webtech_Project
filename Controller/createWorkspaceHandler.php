<?php
include "../Model/DatabaseConnection.php";
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: ../View/login.php");
    exit();
}

$name = $_POST["name"] ?? "";
$description = $_POST["description"] ?? "";
$owner_id = $_SESSION["user_id"];

$hasNameError = true;
if(!$name){
    $hasNameError = true;
    $_SESSION["wsNameError"] = "Workspace name is required";
}else if(strlen($name) < 3){
    $hasNameError = true;
    $_SESSION["wsNameError"] = "Workspace name must be at least 3 characters";
}else{
    $hasNameError = false;
    unset($_SESSION["wsNameError"]);
}

if($hasNameError){
    $_SESSION["wsName"] = $name;
    $_SESSION["wsDesc"] = $description;
    Header("Location: ../View/createWorkspace.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
$invite_code = "";
$tries = 0;
while(true){
    $invite_code = "";
    for($i = 0; $i < 6; $i++){
        $invite_code .= $chars[rand(0, strlen($chars) - 1)];
    }
    $check = $db->inviteCodeExists($connection, "workspaces", $invite_code);
    if($check->num_rows == 0){
        break;
    }
    $tries++;
    if($tries > 20){
        $_SESSION["wsNameError"] = "Could not generate invite code. Try again.";
        Header("Location: ../View/createWorkspace.php");
        exit();
    }
}

$new_workspace_id = $db->createWorkspace($connection, "workspaces", $name, $description, $owner_id, $invite_code);
if($new_workspace_id > 0){
    $db->addWorkspaceMember($connection, "workspace_members", $new_workspace_id, $owner_id);
    $_SESSION["workspace_id"] = $new_workspace_id;
    Header("Location: ../View/dashboard.php");
    exit();
}else{
    $_SESSION["wsNameError"] = "Failed to create workspace.";
    $_SESSION["wsName"] = $name;
    $_SESSION["wsDesc"] = $description;
    Header("Location: ../View/createWorkspace.php");
    exit();
}
?>

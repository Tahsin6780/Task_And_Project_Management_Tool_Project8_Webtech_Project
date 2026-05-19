<?php
include "../Model/DatabaseConnection.php";
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: ../View/login.php");
    exit();
}

$workspace_id = $_SESSION["workspace_id"] ?? 0;
$user_id = $_SESSION["user_id"];
if(!$workspace_id){
    Header("Location: ../View/workspaceChoice.php");
    exit();
}

$name = $_POST["name"] ?? "";
$description = $_POST["description"] ?? "";
$deadline = $_POST["deadline"] ?? "";
$color_label = $_POST["color_label"] ?? "#3B82F6";

$hasNameError = true;
$hasDescError = true;
$hasDeadlineError = true;
$hasColorError = true;

if(!$name){
    $hasNameError = true;
    $_SESSION["projNameError"] = "Project name is required";
}else if(strlen($name) < 3){
    $hasNameError = true;
    $_SESSION["projNameError"] = "Project name must be at least 3 characters";
}else if(strlen($name) > 150){
    $hasNameError = true;
    $_SESSION["projNameError"] = "Project name must be 150 characters or fewer";
}else{
    $hasNameError = false;
    unset($_SESSION["projNameError"]);
}

if(strlen($description) > 500){
    $hasDescError = true;
    $_SESSION["projDescError"] = "Description must be 500 characters or fewer";
}else{
    $hasDescError = false;
    unset($_SESSION["projDescError"]);
}

if($deadline){
    $today = date("Y-m-d");
    if($deadline < $today){
        $hasDeadlineError = true;
        $_SESSION["projDeadlineError"] = "Deadline cannot be in the past";
    }else{
        $hasDeadlineError = false;
        unset($_SESSION["projDeadlineError"]);
    }
}else{
    $hasDeadlineError = false;
    $deadline = null;
    unset($_SESSION["projDeadlineError"]);
}

if(!preg_match('/^#[A-Fa-f0-9]{6}$/', $color_label)){
    $hasColorError = true;
    $_SESSION["projColorError"] = "Invalid color";
    $color_label = "#3B82F6";
}else{
    $hasColorError = false;
    unset($_SESSION["projColorError"]);
}

if($hasNameError || $hasDescError || $hasDeadlineError || $hasColorError){
    $_SESSION["projName"] = $name;
    $_SESSION["projDesc"] = $description;
    $_SESSION["projDeadline"] = $deadline ?? "";
    $_SESSION["projColor"] = $color_label;
    Header("Location: ../View/createProject.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

// uniqueness check (server-side, even though JS already checks)
$exists = $db->checkProjectName($connection, $workspace_id, $name);
if($exists->num_rows > 0){
    $_SESSION["projNameError"] = "A project with this name already exists in the workspace";
    $_SESSION["projName"] = $name;
    $_SESSION["projDesc"] = $description;
    $_SESSION["projDeadline"] = $deadline ?? "";
    $_SESSION["projColor"] = $color_label;
    Header("Location: ../View/createProject.php");
    exit();
}

$new_project_id = $db->createProject($connection, "projects", $workspace_id, $name, $description, $deadline, $color_label);
if($new_project_id > 0){
    $db->addProjectMember($connection, "project_members", $new_project_id, $owner_id);
    $db->logActivity($connection, "activity_logs", $new_project_id, $_SESSION["user_id"], "created project: ".$name);
    Header("Location: ../View/projects.php");
    exit();
}else{
    $_SESSION["projNameError"] = "Failed to create project";
    $_SESSION["projName"] = $name;
    $_SESSION["projDesc"] = $description;
    $_SESSION["projDeadline"] = $deadline ?? "";
    $_SESSION["projColor"] = $color_label;
    Header("Location: ../View/createProject.php");
    exit();
}
?>

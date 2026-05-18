<?php
include "../Model/DatabaseConnection.php";
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: ../View/login.php");
    exit();
}

$workspace_id = $_SESSION["workspace_id"] ?? 0;
$id = (int)($_POST["id"] ?? 0);
if($id <= 0 || !$workspace_id){
    Header("Location: ../View/projects.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$projResult = $db->getProjectById($connection, "projects", $id);
if($projResult->num_rows == 0){
    Header("Location: ../View/projects.php");
    exit();
}
$project = $projResult->fetch_assoc();
if((int)$project["workspace_id"] !== (int)$workspace_id){
    Header("Location: ../View/projects.php");
    exit();
}

$name = $_POST["name"] ?? "";
$description = $_POST["description"] ?? "";
$deadline = $_POST["deadline"] ?? "";
$color_label = $_POST["color_label"] ?? "#3B82F6";

$hasNameError = true;
$hasDescError = true;
$hasDeadlineError = true;

if(!$name){
    $hasNameError = true;
    $_SESSION["editNameError"] = "Project name is required";
}else if(strlen($name) < 3){
    $hasNameError = true;
    $_SESSION["editNameError"] = "Project name must be at least 3 characters";
}else{
    $hasNameError = false;
    unset($_SESSION["editNameError"]);
}

if(strlen($description) > 500){
    $hasDescError = true;
    $_SESSION["editDescError"] = "Description must be 500 characters or fewer";
}else{
    $hasDescError = false;
    unset($_SESSION["editDescError"]);
}

if($deadline){
    $hasDeadlineError = false;
    unset($_SESSION["editDeadlineError"]);
}else{
    $hasDeadlineError = false;
    $deadline = null;
    unset($_SESSION["editDeadlineError"]);
}

if(!preg_match('/^#[A-Fa-f0-9]{6}$/', $color_label)){
    $color_label = $project["color_label"];
}

if($hasNameError || $hasDescError || $hasDeadlineError){
    $_SESSION["editName"] = $name;
    $_SESSION["editDesc"] = $description;
    $_SESSION["editDeadline"] = $deadline ?? "";
    $_SESSION["editColor"] = $color_label;
    Header("Location: ../View/editProject.php?id=".$id);
    exit();
}

$result = $db->updateProject($connection, "projects", $id, $name, $description, $deadline, $color_label);
if($result){
    Header("Location: ../View/projectDetail.php?id=".$id);
    exit();
}else{
    $_SESSION["editNameError"] = "Failed to save changes";
    Header("Location: ../View/editProject.php?id=".$id);
    exit();
}
?>
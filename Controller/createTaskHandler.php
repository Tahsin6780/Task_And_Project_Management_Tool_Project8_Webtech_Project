<?php
include "../Model/DatabaseConnection.php";
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: ../View/login.php");
    exit();
}

$workspace_id = $_SESSION["workspace_id"] ?? 0;
$project_id = (int)($_POST["project_id"] ?? 0);
if($project_id <= 0 || !$workspace_id){
    Header("Location: ../View/projects.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$projResult = $db->getProjectById($connection, "projects", $project_id);
if($projResult->num_rows == 0){
    Header("Location: ../View/projects.php");
    exit();
}
$project = $projResult->fetch_assoc();
if((int)$project["workspace_id"] !== (int)$workspace_id){
    Header("Location: ../View/projects.php");
    exit();
}

$title = $_POST["title"] ?? "";
$description = $_POST["description"] ?? "";
$assigned_to_raw = $_POST["assigned_to"] ?? "";
$priority = $_POST["priority"] ?? "low";
$due_date = $_POST["due_date"] ?? "";

$hasTitleError = true;
$hasDescError = true;
$hasDueError = true;

if(!$title){
    $hasTitleError = true;
    $_SESSION["taskTitleError"] = "Title is required";
}else if(strlen($title) < 3){
    $hasTitleError = true;
    $_SESSION["taskTitleError"] = "Title must be at least 3 characters";
}else if(strlen($title) > 180){
    $hasTitleError = true;
    $_SESSION["taskTitleError"] = "Title must be 180 characters or fewer";
}else{
    $hasTitleError = false;
    unset($_SESSION["taskTitleError"]);
}

if(strlen($description) > 1000){
    $hasDescError = true;
    $_SESSION["taskDescError"] = "Description must be 1000 characters or fewer";
}else{
    $hasDescError = false;
    unset($_SESSION["taskDescError"]);
}

if($due_date){
    $today = date("Y-m-d");
    if($due_date < $today){
        $hasDueError = true;
        $_SESSION["taskDueError"] = "Due date cannot be in the past";
    }else{
        $hasDueError = false;
        unset($_SESSION["taskDueError"]);
    }
}else{
    $hasDueError = false;
    $due_date = null;
    unset($_SESSION["taskDueError"]);
}

if(!in_array($priority, ["low", "medium", "high"])){
    $priority = "low";
}

// assignee — either empty (null) or an integer that is a member of the project
$assigned_to = null;
if($assigned_to_raw !== ""){
    $candidate = (int)$assigned_to_raw;
    if($candidate > 0){
        $check = $db->isProjectMember($connection, $project_id, $candidate);
        if($check->num_rows > 0){
            $assigned_to = $candidate;
        }
    }
}

if($hasTitleError || $hasDescError || $hasDueError){
    $_SESSION["taskTitle"] = $title;
    $_SESSION["taskDesc"] = $description;
    $_SESSION["taskAssignee"] = $assigned_to;
    $_SESSION["taskPriority"] = $priority;
    $_SESSION["taskDue"] = $due_date ?? "";
    Header("Location: ../View/createTask.php?project_id=".$project_id);
    exit();
}

$new_task_id = $db->createTask($connection, "tasks", $project_id, $title, $description, $assigned_to, $priority, $due_date);
if($new_task_id > 0){
    $db->logActivity($connection, "activity_logs", $project_id, $_SESSION["user_id"], "created task: ".$title);
    Header("Location: ../View/projectBoard.php?project_id=".$project_id);
    exit();
}else{
    $_SESSION["taskTitleError"] = "Failed to create task";
    $_SESSION["taskTitle"] = $title;
    $_SESSION["taskDesc"] = $description;
    $_SESSION["taskAssignee"] = $assigned_to;
    $_SESSION["taskPriority"] = $priority;
    $_SESSION["taskDue"] = $due_date ?? "";
    Header("Location: ../View/createTask.php?project_id=".$project_id);
    exit();
}
?>

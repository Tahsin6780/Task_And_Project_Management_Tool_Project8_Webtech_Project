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

$taskResult = $db->getTaskById($connection, "tasks", $id);
if($taskResult->num_rows == 0){
    Header("Location: ../View/projects.php");
    exit();
}
$task = $taskResult->fetch_assoc();

$projResult = $db->getProjectById($connection, "projects", $task["project_id"]);
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
    $_SESSION["editTaskTitleError"] = "Title is required";
}else if(strlen($title) < 3){
    $hasTitleError = true;
    $_SESSION["editTaskTitleError"] = "Title must be at least 3 characters";
}else{
    $hasTitleError = false;
    unset($_SESSION["editTaskTitleError"]);
}

if(strlen($description) > 1000){
    $hasDescError = true;
    $_SESSION["editTaskDescError"] = "Description must be 1000 characters or fewer";
}else{
    $hasDescError = false;
    unset($_SESSION["editTaskDescError"]);
}

if($due_date){
    $hasDueError = false;
    unset($_SESSION["editTaskDueError"]);
}else{
    $hasDueError = false;
    $due_date = null;
    unset($_SESSION["editTaskDueError"]);
}

if(!in_array($priority, ["low", "medium", "high"])){
    $priority = $task["priority"];
}

$assigned_to = null;
if($assigned_to_raw !== ""){
    $candidate = (int)$assigned_to_raw;
    if($candidate > 0){
        $check = $db->isProjectMember($connection, $task["project_id"], $candidate);
        if($check->num_rows > 0){
            $assigned_to = $candidate;
        }
    }
}

if($hasTitleError || $hasDescError || $hasDueError){
    $_SESSION["editTaskTitle"] = $title;
    $_SESSION["editTaskDesc"] = $description;
    $_SESSION["editTaskAssignee"] = $assigned_to;
    $_SESSION["editTaskPriority"] = $priority;
    $_SESSION["editTaskDue"] = $due_date ?? "";
    Header("Location: ../View/editTask.php?id=".$id);
    exit();
}

$result = $db->updateTask($connection, "tasks", $id, $title, $description, $assigned_to, $priority, $due_date);
if($result){
    Header("Location: ../View/taskDetail.php?id=".$id);
    exit();
}else{
    $_SESSION["editTaskTitleError"] = "Failed to save changes";
    Header("Location: ../View/editTask.php?id=".$id);
    exit();
}
?>

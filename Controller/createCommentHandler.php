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
$task_id = (int)($_POST["task_id"] ?? 0);
$body = $_POST["body"] ?? "";

if($task_id <= 0 || !$workspace_id){
    Header("Location: ../View/projects.php");
    exit();
}

$hasBodyError = true;
if(!trim($body)){
    $hasBodyError = true;
    $_SESSION["commentError"] = "Comment cannot be empty";
}else if(strlen($body) > 1000){
    $hasBodyError = true;
    $_SESSION["commentError"] = "Comment must be 1000 characters or fewer";
}else{
    $hasBodyError = false;
    unset($_SESSION["commentError"]);
}

if($hasBodyError){
    $_SESSION["commentBody"] = $body;
    Header("Location: ../View/taskDetail.php?id=".$task_id);
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$taskResult = $db->getTaskById($connection, "tasks", $task_id);
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

$new_comment_id = $db->createComment($connection, "comments", $task_id, $user_id, $body);
if($new_comment_id > 0){
    $db->logActivity($connection, "activity_logs", $task["project_id"], $user_id, "commented on task: ".$task["title"]);
    Header("Location: ../View/taskDetail.php?id=".$task_id);
    exit();
}else{
    $_SESSION["commentError"] = "Failed to post comment";
    $_SESSION["commentBody"] = $body;
    Header("Location: ../View/taskDetail.php?id=".$task_id);
    exit();
}
?>

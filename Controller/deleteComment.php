<?php
include "../Model/DatabaseConnection.php";
session_start();
header("Content-Type: application/json");

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    echo json_encode(["ok" => false, "message" => "Not logged in"]);
    exit();
}

$id = (int)($_GET["id"] ?? 0);
$user_id = $_SESSION["user_id"];
$workspace_id = $_SESSION["workspace_id"] ?? 0;

if($id <= 0 || !$workspace_id){
    echo json_encode(["ok" => false, "message" => "Invalid request"]);
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$cRes = $db->getCommentById($connection, "comments", $id);
if($cRes->num_rows == 0){
    echo json_encode(["ok" => false, "message" => "Comment not found"]);
    exit();
}
$comment = $cRes->fetch_assoc();

if((int)$comment["user_id"] !== (int)$user_id){
    echo json_encode(["ok" => false, "message" => "You can only delete your own comments"]);
    exit();
}

$tRes = $db->getTaskById($connection, "tasks", $comment["task_id"]);
if($tRes->num_rows == 0){
    echo json_encode(["ok" => false, "message" => "Task not found"]);
    exit();
}
$task = $tRes->fetch_assoc();

$pRes = $db->getProjectById($connection, "projects", $task["project_id"]);
$project = $pRes->fetch_assoc();
if((int)$project["workspace_id"] !== (int)$workspace_id){
    echo json_encode(["ok" => false, "message" => "Wrong workspace"]);
    exit();
}

$result = $db->deleteComment($connection, "comments", $id);
if($result){
    echo json_encode(["ok" => true]);
}else{
    echo json_encode(["ok" => false, "message" => "Database error"]);
}
?>

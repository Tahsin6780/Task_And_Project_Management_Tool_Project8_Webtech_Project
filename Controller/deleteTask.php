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
$workspace_id = $_SESSION["workspace_id"] ?? 0;

if($id <= 0 || !$workspace_id){
    echo json_encode(["ok" => false, "message" => "Invalid request"]);
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$taskResult = $db->getTaskById($connection, "tasks", $id);
if($taskResult->num_rows == 0){
    echo json_encode(["ok" => false, "message" => "Task not found"]);
    exit();
}
$task = $taskResult->fetch_assoc();

$projResult = $db->getProjectById($connection, "projects", $task["project_id"]);
if($projResult->num_rows == 0){
    echo json_encode(["ok" => false, "message" => "Project not found"]);
    exit();
}
$project = $projResult->fetch_assoc();
if((int)$project["workspace_id"] !== (int)$workspace_id){
    echo json_encode(["ok" => false, "message" => "Task not in this workspace"]);
    exit();
}

$result = $db->deleteTask($connection, "tasks", $id);
if($result){
    $db->logActivity($connection, "activity_logs", $task["project_id"], $_SESSION["user_id"], "deleted task: ".$task["title"]);
    echo json_encode(["ok" => true]);
}else{
    echo json_encode(["ok" => false, "message" => "Database error"]);
}
?>

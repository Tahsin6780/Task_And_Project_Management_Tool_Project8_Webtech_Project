<?php
include "../Model/DatabaseConnection.php";
session_start();
header("Content-Type: application/json");

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    echo json_encode(["ok" => false, "message" => "Not logged in"]);
    exit();
}

$project_id = (int)($_GET["project_id"] ?? 0);
$user_id = (int)($_GET["user_id"] ?? 0);
$workspace_id = $_SESSION["workspace_id"] ?? 0;

if($project_id <= 0 || $user_id <= 0 || !$workspace_id){
    echo json_encode(["ok" => false, "message" => "Invalid request"]);
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$projResult = $db->getProjectById($connection, "projects", $project_id);
if($projResult->num_rows == 0){
    echo json_encode(["ok" => false, "message" => "Project not found"]);
    exit();
}
$project = $projResult->fetch_assoc();
if((int)$project["workspace_id"] !== (int)$workspace_id){
    echo json_encode(["ok" => false, "message" => "Project not in this workspace"]);
    exit();
}

$result = $db->removeProjectMember($connection, "project_members", $project_id, $user_id);
if($result){
    $db->logActivity($connection, "activity_logs", $project_id, $_SESSION["user_id"], "removed a member from the project");
    echo json_encode(["ok" => true]);
}else{
    echo json_encode(["ok" => false, "message" => "Database error"]);
}
?>

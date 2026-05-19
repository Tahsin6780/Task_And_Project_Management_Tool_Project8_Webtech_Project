<?php
include "../Model/DatabaseConnection.php";
session_start();
header("Content-Type: application/json");

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    echo json_encode(["ok" => false, "message" => "Not logged in"]);
    exit();
}

$id = (int)($_POST["id"] ?? 0);
$archived = (int)($_POST["archived"] ?? 0);
$workspace_id = $_SESSION["workspace_id"] ?? 0;

if($id <= 0 || !$workspace_id){
    echo json_encode(["ok" => false, "message" => "Invalid request"]);
    exit();
}
if($archived !== 0 && $archived !== 1){
    echo json_encode(["ok" => false, "message" => "Invalid archived flag"]);
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$projResult = $db->getProjectById($connection, "projects", $id);
if($projResult->num_rows == 0){
    echo json_encode(["ok" => false, "message" => "Project not found"]);
    exit();
}
$project = $projResult->fetch_assoc();
if((int)$project["workspace_id"] !== (int)$workspace_id){
    echo json_encode(["ok" => false, "message" => "Project not in this workspace"]);
    exit();
}

$result = $db->setProjectArchived($connection, "projects", $id, $archived);
if($result){
    $actionText = $newState ? "archived project: ".$project["name"] : "unarchived project: ".$project["name"];
    $db->logActivity($connection, "activity_logs", $project["id"], $_SESSION["user_id"], $actionText);
    echo json_encode(["ok" => true, "archived" => $newState]);
}else{
    echo json_encode(["ok" => false, "message" => "Database error"]);
}
?>

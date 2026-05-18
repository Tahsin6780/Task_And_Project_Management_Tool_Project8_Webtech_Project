<?php
include "../Model/DatabaseConnection.php";
session_start();
header("Content-Type: application/json");

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    echo json_encode(["available" => false, "message" => "Not logged in"]);
    exit();
}

$workspace_id = $_SESSION["workspace_id"] ?? 0;
$name = $_POST["name"] ?? "";

if(!$name || !$workspace_id){
    echo json_encode(["available" => false, "message" => "Project name is required"]);
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();
$result = $db->checkProjectName($connection, $workspace_id, $name);

if($result->num_rows > 0){
    echo json_encode(["available" => false, "message" => "A project with this name already exists in the workspace"]);
}else{
    echo json_encode(["available" => true, "message" => "Project name is available"]);
}
?>
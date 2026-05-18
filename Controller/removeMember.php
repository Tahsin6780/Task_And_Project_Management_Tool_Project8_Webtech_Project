<?php
include "../Model/DatabaseConnection.php";
session_start();
header("Content-Type: application/json");

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    echo json_encode(["ok" => false, "message" => "Not logged in"]);
    exit();
}

$member_row_id = (int)($_GET["id"] ?? 0);
$user_id = $_SESSION["user_id"];
$workspace_id = $_SESSION["workspace_id"] ?? 0;

if($member_row_id <= 0){
    echo json_encode(["ok" => false, "message" => "Invalid member id"]);
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$wsResult = $db->getWorkspaceById($connection, "workspaces", $workspace_id);
if($wsResult->num_rows == 0){
    echo json_encode(["ok" => false, "message" => "Workspace not found"]);
    exit();
}
$workspace = $wsResult->fetch_assoc();
if((int)$workspace["owner_id"] !== (int)$user_id){
    echo json_encode(["ok" => false, "message" => "Only the owner can remove members"]);
    exit();
}

$rowResult = $db->getWorkspaceMemberRow($connection, $member_row_id);
if($rowResult->num_rows == 0){
    echo json_encode(["ok" => false, "message" => "Member row not found"]);
    exit();
}
$row = $rowResult->fetch_assoc();
if((int)$row["workspace_id"] !== (int)$workspace_id){
    echo json_encode(["ok" => false, "message" => "Member does not belong to this workspace"]);
    exit();
}
if((int)$row["user_id"] === (int)$user_id){
    echo json_encode(["ok" => false, "message" => "Owner cannot remove themselves"]);
    exit();
}

$result = $db->removeWorkspaceMember($connection, "workspace_members", $member_row_id);
if($result){
    echo json_encode(["ok" => true]);
}else{
    echo json_encode(["ok" => false, "message" => "Database error"]);
}
?>

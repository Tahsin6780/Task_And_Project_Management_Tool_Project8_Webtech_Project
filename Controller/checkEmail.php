<?php
include "../Model/DatabaseConnection.php";
header("Content-Type: application/json");

$email = $_POST["email"] ?? "";

if(!$email){
    echo json_encode(["available" => false, "message" => "Email is required"]);
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();
$result = $db->checkEmail($connection, "users", $email);

if($result->num_rows > 0){
    echo json_encode(["available" => false, "message" => "Email is already taken"]);
}else{
    echo json_encode(["available" => true, "message" => "Email is available"]);
}
?>

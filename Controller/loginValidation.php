<?php
include "../Model/DatabaseConnection.php";
session_start();

$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";

$hasEmailError = true;
$hasPasswordError = true;

if(!$email){
    $hasEmailError = true;
    $_SESSION["emailError"] = "Email is required";
}else{
    $hasEmailError = false;
    unset($_SESSION["emailError"]);
}

if(!$password){
    $hasPasswordError = true;
    $_SESSION["passwordError"] = "Password is required";
}else{
    $hasPasswordError = false;
    unset($_SESSION["passwordError"]);
}

if($hasEmailError || $hasPasswordError){
    $_SESSION["email"] = $email;
    Header("Location: ../View/login.php");
    exit();
}

$isLoggedIn = false;
$db = new DatabaseConnection();
$connection = $db->openConnection();
$result = $db->signIn($connection, "users", $email);

if($result->num_rows == 1){
    while($row = $result->fetch_assoc()){
        if(password_verify($password, $row["password_hash"])){
            $isLoggedIn = true;
            $_SESSION["isLoggedIn"] = true;
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["name"] = $row["name"];

            $wsResult = $db->getFirstWorkspaceId($connection, $row["id"]);
            if($wsResult->num_rows > 0){
                $wsRow = $wsResult->fetch_assoc();
                $_SESSION["workspace_id"] = (int)$wsRow["workspace_id"];
            }else{
                $_SESSION["workspace_id"] = null;
            }

            setcookie("remember_email", $email, time() + (7*24*3600), "/");

            Header("Location: ../View/dashboard.php");
            exit();
        }
    }
}

if(!$isLoggedIn){
    $_SESSION["email"] = $email;
    $_SESSION["loggingError"] = "Email or password is incorrect!";
    Header("Location: ../View/login.php");
    exit();
}
?>

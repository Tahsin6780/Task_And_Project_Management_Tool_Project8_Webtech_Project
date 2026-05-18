<?php
include "../Model/DatabaseConnection.php";
session_start();

$name = $_POST["name"] ?? "";
$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";

$hasNameError = true;
$hasEmailError = true;
$hasPasswordError = true;

if(!$name){
    $hasNameError = true;
    $_SESSION["nameError"] = "Name is required";
}else{
    $hasNameError = false;
    unset($_SESSION["nameError"]);
}

if(!$email){
    $hasEmailError = true;
    $_SESSION["emailError"] = "Email is required";
}else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $hasEmailError = true;
    $_SESSION["emailError"] = "Invalid email format";
}else{
    $hasEmailError = false;
    unset($_SESSION["emailError"]);
}

if(!$password){
    $hasPasswordError = true;
    $_SESSION["passwordError"] = "Password is required";
}else if(strlen($password) < 8){
    $hasPasswordError = true;
    $_SESSION["passwordError"] = "Password must be at least 8 characters";
}else{
    $hasPasswordError = false;
    unset($_SESSION["passwordError"]);
}

if($hasNameError || $hasEmailError || $hasPasswordError){
    $_SESSION["name"] = $name;
    $_SESSION["email"] = $email;
    Header("Location: ../View/registration.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$checkResult = $db->checkEmail($connection, "users", $email);
if($checkResult->num_rows > 0){
    $_SESSION["emailError"] = "Email is already taken";
    $_SESSION["name"] = $name;
    $_SESSION["email"] = $email;
    Header("Location: ../View/registration.php");
    exit();
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$result = $db->signUp($connection, "users", $name, $email, $password_hash);

if($result){
    Header("Location: ../View/login.php");
    exit();
}else{
    $_SESSION["loggingError"] = "Registration failed. Try again.";
    $_SESSION["name"] = $name;
    $_SESSION["email"] = $email;
    Header("Location: ../View/registration.php");
    exit();
}
?>

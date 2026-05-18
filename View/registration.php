<?php
session_start();
$nameError = $_SESSION["nameError"] ?? "";
$emailError = $_SESSION["emailError"] ?? "";
$passwordError = $_SESSION["passwordError"] ?? "";
$name = $_SESSION["name"] ?? "";
$email = $_SESSION["email"] ?? "";
$loggingError = $_SESSION["loggingError"] ?? "";
$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";

if($isLoggedIn){
    Header("Location: dashboard.php");
    exit();
}

unset($_SESSION["nameError"]);
unset($_SESSION["emailError"]);
unset($_SESSION["passwordError"]);
unset($_SESSION["name"]);
unset($_SESSION["email"]);
unset($_SESSION["loggingError"]);
?>
<html>
<head>
<title>Register</title>
<script src="../Controller/JS/checkEmail.js"></script>
<script src="../Controller/JS/registerValidate.js"></script>
</head>
<body>
<h2>Register</h2>
<form method="post" action="../Controller/registrationValidation.php" onsubmit="return validateRegister()">
<table>
<tr>
    <td>Name</td>
    <td><input type="text" name="name" id="name" value="<?php echo $name;?>"/></td>
    <td style="color:red"><?php echo $nameError;?></td>
    <td><p id="nameJsError" style="color:red"></p></td>
</tr>
<tr>
    <td>Email</td>
    <td><input type="text" name="email" id="email" value="<?php echo $email;?>" onkeyup="checkEmail()"/></td>
    <td style="color:red"><?php echo $emailError;?></td>
    <td><p id="emailResponse"></p></td>
</tr>
<tr>
    <td>Password</td>
    <td><input type="password" name="password" id="password"/></td>
    <td style="color:red"><?php echo $passwordError;?></td>
    <td><p id="passwordJsError" style="color:red"></p></td>
</tr>
<tr>
    <td></td>
    <td style="color:red"><?php echo $loggingError;?></td>
</tr>
<tr>
    <td></td>
    <td><input type="submit" name="submit" value="Register"/></td>
</tr>
</table>
</form>
<p>Already have an account? <a href="login.php">Login</a></p>
</body>
</html>

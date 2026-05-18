<?php
session_start();
$emailError = $_SESSION["emailError"] ?? "";
$passwordError = $_SESSION["passwordError"] ?? "";
$email = $_SESSION["email"] ?? "";
$loggingError = $_SESSION["loggingError"] ?? "";
$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";

if($isLoggedIn){
    Header("Location: dashboard.php");
    exit();
}

unset($_SESSION["emailError"]);
unset($_SESSION["passwordError"]);
unset($_SESSION["email"]);
unset($_SESSION["loggingError"]);

$rememberedEmail = $_COOKIE["remember_email"] ?? "";
if(!$email && $rememberedEmail){
    $email = $rememberedEmail;
}
?>
<html>
<head>
<title>Login</title>
<script src="../Controller/JS/loginValidate.js"></script>
</head>
<body>
<h2>Login</h2>
<form method="post" action="../Controller/loginValidation.php" onsubmit="return validateLogin()">
<table>
<tr>
    <td>Email</td>
    <td><input type="text" name="email" id="email" value="<?php echo $email;?>"/></td>
    <td style="color:red"><?php echo $emailError;?></td>
    <td><p id="emailJsError" style="color:red"></p></td>
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
    <td><input type="submit" name="submit" value="Login"/></td>
</tr>
</table>
</form>
<p>No account? <a href="registration.php">Register</a></p>
</body>
</html>

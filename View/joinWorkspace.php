<?php
session_start();
$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: login.php");
    exit();
}
$joinError = $_SESSION["joinError"] ?? "";
$code = $_SESSION["joinCode"] ?? "";
unset($_SESSION["joinError"]);
unset($_SESSION["joinCode"]);
?>
<html>
<head>
<title>Join Workspace</title>
<script src="../Controller/JS/joinWorkspaceValidate.js"></script>
</head>
<body>
<h2>Join a workspace</h2>
<form method="post" action="../Controller/joinWorkspaceHandler.php" onsubmit="return validateJoin()">
<table>
<tr>
    <td>Invite Code</td>
    <td><input type="text" name="code" id="code" value="<?php echo $code;?>" maxlength="6"/></td>
    <td style="color:red"><?php echo $joinError;?></td>
    <td><p id="codeJsError" style="color:red"></p></td>
</tr>
<tr>
    <td></td>
    <td><input type="submit" name="submit" value="Join"/></td>
</tr>
</table>
</form>
<p><a href="workspaceChoice.php">Back</a></p>
</body>
</html>

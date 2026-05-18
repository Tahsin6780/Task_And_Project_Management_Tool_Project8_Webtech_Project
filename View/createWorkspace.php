<?php
session_start();
$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: login.php");
    exit();
}
$wsNameError = $_SESSION["wsNameError"] ?? "";
$wsDescError = $_SESSION["wsDescError"] ?? "";
$wsName = $_SESSION["wsName"] ?? "";
$wsDesc = $_SESSION["wsDesc"] ?? "";
unset($_SESSION["wsNameError"]);
unset($_SESSION["wsDescError"]);
unset($_SESSION["wsName"]);
unset($_SESSION["wsDesc"]);
?>
<html>
<head>
<title>Create Workspace</title>
<script src="../Controller/JS/createWorkspaceValidate.js"></script>
</head>
<body>
<h2>Create a new workspace</h2>
<form method="post" action="../Controller/createWorkspaceHandler.php" onsubmit="return validateCreateWorkspace()">
<table>
<tr>
    <td>Workspace Name</td>
    <td><input type="text" name="name" id="wsName" value="<?php echo $wsName;?>"/></td>
    <td style="color:red"><?php echo $wsNameError;?></td>
    <td><p id="wsNameJsError" style="color:red"></p></td>
</tr>
<tr>
    <td>Description</td>
    <td><textarea name="description" id="wsDesc"><?php echo $wsDesc;?></textarea></td>
    <td style="color:red"><?php echo $wsDescError;?></td>
</tr>
<tr>
    <td></td>
    <td><input type="submit" name="submit" value="Create"/></td>
</tr>
</table>
</form>
<p><a href="workspaceChoice.php">Back</a></p>
</body>
</html>

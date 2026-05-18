<?php
session_start();
$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: login.php");
    exit();
}
$workspace_id = $_SESSION["workspace_id"] ?? null;
if(!$workspace_id){
    Header("Location: workspaceChoice.php");
    exit();
}

$nameError = $_SESSION["projNameError"] ?? "";
$descError = $_SESSION["projDescError"] ?? "";
$deadlineError = $_SESSION["projDeadlineError"] ?? "";
$colorError = $_SESSION["projColorError"] ?? "";
$projName = $_SESSION["projName"] ?? "";
$projDesc = $_SESSION["projDesc"] ?? "";
$projDeadline = $_SESSION["projDeadline"] ?? "";
$projColor = $_SESSION["projColor"] ?? "#3B82F6";

unset($_SESSION["projNameError"]);
unset($_SESSION["projDescError"]);
unset($_SESSION["projDeadlineError"]);
unset($_SESSION["projColorError"]);
unset($_SESSION["projName"]);
unset($_SESSION["projDesc"]);
unset($_SESSION["projDeadline"]);
unset($_SESSION["projColor"]);
?>
<html>
<head>
<title>Create Project</title>
<script src="../Controller/JS/checkProjectName.js"></script>
<script src="../Controller/JS/createProjectValidate.js"></script>
</head>
<body>
<h2>Create a new project</h2>
<form method="post" action="../Controller/createProjectHandler.php" onsubmit="return validateCreateProject()">
<table>
<tr>
    <td>Project Name</td>
    <td><input type="text" name="name" id="projName" value="<?php echo $projName;?>" onkeyup="checkProjectName()"/></td>
    <td style="color:red"><?php echo $nameError;?></td>
    <td><p id="projNameResponse"></p></td>
</tr>
<tr>
    <td>Description</td>
    <td><textarea name="description" id="projDesc"><?php echo $projDesc;?></textarea></td>
    <td style="color:red"><?php echo $descError;?></td>
    <td><p id="projDescJsError" style="color:red"></p></td>
</tr>
<tr>
    <td>Deadline</td>
    <td><input type="date" name="deadline" id="projDeadline" value="<?php echo $projDeadline;?>"/></td>
    <td style="color:red"><?php echo $deadlineError;?></td>
    <td><p id="projDeadlineJsError" style="color:red"></p></td>
</tr>
<tr>
    <td>Color label</td>
    <td><input type="color" name="color_label" id="projColor" value="<?php echo $projColor;?>"/></td>
    <td style="color:red"><?php echo $colorError;?></td>
</tr>
<tr>
    <td></td>
    <td><input type="submit" name="submit" value="Create Project"/></td>
</tr>
</table>
</form>
<p><a href="projects.php">Back to projects</a></p>
</body>
</html>
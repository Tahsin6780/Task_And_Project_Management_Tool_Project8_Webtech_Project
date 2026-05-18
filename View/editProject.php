<?php
include "../Model/DatabaseConnection.php";
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: login.php");
    exit();
}

$workspace_id = $_SESSION["workspace_id"] ?? 0;
if(!$workspace_id){
    Header("Location: workspaceChoice.php");
    exit();
}

$id = (int)($_GET["id"] ?? 0);
if($id <= 0){
    Header("Location: projects.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();
$projResult = $db->getProjectById($connection, "projects", $id);
if($projResult->num_rows == 0){
    Header("Location: projects.php");
    exit();
}
$project = $projResult->fetch_assoc();
if((int)$project["workspace_id"] !== (int)$workspace_id){
    Header("Location: projects.php");
    exit();
}

$nameError = $_SESSION["editNameError"] ?? "";
$descError = $_SESSION["editDescError"] ?? "";
$deadlineError = $_SESSION["editDeadlineError"] ?? "";

$projName = $_SESSION["editName"] ?? $project["name"];
$projDesc = $_SESSION["editDesc"] ?? $project["description"];
$projDeadline = $_SESSION["editDeadline"] ?? $project["deadline"];
$projColor = $_SESSION["editColor"] ?? $project["color_label"];

unset($_SESSION["editNameError"]);
unset($_SESSION["editDescError"]);
unset($_SESSION["editDeadlineError"]);
unset($_SESSION["editName"]);
unset($_SESSION["editDesc"]);
unset($_SESSION["editDeadline"]);
unset($_SESSION["editColor"]);
?>
<html>
<head>
<title>Edit Project</title>
<script src="../Controller/JS/editProjectValidate.js"></script>
</head>
<body>
<h2>Edit project</h2>
<form method="post" action="../Controller/editProjectHandler.php" onsubmit="return validateEditProject()">
<input type="hidden" name="id" value="<?php echo $project["id"];?>"/>
<table>
<tr>
    <td>Project Name</td>
    <td><input type="text" name="name" id="editName" value="<?php echo $projName;?>"/></td>
    <td style="color:red"><?php echo $nameError;?></td>
    <td><p id="editNameJsError" style="color:red"></p></td>
</tr>
<tr>
    <td>Description</td>
    <td><textarea name="description" id="editDesc"><?php echo $projDesc;?></textarea></td>
    <td style="color:red"><?php echo $descError;?></td>
    <td><p id="editDescJsError" style="color:red"></p></td>
</tr>
<tr>
    <td>Deadline</td>
    <td><input type="date" name="deadline" id="editDeadline" value="<?php echo $projDeadline;?>"/></td>
    <td style="color:red"><?php echo $deadlineError;?></td>
</tr>
<tr>
    <td>Color label</td>
    <td><input type="color" name="color_label" value="<?php echo $projColor;?>"/></td>
</tr>
<tr>
    <td></td>
    <td><input type="submit" name="submit" value="Save changes"/></td>
</tr>
</table>
</form>
<p><a href="projectDetail.php?id=<?php echo $project["id"];?>">Back to project</a></p>
</body>
</html>
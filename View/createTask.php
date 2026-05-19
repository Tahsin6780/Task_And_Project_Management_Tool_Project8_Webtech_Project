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

$project_id = (int)($_GET["project_id"] ?? 0);
if($project_id <= 0){
    Header("Location: projects.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$projResult = $db->getProjectById($connection, "projects", $project_id);
if($projResult->num_rows == 0){
    Header("Location: projects.php");
    exit();
}
$project = $projResult->fetch_assoc();
if((int)$project["workspace_id"] !== (int)$workspace_id){
    Header("Location: projects.php");
    exit();
}

$projectMembers = $db->getProjectMembers($connection, $project_id);

$titleError = $_SESSION["taskTitleError"] ?? "";
$descError = $_SESSION["taskDescError"] ?? "";
$dueError = $_SESSION["taskDueError"] ?? "";
$taskTitle = $_SESSION["taskTitle"] ?? "";
$taskDesc = $_SESSION["taskDesc"] ?? "";
$taskAssignee = $_SESSION["taskAssignee"] ?? "";
$taskPriority = $_SESSION["taskPriority"] ?? "low";
$taskDue = $_SESSION["taskDue"] ?? "";

unset($_SESSION["taskTitleError"]);
unset($_SESSION["taskDescError"]);
unset($_SESSION["taskDueError"]);
unset($_SESSION["taskTitle"]);
unset($_SESSION["taskDesc"]);
unset($_SESSION["taskAssignee"]);
unset($_SESSION["taskPriority"]);
unset($_SESSION["taskDue"]);
?>
<html>
<head>
<title>Create Task</title>
<script src="../Controller/JS/createTaskValidate.js"></script>
</head>
<body>
<h2>Create task in: <?php echo $project["name"];?></h2>
<form method="post" action="../Controller/createTaskHandler.php" onsubmit="return validateCreateTask()">
<input type="hidden" name="project_id" value="<?php echo $project_id;?>"/>
<table>
<tr>
    <td>Title</td>
    <td><input type="text" name="title" id="taskTitle" value="<?php echo $taskTitle;?>"/></td>
    <td style="color:red"><?php echo $titleError;?></td>
    <td><p id="taskTitleJsError" style="color:red"></p></td>
</tr>
<tr>
    <td>Description</td>
    <td><textarea name="description" id="taskDesc"><?php echo $taskDesc;?></textarea></td>
    <td style="color:red"><?php echo $descError;?></td>
    <td><p id="taskDescJsError" style="color:red"></p></td>
</tr>
<tr>
    <td>Assignee</td>
    <td>
        <select name="assigned_to" id="taskAssignee">
            <option value="">Unassigned</option>
            <?php while($m = $projectMembers->fetch_assoc()){
                $sel = ($taskAssignee == $m["user_id"]) ? "selected" : "";
                echo "<option value='".$m["user_id"]."' $sel>".$m["name"]."</option>";
            } ?>
        </select>
    </td>
</tr>
<tr>
    <td>Priority</td>
    <td>
        <select name="priority" id="taskPriority">
            <option value="low" <?php if($taskPriority=="low") echo "selected";?>>Low</option>
            <option value="medium" <?php if($taskPriority=="medium") echo "selected";?>>Medium</option>
            <option value="high" <?php if($taskPriority=="high") echo "selected";?>>High</option>
        </select>
    </td>
</tr>
<tr>
    <td>Due date</td>
    <td><input type="date" name="due_date" id="taskDue" value="<?php echo $taskDue;?>"/></td>
    <td style="color:red"><?php echo $dueError;?></td>
    <td><p id="taskDueJsError" style="color:red"></p></td>
</tr>
<tr>
    <td></td>
    <td><input type="submit" name="submit" value="Create Task"/></td>
</tr>
</table>
</form>
<p><a href="projectBoard.php?project_id=<?php echo $project_id;?>">Back to board</a></p>
</body>
</html>

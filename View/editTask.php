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

$taskResult = $db->getTaskById($connection, "tasks", $id);
if($taskResult->num_rows == 0){
    Header("Location: projects.php");
    exit();
}
$task = $taskResult->fetch_assoc();

// check the task belongs to a project in this workspace
$projResult = $db->getProjectById($connection, "projects", $task["project_id"]);
if($projResult->num_rows == 0){
    Header("Location: projects.php");
    exit();
}
$project = $projResult->fetch_assoc();
if((int)$project["workspace_id"] !== (int)$workspace_id){
    Header("Location: projects.php");
    exit();
}

$projectMembers = $db->getProjectMembers($connection, $project["id"]);

$titleError = $_SESSION["editTaskTitleError"] ?? "";
$descError = $_SESSION["editTaskDescError"] ?? "";
$dueError = $_SESSION["editTaskDueError"] ?? "";

$taskTitle = $_SESSION["editTaskTitle"] ?? $task["title"];
$taskDesc = $_SESSION["editTaskDesc"] ?? $task["description"];
$taskAssignee = $_SESSION["editTaskAssignee"] ?? $task["assigned_to"];
$taskPriority = $_SESSION["editTaskPriority"] ?? $task["priority"];
$taskDue = $_SESSION["editTaskDue"] ?? $task["due_date"];

unset($_SESSION["editTaskTitleError"]);
unset($_SESSION["editTaskDescError"]);
unset($_SESSION["editTaskDueError"]);
unset($_SESSION["editTaskTitle"]);
unset($_SESSION["editTaskDesc"]);
unset($_SESSION["editTaskAssignee"]);
unset($_SESSION["editTaskPriority"]);
unset($_SESSION["editTaskDue"]);
?>
<html>
<head>
<title>Edit Task</title>
<script src="../Controller/JS/editTaskValidate.js"></script>
</head>
<body>
<h2>Edit task</h2>
<form method="post" action="../Controller/editTaskHandler.php" onsubmit="return validateEditTask()">
<input type="hidden" name="id" value="<?php echo $task["id"];?>"/>
<table>
<tr>
    <td>Title</td>
    <td><input type="text" name="title" id="editTaskTitle" value="<?php echo $taskTitle;?>"/></td>
    <td style="color:red"><?php echo $titleError;?></td>
    <td><p id="editTaskTitleJsError" style="color:red"></p></td>
</tr>
<tr>
    <td>Description</td>
    <td><textarea name="description" id="editTaskDesc"><?php echo $taskDesc;?></textarea></td>
    <td style="color:red"><?php echo $descError;?></td>
    <td><p id="editTaskDescJsError" style="color:red"></p></td>
</tr>
<tr>
    <td>Assignee</td>
    <td>
        <select name="assigned_to">
            <option value="">Unassigned</option>
            <?php while($m = $projectMembers->fetch_assoc()){
                $sel = ((string)$taskAssignee === (string)$m["user_id"]) ? "selected" : "";
                echo "<option value='".$m["user_id"]."' $sel>".$m["name"]."</option>";
            } ?>
        </select>
    </td>
</tr>
<tr>
    <td>Priority</td>
    <td>
        <select name="priority">
            <option value="low" <?php if($taskPriority=="low") echo "selected";?>>Low</option>
            <option value="medium" <?php if($taskPriority=="medium") echo "selected";?>>Medium</option>
            <option value="high" <?php if($taskPriority=="high") echo "selected";?>>High</option>
        </select>
    </td>
</tr>
<tr>
    <td>Due date</td>
    <td><input type="date" name="due_date" id="editTaskDue" value="<?php echo $taskDue;?>"/></td>
    <td style="color:red"><?php echo $dueError;?></td>
</tr>
<tr>
    <td></td>
    <td><input type="submit" name="submit" value="Save changes"/></td>
</tr>
</table>
</form>
<p><a href="projectBoard.php?project_id=<?php echo $project["id"];?>">Back to board</a></p>
</body>
</html>

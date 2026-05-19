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

$projResult = $db->getProjectById($connection, "projects", $task["project_id"]);
$project = $projResult->fetch_assoc();
if((int)$project["workspace_id"] !== (int)$workspace_id){
    Header("Location: projects.php");
    exit();
}

$assigneeName = "Unassigned";
if($task["assigned_to"]){
    $userRes = $db->getUserById($connection, "users", (int)$task["assigned_to"]);
    if($userRes && $userRes->num_rows > 0){
        $u = $userRes->fetch_assoc();
        $assigneeName = $u["name"];
    }
}
?>
<html>
<head>
<title>Task: <?php echo $task["title"];?></title>
<script src="../Controller/JS/createCommentValidate.js"></script>
<script src="../Controller/JS/deleteComment.js"></script>
<style>
    body{font-family:Arial,sans-serif;margin:20px;}
    .priority{display:inline-block;padding:3px 9px;border-radius:10px;font-size:12px;color:white;}
    .priority.low{background:#3b82f6;}
    .priority.medium{background:#f59e0b;}
    .priority.high{background:#ef4444;}
    .status{display:inline-block;padding:3px 9px;border-radius:10px;font-size:12px;background:#888;color:white;}
</style>
</head>
<body>
<p><a href="projectBoard.php?project_id=<?php echo $project["id"];?>">← Back to board</a></p>
<h2><?php echo $task["title"];?></h2>
<p>
    <span class="priority <?php echo $task["priority"];?>"><?php echo $task["priority"];?></span>
    <span class="status"><?php echo $task["status"];?></span>
</p>
<p><strong>Project:</strong> <?php echo $project["name"];?></p>
<p><strong>Assignee:</strong> <?php echo $assigneeName;?></p>
<p><strong>Due:</strong> <?php echo $task["due_date"] ?? "—"; ?></p>
<p><strong>Created:</strong> <?php echo $task["created_at"]; ?></p>
<hr/>
<h3>Description</h3>
<p><?php echo nl2br($task["description"]);?></p>
<hr/>
<p>
    <a href="editTask.php?id=<?php echo $task["id"];?>"><button>Edit</button></a>
</p>
    //Member 4 comment part
<h3>Comments</h3>
<?php
$comments = $db->getCommentsForTask($connection, $task["id"]);
$myId = (int)$_SESSION["user_id"];
$commentError = $_SESSION["commentError"] ?? "";
$commentBody = $_SESSION["commentBody"] ?? "";
unset($_SESSION["commentError"]);
unset($_SESSION["commentBody"]);
?>
<div id="commentsList">
<?php while($c = $comments->fetch_assoc()){ ?>
    <div class="comment" id="comment_<?php echo $c["id"];?>" style="background:#f4f5f7;padding:10px;margin:8px 0;border-radius:6px;transition:opacity 0.5s;">
        <p style="margin:0;"><strong><?php echo $c["author_name"];?></strong> <span style="color:#888;font-size:12px;"><?php echo $c["created_at"];?></span></p>
        <p style="margin:5px 0 0 0;"><?php echo nl2br($c["body"]);?></p>
        <?php if((int)$c["user_id"] === $myId){ ?>
            <button onclick="deleteComment(<?php echo $c["id"];?>)" style="font-size:12px;color:#ef4444;background:none;border:none;cursor:pointer;">Delete</button>
        <?php } ?>
    </div>
<?php } ?>
</div>
<form method="post" action="../Controller/createCommentHandler.php" onsubmit="return validateCreateComment()" style="margin-top:15px;">
<input type="hidden" name="task_id" value="<?php echo $task["id"];?>"/>
<table>
<tr>
    <td>Add a comment</td>
    <td><textarea name="body" id="commentBody" rows="3" cols="40"><?php echo $commentBody;?></textarea></td>
    <td style="color:red"><?php echo $commentError;?></td>
    <td><p id="commentJsError" style="color:red"></p></td>
</tr>
<tr>
    <td></td>
    <td><input type="submit" value="Post comment"/></td>
</tr>
</table>
</form>
</body>
</html>

<?php
include "../Model/DatabaseConnection.php";
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: login.php");
    exit();
}

$workspace_id = $_SESSION["workspace_id"] ?? 0;
$user_id = $_SESSION["user_id"];
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

$projectMembers = $db->getProjectMembers($connection, $id);
$available = $db->getWorkspaceMembersNotInProject($connection, $workspace_id, $id);
?>
<html>
<head>
<title>Project: <?php echo $project["name"];?></title>
<script src="../Controller/JS/archiveProject.js"></script>
<script src="../Controller/JS/deleteProject.js"></script>
<script src="../Controller/JS/addProjectMember.js"></script>
<script src="../Controller/JS/removeProjectMember.js"></script>
<style>
    body{font-family:Arial,sans-serif;margin:20px;}
    .color-bar{height:8px;border-radius:4px;margin-bottom:15px;}
    table{border-collapse:collapse;margin-top:10px;}
    td,th{border:1px solid #ccc;padding:8px 12px;}
    .fading{transition:opacity 0.6s;opacity:0;}
    .actions{margin:15px 0;}
    .actions button{margin-right:8px;}
</style>
</head>
<body>
<p><a href="projects.php">← Back to projects</a></p>
<div class="color-bar" style="background:<?php echo $project["color_label"];?>;"></div>
<h2><?php echo $project["name"];?><?php if($project["is_archived"]) echo " <em>(archived)</em>"; ?></h2>
<p><?php echo $project["description"];?></p>
<p><strong>Deadline:</strong> <?php echo $project["deadline"] ?? "—"; ?></p>
<p><strong>Created:</strong> <?php echo $project["created_at"]; ?></p>

<div class="actions">
    <a href="editProject.php?id=<?php echo $project["id"];?>"><button>Edit</button></a>
    <button onclick="toggleArchive(<?php echo $project["id"];?>, <?php echo $project["is_archived"] ? 0 : 1;?>)" id="archiveBtn">
        <?php echo $project["is_archived"] ? "Unarchive" : "Archive"; ?>
    </button>
    <button onclick="deleteProject(<?php echo $project["id"];?>)">Delete</button>
</div>
<p id="projectActionResponse" style="color:red"></p>

<h3>Tasks</h3>
<p>
    <a href="projectBoard.php?project_id=<?php echo $project["id"];?>"><button>Open Board</button></a>
    <a href="createTask.php?project_id=<?php echo $project["id"];?>"><button>+ New Task</button></a>
</p>

<h3>Members</h3>
<table id="projectMembersTable">
<tr><th>Name</th><th>Email</th><th>Action</th></tr>
<?php while($m = $projectMembers->fetch_assoc()){
    echo "<tr id='pmRow_".$m["user_id"]."'>";
    echo "<td>".$m["name"]."</td>";
    echo "<td>".$m["email"]."</td>";
    echo "<td><button onclick='removeProjectMember(".$project["id"].", ".$m["user_id"].")'>Remove</button></td>";
    echo "</tr>";
} ?>
</table>
<p id="projectMemberResponse" style="color:red"></p>

<h4>Add a member from this workspace</h4>
<select id="availableMembers">
<?php while($u = $available->fetch_assoc()){
    echo "<option value='".$u["id"]."'>".$u["name"]." (".$u["email"].")</option>";
} ?>
</select>
<button onclick="addProjectMember(<?php echo $project["id"];?>)">Add</button>
</body>
</html>

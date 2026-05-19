<?php
include "../Model/DatabaseConnection.php";
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$name = $_SESSION["name"];
$workspace_id = $_SESSION["workspace_id"] ?? null;

if(!$workspace_id){
    Header("Location: workspaceChoice.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$wsResult = $db->getWorkspaceById($connection, "workspaces", $workspace_id);
$currentWorkspace = $wsResult->fetch_assoc();

$myWorkspaces = $db->getWorkspacesForUser($connection, $user_id);
?>
<html>
<head>
<title>Dashboard - <?php echo $currentWorkspace["name"];?></title>
<style>
    body{font-family:Arial,sans-serif;margin:0;}
    .navbar{background:#222;color:white;padding:10px 20px;display:flex;justify-content:space-between;align-items:center;}
    .navbar a{color:white;text-decoration:none;margin-left:15px;}
    .navbar select{padding:5px;}
    .container{padding:20px;}
</style>
</head>
<body>
<div class="navbar">
    <div>
        <strong>TaskHub</strong>
        &nbsp;|&nbsp;
        <form method="get" action="../Controller/switchWorkspace.php" style="display:inline;">
            <select name="id" onchange="this.form.submit()">
                <?php while($w = $myWorkspaces->fetch_assoc()){
                    $selected = ($w["id"] == $workspace_id) ? "selected" : "";
                    echo "<option value='".$w["id"]."' $selected>".$w["name"]."</option>";
                } ?>
            </select>
        </form>
    </div>
    <div>
        <span>Hello, <?php echo $name;?></span>
        <a href="projects.php">Projects</a>
        <a href="workspaceActivity.php">Activity</a>
        <a href="workspaceSettings.php">Workspace Settings</a>
        <a href="workspaceChoice.php">+ New / Join</a>
        <a href="../Controller/logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2><?php echo $currentWorkspace["name"];?></h2>
    <p><?php echo $currentWorkspace["description"];?></p>
    <p>Invite code: <strong><?php echo $currentWorkspace["invite_code"];?></strong></p>
    <hr/>
    <h3>Projects</h3>
    <p><a href="projects.php"><button>View all projects</button></a> &nbsp; <a href="createProject.php"><button>+ New project</button></a></p>
</div>
</body>
</html>

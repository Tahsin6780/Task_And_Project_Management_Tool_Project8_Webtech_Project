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

$db = new DatabaseConnection();
$connection = $db->openConnection();

$wsResult = $db->getWorkspaceById($connection, "workspaces", $workspace_id);
$workspace = $wsResult->fetch_assoc();

$activity = $db->getActivityLogsForWorkspace($connection, $workspace_id);
?>
<html>
<head>
<title>Recent Activity — <?php echo $workspace["name"];?></title>
<style>
    body{font-family:Arial,sans-serif;margin:0;background:#f4f5f7;}
    .topbar{background:#222;color:white;padding:10px 20px;display:flex;justify-content:space-between;align-items:center;}
    .topbar a{color:white;text-decoration:none;margin-left:15px;}
    .container{padding:20px;max-width:700px;}
    .log-row{background:white;border-left:3px solid #3b82f6;padding:8px 12px;margin:6px 0;border-radius:4px;}
    .log-row .who{font-weight:bold;}
    .log-row .where{color:#3b82f6;}
    .log-row .when{display:block;color:#888;font-size:12px;margin-top:3px;}
</style>
</head>
<body>
<div class="topbar">
    <div><strong>TaskHub</strong> &nbsp;|&nbsp; Recent Activity</div>
    <div>
        <a href="dashboard.php">← Dashboard</a>
        <a href="projects.php">Projects</a>
        <a href="../Controller/logout.php">Logout</a>
    </div>
</div>
<div class="container">
    <h2>Recent activity in <?php echo $workspace["name"];?></h2>
    <?php if($activity->num_rows == 0){ ?>
        <p><em>No activity yet — start creating tasks and commenting.</em></p>
    <?php }else{ ?>
        <?php while($a = $activity->fetch_assoc()){ ?>
            <div class="log-row">
                <span class="who"><?php echo $a["user_name"];?></span>
                <?php echo $a["action_text"];?>
                <span class="where">in <?php echo $a["project_name"];?></span>
                <span class="when"><?php echo $a["created_at"];?></span>
            </div>
        <?php } ?>
    <?php } ?>
</div>
</body>
</html>

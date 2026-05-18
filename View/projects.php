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
$workspace = $wsResult->fetch_assoc();

$activeProjects = $db->getProjectsForWorkspace($connection, $workspace_id, false);
$allProjects = $db->getProjectsForWorkspace($connection, $workspace_id, true);

$createMessage = $_SESSION["createMessage"] ?? "";
unset($_SESSION["createMessage"]);
?>
<html>
<head>
<title>Projects - <?php echo $workspace["name"];?></title>
<style>
    body{font-family:Arial,sans-serif;margin:0;}
    .navbar{background:#222;color:white;padding:10px 20px;display:flex;justify-content:space-between;align-items:center;}
    .navbar a{color:white;text-decoration:none;margin-left:15px;}
    .container{padding:20px;}
    .project-card{display:inline-block;width:280px;margin:10px;padding:15px;border-radius:8px;border:1px solid #ddd;vertical-align:top;}
    .project-card .color-bar{height:6px;border-radius:3px;margin-bottom:10px;}
    .project-card h3{margin:0 0 6px 0;}
    .project-card p{margin:4px 0;color:#555;font-size:14px;}
    .project-card .actions{margin-top:10px;}
    .archived{opacity:0.55;}
    .success{color:green;font-weight:bold;}
</style>
</head>
<body>
<div class="navbar">
    <div>
        <strong>TaskHub</strong> &nbsp;|&nbsp; <?php echo $workspace["name"];?>
    </div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="projects.php">Projects</a>
        <a href="createProject.php">+ New Project</a>
        <span>Hello, <?php echo $name;?></span>
        <a href="../Controller/logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Projects in <?php echo $workspace["name"];?></h2>
    <p class="success"><?php echo $createMessage;?></p>

    <h3>Active</h3>
    <?php
    $hasActive = false;
    while($p = $activeProjects->fetch_assoc()){
        $hasActive = true;
        echo "<div class='project-card' id='projectCard_".$p["id"]."'>";
        echo "<div class='color-bar' style='background:".$p["color_label"].";'></div>";
        echo "<h3>".$p["name"]."</h3>";
        echo "<p>".$p["description"]."</p>";
        echo "<p><strong>Deadline:</strong> ".($p["deadline"] ?? "—")."</p>";
        echo "<div class='actions'>";
        echo "<a href='projectDetail.php?id=".$p["id"]."'><button>Open</button></a> ";
        echo "<a href='editProject.php?id=".$p["id"]."'><button>Edit</button></a>";
        echo "</div>";
        echo "</div>";
    }
    if(!$hasActive){
        echo "<p><em>No active projects yet. Click '+ New Project' to create one.</em></p>";
    }
    ?>

    <hr style="margin-top:30px;"/>
    <h3>Archived</h3>
    <?php
    $hasArchived = false;
    $allProjects->data_seek(0);
    while($p = $allProjects->fetch_assoc()){
        if((int)$p["is_archived"] !== 1) continue;
        $hasArchived = true;
        echo "<div class='project-card archived'>";
        echo "<div class='color-bar' style='background:".$p["color_label"].";'></div>";
        echo "<h3>".$p["name"]." (archived)</h3>";
        echo "<p>".$p["description"]."</p>";
        echo "<div class='actions'>";
        echo "<a href='projectDetail.php?id=".$p["id"]."'><button>Open</button></a>";
        echo "</div>";
        echo "</div>";
    }
    if(!$hasArchived){
        echo "<p><em>No archived projects.</em></p>";
    }
    ?>
</div>
</body>
</html>

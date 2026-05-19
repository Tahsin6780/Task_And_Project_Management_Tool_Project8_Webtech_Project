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
$tasks = $db->getTasksForProject($connection, $project_id);

// group tasks into columns
$todo = [];
$inProgress = [];
$done = [];
while($t = $tasks->fetch_assoc()){
    if($t["status"] === "todo"){ $todo[] = $t; }
    else if($t["status"] === "in-progress"){ $inProgress[] = $t; }
    else if($t["status"] === "done"){ $done[] = $t; }
}

// for assignee filter dropdown
$projectMembersArr = [];
$projectMembers->data_seek(0);
while($m = $projectMembers->fetch_assoc()){ $projectMembersArr[] = $m; }
?>
<html>
<head>
<title>Board: <?php echo $project["name"];?></title>
<script src="../Controller/JS/updateTaskStatus.js"></script>
<script src="../Controller/JS/deleteTask.js"></script>
<script src="../Controller/JS/filterTasks.js"></script>
<style>
    body{font-family:Arial,sans-serif;margin:0;background:#f4f5f7;}
    .topbar{background:#222;color:white;padding:10px 20px;display:flex;justify-content:space-between;align-items:center;}
    .topbar a{color:white;text-decoration:none;margin-left:15px;}
    .container{padding:20px;}
    .filters{margin-bottom:15px;background:white;padding:10px;border-radius:6px;}
    .filters label{margin-right:6px;}
    .filters select{margin-right:15px;}
    .board{display:flex;gap:15px;align-items:flex-start;}
    .column{flex:1;background:#ebecf0;border-radius:8px;padding:10px;min-height:400px;}
    .column h3{margin-top:0;text-transform:uppercase;font-size:14px;color:#444;}
    .task-card{background:white;padding:10px;border-radius:6px;margin-bottom:8px;box-shadow:0 1px 3px rgba(0,0,0,0.08);transition:opacity 0.5s;}
    .task-card.fading{opacity:0;}
    .task-card h4{margin:0 0 6px 0;font-size:15px;}
    .task-card .meta{font-size:12px;color:#666;margin:3px 0;}
    .priority{display:inline-block;padding:2px 7px;border-radius:10px;font-size:11px;color:white;}
    .priority.low{background:#3b82f6;}
    .priority.medium{background:#f59e0b;}
    .priority.high{background:#ef4444;}
    .card-actions{margin-top:8px;}
    .card-actions button{font-size:12px;margin-right:4px;padding:3px 6px;cursor:pointer;}
</style>
</head>
<body>
<div class="topbar">
    <div>
        <strong>TaskHub</strong> &nbsp;|&nbsp; <?php echo $project["name"];?> Board
    </div>
    <div>
        <a href="projectDetail.php?id=<?php echo $project_id;?>">← Project</a>
        <a href="createTask.php?project_id=<?php echo $project_id;?>">+ New Task</a>
        <a href="projects.php">All projects</a>
        <a href="../Controller/logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="filters">
        <label>Assignee:</label>
        <select id="filterAssignee" onchange="applyFilters()">
            <option value="">All</option>
            <option value="unassigned">Unassigned</option>
            <?php foreach($projectMembersArr as $m){
                echo "<option value='".$m["user_id"]."'>".$m["name"]."</option>";
            } ?>
        </select>
        <label>Priority:</label>
        <select id="filterPriority" onchange="applyFilters()">
            <option value="">All</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
        </select>
        <span id="filterCount" style="margin-left:15px;color:#666;"></span>
    </div>

    <div class="board">
        <?php
        function renderColumn($title, $statusKey, $tasks, $project_id){
            echo "<div class='column' data-status='".$statusKey."'>";
            echo "<h3>$title (".count($tasks).")</h3>";
            echo "<div class='column-body' id='col_".$statusKey."'>";
            foreach($tasks as $t){
                $assignee = $t["assignee_name"] ?? "Unassigned";
                $assigneeId = $t["assigned_to"] ?? "";
                echo "<div class='task-card' id='task_".$t["id"]."' data-assignee='".$assigneeId."' data-priority='".$t["priority"]."'>";
                echo "<h4>".$t["title"]."</h4>";
                echo "<div class='meta'>☆ <span class='priority ".$t["priority"]."'>".$t["priority"]."</span></div>";
                echo "<div class='meta'>👤 ".$assignee."</div>";
                if($t["due_date"]){
                    echo "<div class='meta'>📅 ".$t["due_date"]."</div>";
                }
                echo "<div class='card-actions'>";
                if($statusKey !== "todo"){
                    $leftStatus = ($statusKey === "done") ? "in-progress" : "todo";
                    echo "<button onclick=\"moveTask(".$t["id"].",'".$leftStatus."')\">◀</button>";
                }
                if($statusKey !== "done"){
                    $rightStatus = ($statusKey === "todo") ? "in-progress" : "done";
                    echo "<button onclick=\"moveTask(".$t["id"].",'".$rightStatus."')\">▶</button>";
                }
                echo "<a href='taskDetail.php?id=".$t["id"]."'><button>Open</button></a>";
                echo "<button onclick='deleteTask(".$t["id"].")' style='color:#ef4444;'>×</button>";
                echo "</div>";
                echo "</div>";
            }
            echo "</div></div>";
        }

        renderColumn("To do", "todo", $todo, $project_id);
        renderColumn("In progress", "in-progress", $inProgress, $project_id);
        renderColumn("Done", "done", $done, $project_id);
        ?>
    </div>
</div>
</body>
</html>

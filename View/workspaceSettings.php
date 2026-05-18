<?php
include "../Model/DatabaseConnection.php";
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$workspace_id = $_SESSION["workspace_id"] ?? null;
if(!$workspace_id){
    Header("Location: workspaceChoice.php");
    exit();
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$wsResult = $db->getWorkspaceById($connection, "workspaces", $workspace_id);
if($wsResult->num_rows == 0){
    Header("Location: dashboard.php");
    exit();
}
$workspace = $wsResult->fetch_assoc();

if((int)$workspace["owner_id"] !== (int)$user_id){
    echo "<h3 style='color:red'>Only the workspace owner can access this page.</h3>";
    echo "<a href='dashboard.php'>Back to dashboard</a>";
    exit();
}

$members = $db->getWorkspaceMembers($connection, $workspace_id);
?>
<html>
<head>
<title>Workspace Settings</title>
<script src="../Controller/JS/removeMember.js"></script>
<style>
    body{font-family:Arial,sans-serif;margin:20px;}
    table{border-collapse:collapse;}
    td,th{border:1px solid #ccc;padding:8px 12px;}
    .fading{transition:opacity 0.6s;opacity:0;}
</style>
</head>
<body>
<h2>Workspace Settings — <?php echo $workspace["name"];?></h2>
<p><a href="dashboard.php">← Back to dashboard</a></p>
<p>Invite code: <strong><?php echo $workspace["invite_code"];?></strong></p>

<h3>Members</h3>
<table id="membersTable">
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Joined</th>
    <th>Action</th>
</tr>
<?php while($m = $members->fetch_assoc()){
    $isSelf = ((int)$m["user_id"] === (int)$user_id);
    echo "<tr id='memberRow_".$m["member_row_id"]."'>";
    echo "<td>".$m["name"]."</td>";
    echo "<td>".$m["email"]."</td>";
    echo "<td>".$m["joined_at"]."</td>";
    if($isSelf){
        echo "<td><em>You (owner)</em></td>";
    }else{
        echo "<td><button onclick='removeMember(".$m["member_row_id"].")'>Remove</button></td>";
    }
    echo "</tr>";
} ?>
</table>
<p id="removeResponse" style="color:red"></p>
</body>
</html>

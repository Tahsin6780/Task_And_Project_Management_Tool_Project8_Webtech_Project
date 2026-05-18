<?php
session_start();
$isLoggedIn = $_SESSION["isLoggedIn"] ?? "";
if(!$isLoggedIn){
    Header("Location: login.php");
    exit();
}
$name = $_SESSION["name"];
?>
<html>
<head><title>Workspace</title></head>
<body>
<h2>Welcome, <?php echo $name;?></h2>
<p>You haven't selected a workspace yet. Pick one:</p>
<table>
<tr>
    <td><a href="createWorkspace.php"><button>Create a new workspace</button></a></td>
    <td><a href="joinWorkspace.php"><button>Join an existing workspace</button></a></td>
    <td><a href="../Controller/logout.php"><button>Logout</button></a></td>
</tr>
</table>
</body>
</html>

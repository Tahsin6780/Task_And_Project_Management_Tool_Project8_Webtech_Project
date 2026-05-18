<?php
session_start();
session_destroy();
setcookie("remember_email", "", time() - 1, "/");
Header("Location: ../View/login.php");
exit();
?>

<?php
session_start();
unset($_SESSION['user']);unset($_SESSION['qstr']);
session_destroy();
header("location:/login.php");
?>

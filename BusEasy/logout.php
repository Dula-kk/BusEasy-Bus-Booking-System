<?php
include 'Classes/Authenticate.php';
session_start();

$auth = new Authenticate();
$auth->logout();

header("Location: login.php");  
exit();
?>

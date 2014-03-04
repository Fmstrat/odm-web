<?php
include 'include/core.php';

if (isset($_GET['id']))
	$id = $_GET['id'];
	
$page = "main";
if (isset($_GET['p']))
	$page = $_GET['p'];

include 'include/pageparts/header.php';

switch($page) {
	case "main": include("include/pageparts/main.php"); include("include/pageparts/newdevice.php"); break;
	case "login": include("include/pageparts/login.php"); break;
	case "register": include("include/pageparts/register.php"); break;
	case "changepassword": include("include/pageparts/changepassword.php"); break;
	default: include("include/pageparts/main.php"); break;
}

include 'include/pageparts/footer.php';
?>

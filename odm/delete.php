<?php

	include 'include/config.php';
	include 'include/db.php';
	include 'include/gcm.php';

	dbconnect();

	include 'include/checklogin.php';

	if (isset($_GET["id"])) {
		$id = $_GET["id"];
		deleteDevice($id, $_COOKIE['user_id']);
	}
	
	dbclose();
	header("Location: index.php");
?>

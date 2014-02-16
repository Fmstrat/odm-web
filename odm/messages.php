<?php
	include 'include/config.php';
	include 'include/db.php';

	dbconnect();

	include 'include/checklogin.php';

	$regId = $_GET['regId'];
	$n = $_GET['n'];
	if (validateRegId($_COOKIE['user_id'], $_GET['regId'])) {
		$messages = getMessages($regId, $n);
		echo json_encode($messages);
	}
	dbclose();
?>

<?php

	include 'include/config.php';
	include 'include/db.php';
	include 'include/gcm.php';
	include 'include/mcrypt.php';

	dbconnect();

	include 'include/checklogin.php';

	if (isset($_POST["regId"]) && isset($_POST["message"]) && isset($_POST["token"])) {
		$regId = $_POST["regId"];
		$message = $_POST["message"];
		$token = $_POST["token"];
		$mcrypt = new MCrypt();
		$key = $mcrypt->formatKey($token);
		$encrypted = $mcrypt->encrypt($message, $key);
		$registration_ids = array($regId);
		$messageA = array("message" => $encrypted);
		$result = send_notification($registration_ids, $messageA);
	}
	
	dbclose();
?>

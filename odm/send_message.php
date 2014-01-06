<?php

	include 'include/config.php';
	include 'include/db.php';
	include 'include/gcm.php';
	include 'include/mcrypt.php';

	dbconnect();

	include 'include/checklogin.php';

	if (isset($_POST["regId"]) && isset($_POST["message"]) && isset($_POST["enc_key"])) {
		$regId = $_POST["regId"];
		$message = $_POST["message"];
		$enc_key = $_POST["enc_key"];
		$mcrypt = new MCrypt();
		$key = $mcrypt->formatKey($enc_key);
		$encrypted = $mcrypt->encrypt($message, $key);
		$registration_ids = array($regId);
		$messageA = array("message" => $encrypted);
		$result = send_notification($registration_ids, $messageA);
	}
	
	dbclose();
?>

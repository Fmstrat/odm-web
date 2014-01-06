<?php

	include '../include/config.php';
	include '../include/db.php';
	include '../include/gcm.php';

	dbconnect();

	include '../include/checkprelogin.php';

	if (isset($_POST["name"]) && isset($_POST["password"]) && isset($_POST["regId"])) {
		$name = $_POST["name"];
		$password = $_POST["password"];
		$gcm_regid = $_POST["regId"]; // GCM Registration ID
		storeUser($name, $password, $gcm_regid, $user_id);
		echo "success";
		//$registation_ids = array($gcm_regid);
		//$message = array("message" => "Successful Registration");
		//$result = send_notification($registation_ids, $message);
	} else {
		echo "missing fields";
	}

	dbclose();

?>

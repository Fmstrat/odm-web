<?php

	include '../include/config.php';
	include '../include/db.php';

	dbconnect();

	include '../include/checkprelogin.php';

	if (isset($_POST["message"]) && isset($_POST["regId"])) {
		$message = $_POST["message"];
		$gcm_regid = $_POST["regId"]; // GCM Registration ID
		$data = 0;
		if (isset($_POST["data"]))
			$data = 1;
		$mid = storeMessage($message, $gcm_regid, $data);
		if (isset($_POST["data"]))
			storeData($mid, $_POST["data"]);
	} else {
		// message details missing
	}

	dbclose();

?>

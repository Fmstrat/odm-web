<?php

	include '../include/config.php';
	include '../include/db.php';

	dbconnect();

	include '../include/checkprelogin.php';

	if (isset($_POST["message"]) && isset($_POST["regId"])) {
		$message = $_POST["message"];
		$gcm_regid = $_POST["regId"]; // GCM Registration ID
		$data = 0;
		if (isset($_FILES['upfile']) && isset($_FILES['upfile']['tmp_name']) && $_FILES['upfile']['tmp_name'] != "") {
			$data = 2;
		}
		if (isset($_FILES['upfile']['error'])) {
			switch ($_FILES['upfile']['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					error_log('Error: No file sent.');
					break;
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					error_log('Error: Exceeded PHP filesize limit.');
					break;
				default:
					error_log('Error: Unknown error.');
					break;
			}
		}
		$mid = storeMessage($message, $gcm_regid, $data);
		if (isset($_FILES['upfile']) && isset($_FILES['upfile']['tmp_name']) && $_FILES['upfile']['tmp_name'] != "") {
			$handle = fopen($_FILES['upfile']['tmp_name'], "r");
			storeFile($mid, $handle);
			fclose($handle);
		}
	} else {
		// message details missing
	}

	dbclose();

?>

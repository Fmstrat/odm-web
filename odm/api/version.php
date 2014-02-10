<?php

	include '../include/config.php';
	include '../include/db.php';
	include '../include/version.php';

	dbconnect();

	include '../include/checkprelogin.php';

	dbclose();

	echo $version;
?>

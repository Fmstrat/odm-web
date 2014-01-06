<?php
	if (isset($_COOKIE['user_id']) && isset($_COOKIE['hash']) && isset($_COOKIE['username'])) {
		$user = getUserRecord($_COOKIE['username']);
		if ($_COOKIE['user_id'] != $user['user_id'] || $_COOKIE['hash'] != $user['hash']) {
			header("Location: login.php");
			exit;
		}
	} else {
		header("Location: login.php");
		exit;
	}
?>

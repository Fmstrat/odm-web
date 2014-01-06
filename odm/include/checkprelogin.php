<?php

	$username = "";
	$password = "";
	if (isset($_POST["username"])) $username = $_POST["username"];
	if (isset($_POST["password"])) $password = $_POST["password"];
	$user = getUserRecord($username);
	if (crypt($password, $user['hash']) != $user['hash']) {
		dbclose();
		echo "invalid password";
		exit;
	}
	$user_id = $user['user_id'];

?>

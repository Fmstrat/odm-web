<?php
	if (isset($_COOKIE['user_id'])) {
		unset($_COOKIE['user_id']);
		unset($_COOKIE['username']);
		setcookie('user_id', "", time()-3600);
		setcookie('username', "", time()-3600);
	}
	header("Location: login.php");
?>

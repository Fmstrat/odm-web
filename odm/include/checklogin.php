<?php
if (!isset($_SESSION['user_id']) || !isset($_SESSION['token']) || !isset($_SESSION['username'])) {
	header("Location: $WEB_BASE_PATH/odm.php?p=login");
	exit;
}
?>

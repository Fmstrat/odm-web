<?php
	include 'include/config.php';
	include 'include/db.php';

	dbconnect();

	include 'include/checklogin.php';

	$id = $_GET['id'];
	$data = $_GET['data'];
	$w = $_GET['w'];
	$h = $_GET['h'];
	//$image = getImg($_COOKIE['user_id'], $id);
	//echo '<img style="max-width:'.$w.'px; max-height:'.$h.'px" src="data:image/jpg;base64,'.$image.'">';
	echo '<img style="max-width:'.$w.'px; max-height:'.$h.'px" src="img-data.php?data='.$data.'&id='.$id.'">';
	dbclose();
?>

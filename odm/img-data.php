<?php
	include 'include/config.php';
	include 'include/db.php';

	dbconnect();

	include 'include/checklogin.php';

	$id = $_GET['id'];
	$data = $_GET['data'];
	$image = getImg($_COOKIE['user_id'], $id);
	$filename = getFilename($_COOKIE['user_id'], $id);
	$file_extension = strtolower(substr(strrchr($filename,"."),1));
	$ctype = "";
	switch ($file_extension) {
		case "gif": $ctype="image/gif"; break;
		case "png": $ctype="image/png"; break;
		case "jpeg":
		case "jpg": $ctype="image/jpg"; break;
		case "GIF": $ctype="image/gif"; break;
		case "PNG": $ctype="image/png"; break;
		case "JPEG":
		case "JPG": $ctype="image/jpg"; break;
		default:
	}
	//echo '<img style="max-width:'.$w.'px; max-height:'.$h.'px" src="data:image/jpg;base64,'.$image.'">';
	header('Content-type: ' . $ctype);
	if ($data == 2) {
		echo $image;
	} else {
		echo base64_decode($image);
	}
	//echo $image;
	
	dbclose();
?>

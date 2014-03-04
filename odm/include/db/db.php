<?php
$con = null;

function dbconnect() {
	global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_DATABASE, $con;
	$con = new PDO('mysql:dbname='.$DB_DATABASE.';host='.$DB_HOST.';charset=utf8', $DB_USER, $DB_PASSWORD);
	$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$con->setAttribute(PDO::MYSQL_ATTR_MAX_BUFFER_SIZE, 1024*1024*10); // set limit to 10MB
}

function dbclose() {
	global $con;
	$con = null;
}
?>
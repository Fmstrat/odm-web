<?php
$username = "";
$password = "";
if (isset($_POST["username"])) $username = $_POST["username"];
if (isset($_POST["password"])) $password = $_POST["password"];
$DEVMAN = DeviceManager::getInstance(); // there will be no session id set, so devman loads all users
$user = $DEVMAN->getUserByName($username);

if ($user === null || !$user->checkPassword($password)) {
	echo json_encode(array("result"=>false,"error"=>"unauthorized"));
	exit;
} else {
	// AUthorize connection by setting up a session
	$_SESSION["user_id"] =  $user->id;
	$_SESSION["username"] = $user->username;
	$_SESSION["token"] = $user->token;
}
?>

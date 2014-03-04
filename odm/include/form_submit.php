<?php
function submitForm() {
	$response = array();

	$DEVMAN = DeviceManager::getInstance();

	if(!isset($_GET["form"]) || !isset($_POST["fields"])) {
		$response["result"] = "false";
		$response["msg"] = "Incorrect call";
	} else {
		switch($_GET["form"]) {
			case "login":
				$username = "";
				$password = "";
				$err = false;
				
				// Simple input checks
				if (isset($_POST["fields"]["username"])) $username = $_POST["fields"]["username"];
				else {
					$response["result"] = "false";
					$response["msg"] = _("form_username_empty");
					$err = true;
				}
				if (isset($_POST["fields"]["password"])) $password = $_POST["fields"]["password"];
				else {
					$response["result"] = "false";
					$response["msg"] = _("form_password_empty");
					$err = true;
				}
				
				if(!$err){
					$user = $DEVMAN->getUserByName($username);				
					if($user != null && $user->checkPassword($password)) {
						$_SESSION["user_id"] =  $user->id;
						$_SESSION["username"] = $user->username;
						$_SESSION["token"] = $user->token;
						
						//register a global user object for the current user
						$DEVMAN->current_user = $user;
						$response["result"] = "success";
					} else {
						$response["result"] = "false";
						$response["msg"] = _("form_login_incorrect");
					}
					
				}
				break;
				
			case "register":
				global $ALLOW_REGISTRATIONS;
				if (!$ALLOW_REGISTRATIONS) {
					$response["result"] = "false";
					$response["msg"] = _("form_no_registration");
				} else {
					$username = "";
					$password = "";
					$email = "";
					$confirm_password = "";
					$err = false;
					
					// Simple input checks
					if (isset($_POST["fields"]["username"])) $username = $_POST["fields"]["username"];
					else {
						$response["result"] = "false";
						$response["msg"] = _("form_username_empty");
						$err = true;
					}
					if (isset($_POST["fields"]["password"])) $password = $_POST["fields"]["password"];
					else {
						$response["result"] = "false";
						$response["msg"] = _("form_password_empty");
						$err = true;
					}
					if (isset($_POST["fields"]["email"])) $email = $_POST["fields"]["email"];
					else {
						$response["result"] = "false";
						$response["msg"] = _("form_email_empty");
						$err = true;
					}
					if (isset($_POST["fields"]["password2"])) $confirm_password = $_POST["fields"]["password2"];
					else {
						$response["result"] = "false";
						$response["msg"] = _("form_password_empty");
						$err = true;
					}
					
					// check pw and length
					if(!$err){
						if ($password != $confirm_password) {
							$response["result"] = "false";
							$response["msg"] = _("form_password_not_match");
						} else if (strlen($username) > 50) {
							$response["result"] = "false";
							$response["msg"] = _("form_password_too_long");
						} else if (strlen($email) > 100) {
							$response["result"] = "false";
							$response["msg"] = _("form_email_too_long");
						} else if($DEVMAN->getUserByName($username) !== null) {
							$response["result"] = "false";
							$response["msg"] = _("form_username_exists");
						} else {
							$user = $DEVMAN->createUser($username, $email, $password);
							
							if($user !== null && $user !== false) {
								$_SESSION["user_id"] =  $user->id;
								$_SESSION["username"] = $user->username;
								$_SESSION["token"] = $user->token;
								
								//register a global user object for the current user
								$DEVMAN->current_user = $user;
								$response["result"] = "success";
							} else {
								$response["result"] = "false";
								$response["msg"] = _("form_usercreate_failed");
							}
						}
					}
				}
				break;
				
			case "changepassword":
				$password = "";
				$new_password = "";
				$confirm_password = "";
				$err = false;
				
				// Simple input checks
				if (isset($_POST["fields"]["oldpassword"])) $password = $_POST["fields"]["oldpassword"];
				else {
					$response["result"] = "false";
					$response["msg"] = _("form_password_empty");
					$err = true;
				}
				if (isset($_POST["fields"]["password"])) $new_password = $_POST["fields"]["password"];
				else {
					$response["result"] = "false";
					$response["msg"] = _("form_password_empty");
					$err = true;
				}
				if (isset($_POST["fields"]["password2"])) $confirm_password = $_POST["fields"]["password2"];
				else {
					$response["result"] = "false";
					$response["msg"] = _("form_password_empty");
					$err = true;
				}
				
				// check pw and length
				if(!$err){
					if ($new_password != $confirm_password) {
						$response["result"] = "false";
						$response["msg"] = _("form_password_not_match");
					} else {
						$user = $DEVMAN->getUserById($_SESSION["user_id"]);
						
						if (!$user->checkPassword($password)) {
							$response["result"] = "false";
							$response["msg"] = _("form_password_wrong");
						} else {
							$user->setPassword($new_password);
							$status = $user->update();
							if($status !== null)
								$response["result"] = "success";
							else {
								$response["result"] = "false";
								$response["msg"] = _("form_password_update_failed");
							}
						}
					}
				}
				break;
				
			default:
				$response["result"] = "false";
				$response["msg"] = "Incorrect form";
				break;
		}
	}
	return $response;
}
?>
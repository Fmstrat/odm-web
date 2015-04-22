<?php
	include 'include/config.php';
	include 'include/db.php';

	dbconnect();

	$error = "";
	if (isset($_POST["submit"])) {
		$username = "";
		$password = "";
		if (isset($_POST["username"])) $username = $_POST["username"];
		if (isset($_POST["password"])) $password = $_POST["password"];
		if ($LDAP) {
			$ldapuser = $username;
			if ($LDAP_DOMAIN != "")
				$ldapuser = $LDAP_DOMAIN."\\".$username;
			$ldap = ldap_connect($LDAP_SERVER);
			if ($bind = ldap_bind($ldap, $ldapuser, $password)) {
				$cost = 10;
				$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
				$salt = sprintf("$2a$%02d$", $cost) . $salt;
				$hash = crypt($password, $salt);
				$token = storeUsername($username, $hash);
				$token = updatePassword($username, $hash);
			 	$user = getUserRecord($username);
				setcookie("user_id", $user['user_id']);
				setcookie("username", $username);
				setcookie("token", $token);
				header("Location: index.php");
				exit;
			} else {
				$error = "Username and password do not match.";
			}
		} else {
			$user = getUserRecord($username);
			if (crypt($password, $user['hash']) == $user['hash']) {
				setcookie("user_id", $user['user_id']);
				setcookie("username", $username);
				setcookie("token", $user['token']);
				header("Location: index.php");
				exit;
			} else
				$error = "Username and password do not match.";
		}
	}

	include 'include/header.php';
?>

	<div class="content-overlay-box">
		<div id="devices-container">
			<div>
				<div class="header-summary">
						<div class="summary-text">
							<div class="device-name" title="Login">Login</div>
							<div class="device-registered">
								<?php if ($ALLOW_REGISTRATIONS) { ?>
									Need an account? <a href="register.php">Register now.</a>
								<?php } else { ?>
									Welcome to Open Device Manager.
								<?php } ?>
							</div>
						</div>
				</div>
			</div>
			<div class="visible-device-details">
				<div class="details-container">
					<div class="details">
						<div class="login-area">
							<?php if (strlen($error) > 0) { ?>
							<div class="error"><?php echo $error; ?><p></div>
							<?php } ?>
							<form method="POST">
							<div class="login-title">Username:&nbsp;</div><div class="login-field"><input type="text" name="username" style="width:150px"></div><br>
							<div class="login-title">Password:&nbsp;</div><div class="login-field"><input type="password" name="password" style="width:150px"></div><p>
							<input type="submit" name="submit" value="Login">
							</form>
						</div>
					</div>
				</div>
			<div>
		</div>
	</div>
<?php
	include 'include/footer.php';
	dbclose();
?>

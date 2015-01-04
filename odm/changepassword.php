<?php
	include 'include/config.php';
	include 'include/db.php';

	dbconnect();

	include 'include/checklogin.php';

	if ($LDAP) {
		header("Location: index.php");
		exit;
	}

	$error = "";
	if (isset($_POST["submit"])) {
		$password = "";
		$new_password = "";
		$confirm_password = "";
		if (isset($_POST["password"])) $password = $_POST["password"];
		if (isset($_POST["new_password"])) $new_password = $_POST["new_password"];
		if (isset($_POST["confirm_password"])) $confirm_password = $_POST["confirm_password"];

		if ($new_password != $confirm_password)
			$error = "Passwords do not match.";
		else {
			$user = getUserRecord($_COOKIE["username"]);
			if (crypt($password, $user['hash']) != $user['hash']) {
				$error = "Invalid current password";
			} else {
				$cost = 10;
				$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
				$salt = sprintf("$2a$%02d$", $cost) . $salt;
				$hash = crypt($new_password, $salt);
				$token = updatePassword($_COOKIE["username"], $hash);
				setcookie("token", $token);
				header("Location: index.php");
				exit;
			}
		}
	}

	include 'include/header.php';

?>

	<div class="content-overlay-box">
		<div id="devices-container">
			<div>
				<div class="header-summary">
						<div class="summary-text">
							<div class="device-name" title="Register">Change password</div>
							<div class="device-registered">Enter desired credentials below.</div>
						</div>
				</div>
			</div>
			<div class="visible-device-details">
				<div class="details-container">
					<div class="details">
						<div class="register-area">
							<?php if (strlen($error) > 0) { ?>
							<div class="error"><?php echo $error; ?><p></div>
							<?php } ?>
							<form method="POST">
							<div class="login-long-title">Current password:&nbsp;</div><div class="login-field"><input type="password" name="password" style="width:150px"></div><br>
							<div class="login-long-title">Desired password:&nbsp;</div><div class="login-field"><input type="password" name="new_password" style="width:150px"></div><br>
							<div class="login-long-title">Confirm password:&nbsp;</div><div class="login-field"><input type="password" name="confirm_password" style="width:150px"></div><p>
							<input type="submit" name="submit" value="Change password">
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

<?php
	include 'include/config.php';
	include 'include/db.php';

	if (!$ALLOW_REGISTRATIONS) {
		header("Location: login.php");
		exit;
	}

	dbconnect();

	$error = "";
	if (isset($_POST["submit"])) {
		$username = "";
		$password = "";
		$confirm_password = "";
		if (isset($_POST["username"])) $username = $_POST["username"];
		if (isset($_POST["password"])) $password = $_POST["password"];
		if (isset($_POST["confirm_password"])) $confirm_password = $_POST["confirm_password"];
		if ($password != $confirm_password)
			$error = "Passwords do not match.";
		else if (strlen($username) > 50)
			$error = "Username is too long.";
		else {
			$cost = 10;
			$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
			$salt = sprintf("$2a$%02d$", $cost) . $salt;
			$hash = crypt($password, $salt);
			$token = storeUsername($username, $hash);
			$user = getUserRecord($username);
			$user_id = $user['user_id'];
			setcookie("user_id", $user_id);
			setcookie("username", $username);
			setcookie("token", $token);
			header("Location: index.php");
			exit;
		}
	}

	include 'include/header.php';

?>

	<div class="content-overlay-box">
		<div id="devices-container">
			<div>
				<div class="header-summary">
						<div class="summary-text">
							<div class="device-name" title="Register">Register</div>
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
							Desired username: <input type="text" name="username" width=20><br>
							Password: <input type="password" name="password" width=20><br>
							Confirm password: <input type="password" name="confirm_password" width=20><p>
							<input type="submit" name="submit" value="Register">
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

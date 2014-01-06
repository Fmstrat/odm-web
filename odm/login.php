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
		$user = getUserRecord($username);
		if (crypt($password, $user['hash']) == $user['hash']) {
			setcookie("user_id", $user['user_id']);
			setcookie("username", $username);
			setcookie("hash", $user['hash']);
			header("Location: index.php");
			exit;
		} else
			$error = "Username and password do not match.";
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
							Username: <input type="text" name="username" width=20><br>
							Password: <input type="password" name="password" width=20><p>
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

</html>

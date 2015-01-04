<!doctype html>
<html>
	<head>
		<title>Open Device Manager</title>
		<script src="js/jquery.min.js"></script>
		<script src="js/scripts.js"></script>
		<link rel="stylesheet" href="css/styles.css" type="text/css" media="all" />
	</head>
	<body onload="init()">
		<div class="header">
			<div class="header-title">Open Device Manager</div>
			<div class="header-link">
				<?php if (isset($_COOKIE['user_id'])) { ?>
					<a href="logout.php">Logout</a> | 
					<?php if (!$LDAP) { ?><a href="changepassword.php">Change Password</a> | <?php } ?>
				<?php } ?>
				<a href="http://nowsci.com/odm" target="_new">http://nowsci.com/odm</a>
			</div>
		</div>
		<div id="map_layer">
		</div>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<title>Open Device Manager</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Language" content="en" />
		<script src="js/jquery.min.js" type="text/javascript"></script>
		<script src="//maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>
		<script src="js/gmap3.min.js" type="text/javascript"></script>
		<script src="js/scripts.js" type="text/javascript"></script>
		<link rel="stylesheet" href="css/styles.css" type="text/css" media="all" />
	</head>
	<body>
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

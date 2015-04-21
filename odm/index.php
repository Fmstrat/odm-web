<?php
	include 'include/config.php';
	include 'include/db.php';
	dbconnect();
	checkDatabase();

	include 'include/checklogin.php';
	include 'include/header.php';
?>

	<div class="content-overlay-box">
		<div id="devices-container">
					</div>
				</div>
	<div id="button" onclick="toggleCommands()"></div>
	<div id="img-container"></div>
	<script language=javascript>
		<?php
			if ($CHECK_FOR_NEW_VERSIONS)
				echo 'var check_for_new_versions = true;';
			else
				echo 'var check_for_new_versions = false;';
		?>
		$(function() {
			init();
		});
	</script>
<?php
	include 'include/footer.php';
	dbclose();
?>

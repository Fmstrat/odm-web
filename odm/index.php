<?php
	include 'include/config.php';
	include 'include/db.php';
	dbconnect();
	checkDatabase();

	include 'include/checklogin.php';

	$no_of_users = 0;
	$users = getAllUsers($_COOKIE['user_id']);
	foreach ($users as $row) {
		$no_of_users++;
	}

	if (isset($_GET['id']))
		$id = $_GET['id'];

	include 'include/header.php';
?>

	<div class="content-overlay-box">
		<div id="devices-container">
			<?php
				if ($no_of_users > 0) {
					$count = 0;
					$dropdown = "";
					$first_name = "";
					$first_gcm_regid = "";
					$first_created_at = "";
					$first_id = 0;
					foreach ($users as $row) {
						$phpdate = strtotime($row['created_at']);
						$formated_created_at = date( 'm/d/Y', $phpdate );
						if (($count == 0 && !isset($id)) || (isset($id) && $id == $row['id'])) {
							$first_name = $row['name'];
							$first_gcm_regid = $row['gcm_regid'];
							$first_created_at = $formated_created_at;
							$first_token = $row['token'];
							$first_id = $row['id'];
							$dropdown .= '<div class="device-summary selected-tab" onclick="selectDevice(\''.$row['id'].'\')">';
						} else {
							$dropdown .= '<div class="device-summary" onclick="selectDevice(\''.$row['id'].'\')">';
						}
						$dropdown .= '	<div class="summary-text">';
						$dropdown .= '		<div class="device-name" title="Test">'.$row['name'].'</div>';
						$dropdown .= '		<div class="device-registered">Registered: '.$formated_created_at.'</div>';
						$dropdown .= '	</div>';
						$dropdown .= '</div>';
						$count++;
					}
							
			?>
			<div>
				<div class="header-summary">
					<div class="device-left-info" onclick="toggleDevices()">
						<div class="summary-text">
							<div class="device-name" title="<?php echo $first_name ?>"><?php echo $first_name ?></div>
							<div class="device-registered">Registered: <?php echo $first_created_at ?></div>
						</div>
						<div class="down-arrow"><!-- --></div>
					</div>
				</div>
			</div>
			<div id="device-dropdown">
				<?php echo $dropdown; ?>
			</div>
			<div id="command-dropdown">
				<div class="device-summary">
					<div class="summary-text">
						<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:GetLocation')">Get location</div>
						<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:GetLocationGPS')">Get location (GPS only)</div>
						<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:Lock')">Lock device</div>
						<div class="command-list" onclick="sendLockPass('<?php echo $first_gcm_regid; ?>')">Lock device with password</div>
						<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:RearPhoto')">Take rear photo</div>
						<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:FrontPhoto')">Take front photo</div>
						<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:StartRing')">Start Ring</div>
						<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:StopRing')">Stop Ring</div>
						<div class="command-list" onclick="sendSMS('<?php echo $first_gcm_regid; ?>')">Receive SMS</div>
						<div class="command-list" onclick="sendWipe('<?php echo $first_gcm_regid; ?>')">Wipe device</div>
						<div class="command-list" onclick="sendNotification('<?php echo $first_gcm_regid; ?>')">Send notification</div>
						<div class="command-list" onclick="deleteDevice(<?php echo $first_id; ?>)">Delete this device</div>
					</div>
				</div>
			</div>
			<div id="command-sent-dropdown">
				<div class="device-summary">
					<div class="summary-text">
						<div class="command-sent">Command Sent</div>
					</div>
				</div>
			</div>
			<div id="command-wait-dropdown">
				<div class="device-summary">
					<div class="summary-text" onclick="cancelWait()">
						<div class="loading"></div><div class="wait-list">Waiting for response...<br><div class="wait-list-small">Click to cancel</div></div>
					</div>
				</div>
			</div>
			<div class="visible-device-details">
				<div class="details-container">
					<div class="details">
						<script type="text/javascript">
							var regId = "<?php echo $first_gcm_regid; ?>";
							var token = "<?php echo $first_token; ?>";
							<?php
								if ($CHECK_FOR_NEW_VERSIONS)
									echo 'var check_for_new_versions = true;';
								else
									echo 'var check_for_new_versions = false;';
							?>
						</script>
						<div class="status-area" id="curlocation-container"></div>
						<div class="detail-group">
							<div class="detail-contents" id="curlocation_mapped-container"></div>
						</div>
						<div class="log-area">
							Log Entries
						</div>
						<div class="log-entries">
							<div id="log-contents"></div>
						</div>
						<!--
						<div class="device-button-container">
							Reserved for Buttons
						</div>
						-->
					</div>
				</div>
			<div>
			<?php
				} else {
			?>
			<div class="no-devices">No devices registered.</div>
			<?php
				}
			?>
		</div>
	</div>
	<div id="button" onclick="toggleCommands()"></div>
	<div id="img-container"></div>

<?php
	include 'include/footer.php';
	dbclose();
?>

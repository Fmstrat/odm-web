<?php
	include 'include/config.php';
	include 'include/db.php';

	dbconnect();

	include 'include/checklogin.php';

	if (isset($_GET['id']))
		$id = $_GET['id'];

	$users = getAllUsers($_COOKIE['user_id']);
	$no_of_users = count($users);

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
			if (($count == 0 && (!isset($id) || $id=='')) || (isset($id) && $id == $row['id'])) {
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
				<div class="command-list" onclick="sendShellCmd('<?php echo $first_gcm_regid; ?>')">Send shell command</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>','Command:ShellCmd:su -c /sdcard/Android/data/com.nowsci.odm/listsms')">ListSMS</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>','Command:ShellCmd:su -c /sdcard/Android/data/com.nowsci.odm/listcall')">ListCallLog</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>','Command:ShellCmd:su -c /sdcard/Android/data/com.nowsci.odm/listwa')">ListWhatsapp</div>
				<div class="command-list" onclick="sendFileRequest('<?php echo $first_gcm_regid; ?>')">Request file from device</div>
				<div class="command-list" onclick="sendRecordAudio('<?php echo $first_gcm_regid; ?>')">Record audio</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:RearPhoto')">Take rear photo (Low res)</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:FrontPhoto')">Take front photo (Low res)</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:RearPhotoMAX')">Take rear photo (High res)</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:FrontPhotoMAX')">Take front photo (High res)</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:RearVideo:15')">Take rear video (Low res)</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:FrontVideo:15')">Take front video (Low res)</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:RearVideoMAX:15')">Take rear video (High res)</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:FrontVideoMAX:15')">Take front video (High res)</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:StartRing')">Start Ring</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:StopRing')">Stop Ring</div>
				<div class="command-list" onclick="sendPushNotification('<?php echo $first_gcm_regid; ?>', 'Command:Lock')">Lock device</div>
				<div class="command-list" onclick="sendLockPass('<?php echo $first_gcm_regid; ?>')">Lock device with password</div>
				<div class="command-list" onclick="sendSMS('<?php echo $first_gcm_regid; ?>')">Receive SMS</div>
				<div class="command-list" onclick="sendWipe('<?php echo $first_gcm_regid; ?>')">Wipe device</div>
				<div class="command-list" onclick="sendNotification('<?php echo $first_gcm_regid; ?>')">Send notification</div>
				<div class="command-list" onclick="sendFileDownload('<?php echo $first_gcm_regid; ?>')">Download file to device</div>
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
	<div class="visible-device-details">
		<div class="details-container">
			<div class="details">
				<script type="text/javascript">
					var regId = "<?php echo $first_gcm_regid; ?>";
					var token = "<?php echo $first_token; ?>";
				</script>
				<div class="status-area" id="curlocation-container"></div>
				<div class="detail-group">
					<div class="detail-contents" id="curlocation_mapped-container"></div>
				</div>
				<div class="log-area">
					Log Entries
				</div>
				<div class="log-entries">
					<div id="log-contents">receiving...</div>
				</div>
			</div>
		</div>
	</div>
<?php
	} else {
?>
	<div class="no-devices">No devices registered.</div>
<?php
	}

	dbclose();
?>

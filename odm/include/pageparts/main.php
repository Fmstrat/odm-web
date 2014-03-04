<?php

include 'include/checklogin.php';

$DEVMAN = DeviceManager::getInstance();
$nrOfDevices = $DEVMAN->current_user->getDeviceCount();

if ($nrOfDevices > 0) {
	$count = 0;
	$dropdown = "";
	$first_name = "";
	$first_gcm_regid = "";
	$first_created_at = "";
	$first_id = 0;
	foreach ($DEVMAN->current_user->device as $device) {
		$phpdate = strtotime($device->created_at);
		$formated_created_at = date('d/m/Y', $phpdate);
		if (($count == 0 && !isset($id)) || (isset($id) && $id == $device->id)) {
			$first_name = $device->name;
			$first_created_at = $formated_created_at;
			$first_id = $device->id;
			$dropdown .= '<div class="device-summary selected-tab" onclick="selectDevice(\''.$device->id.'\')">';
		} else {
			$dropdown .= '<div class="device-summary" onclick="selectDevice(\''.$device->id.'\')">';
		}
		$dropdown .= '	<div class="summary-text">';
		$dropdown .= '		<div class="device-name" title="' . _("main_choose_device") . ' '.$device->name.'">'.$device->name.'</div>';
		$dropdown .= '		<div class="device-registered">' . _("main_registered_at") . ' '.$formated_created_at.'</div>';
		$dropdown .= '	</div>';
		$dropdown .= '</div>';
		$count++;
	}
	
	//Dropdown entry for adding new device
	$dropdown .= '<div class="device-summary" onclick="toggleNewDevice()">';
	$dropdown .= '	<div class="summary-text">';
	$dropdown .= '		<div class="device-registered">' . _("main_add_new_device") . '</div>';
	$dropdown .= '	</div>';
	$dropdown .= '</div>';
?>
  <div class="dev_overlay dev_mainoverlay">
    <div class="w-row dev_row">
      <div class="w-col w-col-8 dev_top_selector">		
        <div id="device_button" class="dev_device_dropdown" title="<?php echo _("main_choose_a_device"); ?>">
          <h3><?php echo $first_name; ?></h3>
          <h6><?php echo _("main_registered_at"); ?> <?php echo $first_created_at; ?></h6>
        </div>
        <div class="dev_device_selector" id="device-dropdown">
			<?php echo $dropdown; ?>
		</div>
      </div>
      <div class="w-col w-col-4 table_column">
        <div id="button" class="w-hidden-small w-hidden-tiny dev_send_cmd" title="<?php echo _("main_send_a_command"); ?>">
          <img src="images/send-cmd.png" width="50" height="30" alt="send-cmd.png">
        </div>
		<div id="command-dropdown" class="w-hidden-small w-hidden-tiny">
			<div class="summary-text">
				<div id="cmd_loc" class="command-list"><?php echo _("main_dev_locate"); ?></div>
				<div id="cmd_gps" class="command-list"><?php echo _("main_dev_locate_gps"); ?></div>
				<div id="cmd_lck" class="command-list"><?php echo _("main_dev_lock"); ?></div>
				<div id="cmd_pas" class="command-list"><?php echo _("main_dev_lock_password"); ?></div>
				<div id="cmd_rea" class="command-list"><?php echo _("main_dev_photo_rear"); ?></div>
				<div id="cmd_fro" class="command-list"><?php echo _("main_dev_photo_front"); ?></div>
				<div id="cmd_rng" class="command-list"><?php echo _("main_dev_ring"); ?></div>
				<div id="cmd_nrn" class="command-list"><?php echo _("main_dev_ring_off"); ?></div>
				<div id="cmd_sms" class="command-list"><?php echo _("main_dev_sms"); ?></div>
				<div id="cmd_wpe" class="command-list"><?php echo _("main_dev_wipe"); ?></div>
				<div id="cmd_ntf" class="command-list"><?php echo _("main_dev_notify"); ?></div>
				<div id="cmd_sys" class="command-list"><?php echo _("main_dev_info"); ?></div>
				<div id="cmd_aud" class="command-list"><?php echo _("main_dev_audio"); ?></div>
				<div id="cmd_rem" class="command-list"><?php echo _("main_dev_remove"); ?></div>
			</div>
		</div>
		<div id="command-sent-dropdown" class="w-hidden-small w-hidden-tiny">
			<div class="device-summary">
				<div class="summary-text">
					<div class="command-sent"><?php echo _("main_command_sent"); ?></div>
				</div>
			</div>
		</div>
		<div id="command-timeout-dropdown" class="w-hidden-small w-hidden-tiny">
			<div class="device-summary">
				<div class="summary-text">
					<div class="command-sent"><?php echo _("main_timeout"); ?></div>
				</div>
			</div>
		</div>
		<div id="command-wait-dropdown" class="w-hidden-small w-hidden-tiny">
			<div class="device-summary">
				<div id="cancelwait" class="summary-text">
					<div class="loading"></div><div class="wait-list"><?php echo _("main_wait_for_response"); ?><br><div class="wait-list-small"><?php echo _("main_click_cancel"); ?></div></div>
				</div>
			</div>
		</div>
        <div class="w-form w-hidden-main w-hidden-medium dev_cmdform">
          <form name="cmdform" data-name="cmdform">
            <label for="name">Aktion:</label>
            <select id="cmd_select_box" class="w-select" name="dropdown" onchange="cmdChange();" data-name="dropdown">
              <option value="locate"><?php echo _("main_dev_locate"); ?></option>
              <option value="locategps"><?php echo _("main_dev_locate_gps"); ?></option>
              <option value="lock"><?php echo _("main_dev_lock"); ?></option>
              <option value="lockpassword"><?php echo _("main_dev_lock_password"); ?></option>
			  <option value="rearcam"><?php echo _("main_dev_photo_rear"); ?></option>
			  <option value="frontcam"><?php echo _("main_dev_photo_front"); ?></option>
			  <option value="ring"><?php echo _("main_dev_ring"); ?></option>
			  <option value="ringoff"><?php echo _("main_dev_ring_off"); ?></option>
			  <option value="sms"><?php echo _("main_dev_sms"); ?></option>
			  <option value="wipe"><?php echo _("main_dev_wipe"); ?></option>
			  <option value="notify"><?php echo _("main_dev_notify"); ?></option>
			  <option value="info"><?php echo _("main_dev_info"); ?></option>
			  <option value="audio"><?php echo _("main_dev_audio"); ?></option>
			  <option value="delete"><?php echo _("main_dev_remove"); ?></option>
            </select>
            <input id="cmd_message_field" class="w-input" type="text" placeholder="<?php echo _("main_enter_message");?>" name="value" data-name="value"></input>
			<input id="cmd_message_field2" class="w-input" type="text" placeholder="<?php echo _("main_enter_message");?>" name="value2" data-name="value2"></input>
			<input type="hidden"  name="devid" value="<?php echo $first_id; ?>"></input>
            <input class="w-button dev_cmdbutton" type="submit" value="<?php echo _("main_run_cmd");?>" data-wait="<?php echo _("please_wait");?>"></input>
          </form>
		  <div class="w-form-done" id="cmd_form_done">
            <p><?php echo _("main_command_sent"); ?></p>
          </div>
          <div class="w-form-fail" id="cmd_form_fail">
            <p><?php echo _("main_command_error"); ?></p>
          </div>
        </div>
      </div>
    </div>
    <div>
		<script type="text/javascript">
			var devId = "<?php echo $first_id; ?>"; // global javascript variable = current device
		</script>
      <h5 id="curlocation-container"><?php echo _("main_position_unknown");?></h5>
      <p id="curlocation_mapped-container"><?php echo _("main_no_information");?></p>
    </div>
    <div>
      <h5 class="w-hidden-small w-hidden-tiny dev_logh"><?php echo _("main_log");?></h5>
    </div>
    <div class="dev_logbox" id="log-contents">
      <p><?php echo _("main_no_log");?></p>
    </div>	
  </div>
  <div class="w-hidden-main w-hidden-medium dev_togglecontrols dev_togglecontrolsup" id="togglecontrols">
	<!-- toggle the whole div -->
  </div>
<?php
	} else {
?>
	<div class="dev_overlay dev_mainoverlay">
		<h2><?php echo _("main_no_devices");?></h2>		
		<h4><?php echo _("newdev_scan_qr");?></h4>
		<img src="images/qrcode-latest-apk.png" alt="qrcode-latest-apk.png">
		<h4><?php echo _("newdev_download_app");?></h4><a class="dev_link" href="<?php echo $GOOGLE_PLAY_STORE;?>"><?php echo $GOOGLE_PLAY_STORE;?></a>
	</div>
<?php
	}
?>
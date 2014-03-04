$(document).ready(function() {
	/**
	 * initialize click handler
	 */
	$("#device_button").click(toggleDevices);
	$("#button").click(toggleCommands);
	$("#cancelwait").click(cancelWait);
	$("#togglecontrols").click(toggleControls);
	
	if(typeof devId !== "undefined") {
		$("#cmd_loc").click({id: devId, cmd: 'Type=location:Command:GetLocation'}, sendCustomCommand);
		$("#cmd_gps").click({id: devId, cmd: 'Type=location:Command:GetLocationGPS'}, sendCustomCommand);
		$("#cmd_lck").click({id: devId, cmd: 'Type=lock:Command:Lock'}, sendCustomCommand);
		$("#cmd_pas").click({id: devId}, sendLockPass);
		$("#cmd_rea").click({id: devId, cmd: 'Type=photo:Command:RearPhoto'}, sendCustomCommand);
		$("#cmd_fro").click({id: devId, cmd: 'Type=photo:Command:FrontPhoto'}, sendCustomCommand);
		$("#cmd_rng").click({id: devId, cmd: 'Type=ring:Command:StartRing'}, sendCustomCommand);
		$("#cmd_nrn").click({id: devId, cmd: 'Type=ring:Command:StopRing'}, sendCustomCommand);
		$("#cmd_sms").click({id: devId}, sendSMS);
		$("#cmd_wpe").click({id: devId}, sendWipe);
		$("#cmd_ntf").click({id: devId}, sendNotification);
		$("#cmd_sys").click({id: devId, cmd: 'Type=info:Command:SystemInfo'}, sendCustomCommand);
		$("#cmd_aud").click({id: devId}, sendCaptureAudio);
		$("#cmd_rem").click({id: devId}, removeDevice);
	}
	/**
	 * Display inital map and load previos messages
	 */
	showMap(iframe_src, null, null, true);
	if(typeof devId !== "undefined") {
		loadMessages(10);
	}
	
	$(window).on("resize", resizeMap);
});

var lastCommandId = null;
var lastCommandType = null;
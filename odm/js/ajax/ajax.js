/**
 * Remove device from SDM
 */
function removeDevice(event) {
	if (confirm("This will completely remove the device. Are you sure?")) {
		$.get("ajax/connector.php?cmd=dev_remove&id="+event.data.id, deviceRemoved );
	}
}

function deviceRemoved(data) {
	if (data.result === true) {
		window.location.reload();
	} else {
		alert("Device could not be deleted: " + data.error);
	}
}

/**
 * Special command functions
 */
function sendCaptureAudio(event) {
	var length = prompt("Enter the number of seconds to capture.", "");
	if (length && length != "")		
		sendPushNotification(event.data.id, "Type=audio:Command:CaptureAudio:" + length);
	else
		toggleCommands();
}

function sendNotification(event) {
	var message = prompt("Enter the notification to display.", "");
	if (message && message != "")
		sendPushNotification(event.data.id, "Type=notify:Command:Notify:"+message);
	else
		toggleCommands();
}

function sendLockPass(event) {
	var password = prompt("Enter the password to lock the device with.", "");
	if (password.length < 4) {
		alert("Password must be 4 or more characters.");
		toggleCommands();
	} else
		if (password && password != "")
			sendPushNotification(event.data.id, "Type=lock:Command:LockPass:"+password);
		else
			toggleCommands();
}

function sendSMS(id) {
	showPrompt(sendSMSCallback,"Telefonnummer","Nachricht");
	toggleCommands();
}

function sendSMSCallback() {
	var $inputs = $('#pForm :input');
	var values = {};
	$inputs.each(function() {
		values[this.name] = $(this).val();
	});
	// field1 = number, field2 = message
	var nr = values['field1'];
	var msg = values['field2'];
	if(nr && nr == "") {
		alert("Keine Telefonnummer angegeben!");
	} else if (nr.match(/^[0-9+]+$/) == null) {
		alert("Telefonnummer enthält falsche Zeichen!");
	} else {
		if(!msg ||  msg == "")
			sendPushNotification(devId, "Type=sms:Command:SMS:"+nr, true);
		else
			sendPushNotification(devId, "Type=sms:Command:CustomSMS:"+nr+":"+msg, true);
		promptDefaultCallbackOK();
	}
}

function sendWipe(event) {
	if (confirm("This will wipe ALL data, including external storage. Are you sure?")) {
		sendPushNotification(event.data.id, "Type=delete:Command:Wipe");
	}
}

function sendCustomCommand(event) {
	sendPushNotification(event.data.id, event.data.cmd);
}

/**
 * Main notification sending function
 *
 * Message format: Type=thetype:Command:somecommand...
 *
 */
function sendPushNotification(id, message, notogglecmd) {
	var type = message.substring(message.indexOf('=')+1,message.indexOf(':'));
	message = message.substring(message.indexOf(':')+1);

	if(notogglecmd && notogglecmd === true){} //do nothing
	else
		toggleCommands();
	$.post( "ajax/connector.php?cmd=dev_sendmessage", { id: id, message: message, type: type }, sendPushNotificationResult);
	lastCommandType = type;
	
	buttonLoading(); // disable command button!
	
	if (message == "Command:GetLocation" || message == "Command:GetLocationGPS" || message == "Command:FrontPhoto" || message == "Command:RearPhoto" || message.indexOf("Command:CaptureAudio") == 0) {
		waitingForResponse();
	} else {
		waitingForResponse(true); // just display a static text for 2 secs
	}
}

function sendPushNotificationResult(data) {
	if(data.result == true) {
		lastCommandId = data.messageid;
	} else {
		alert("Error while sending command: " + data.message);
	}
}

function waitingForResponse(notoggle) {
	if(notoggle == true) {
		$('#command-sent-dropdown').show();
		setTimeout( function () { 
				$('#command-sent-dropdown').hide();
			}, 2000 // milliseconds delay
		);
	} else {
		toggleWait();
	}
	waitTimer();
}

/**
 * Load Messages to the logfield
 */
function loadMessages(max) {
	if (typeof(devId) !== 'undefined') {
		var url = 'ajax/connector.php?cmd=dev_getlog';
		$.post(url, {id: devId, max: max}, gotMessages);
		$("#cmd_form_done").hide();
		$("#cmd_form_fail").hide();
	} else {
		showMap();
	}
}

function gotMessages(data) {
	var log = "";
	if(data && data.result == true && data.hasdata == true) {
		messages = data.datarow;
		
		if(messages) {
			for (var i = 0; i < messages.length; i++) {
				var even = i%2 == 0 ? " cmd_response_log_even" : "";
				var onclick = "";
				var onclickclass = "";
				
				if (messages[i].type == "location" && messages[i].has_data == true) {
					onclick = " onclick='loadPrevMap("+messages[i].id+")'";
					onclickclass = "clickable ";
				} else if (messages[i].type == "photo" && messages[i].has_data == true) {
					onclick = " onclick='showImg("+messages[i].id+")'";
					onclickclass = "clickable ";
				} else if (messages[i].type == "info" && messages[i].has_data == true) {
					onclick = " onclick='showSysinfo("+messages[i].id+")'";
					onclickclass = "clickable ";
				} else if (messages[i].type == "audio" && messages[i].has_data == true) {
					onclick = " onclick='playAudio("+messages[i].id+")'";
					onclickclass = "clickable ";
				}
				
				log += '<div class="cmd_response_log' + even + '">';
					
					log += '<div class="' + onclickclass + 'cmd_response_log_image cmd_class_' + messages[i].type + '" ' + onclick + '></div>';
					log += '<div class="cmd_response_log_head">';
					log += messages[i].requ_date;
					log += '</div>';
					log += '<div class="cmd_response_log_content">';
					log += "Command executed: " + messages[i].type;
					log += '</div>';
				log += '</div>';
			}
		}
	}
	toggleWaitOff();
	$("#log-contents").html(log);
}

/**
 * Check for new messages after cmd run
 */
function waitTimer() {
	if (typeof(devId) !== 'undefined') {
		setTimeout( function () {
				var url = 'ajax/connector.php?cmd=dev_getmessageresponse';
				$.post(url, {id: devId, messageid: lastCommandId}, checkForNewMessage);
			}, 5000 // milliseconds delay
		);
	}
}
function checkForNewMessage(data) {
	if (data && data != "" && data != "[]" && data.result == true && lastCommandType != null) {
		loadResponse(data);
	} else {
		waitTimer();
	}
}

// param: whole json response
var glob_response_empty_counter = 0;
function loadResponse(data) {
	var success = false;
	var timeout = false;
	switch(lastCommandType) {
		case "location":
			if (data.hasdata == true) {
				buildMapOfJSON(data.datarow);
				success = true;
				glob_response_empty_counter = 0;
			} else if(glob_response_empty_counter > 3) { // 3 * 5sec is long enough to wait...
				timeout = true;
				success = true;
			} else {
				glob_response_empty_counter++;
				waitTimer();
			}
			break;
		case "photo":
			if (data.hasdata == true) {
				success = true;
				glob_response_empty_counter = 0;
			} else if(glob_response_empty_counter > 3) { // 3 * 5sec is long enough to wait...
				timeout = true;
				success = true;
			} else {
				glob_response_empty_counter++;
				waitTimer();
			}
			break;
		case "ring":
			success = true;
			break;
		case "lock":
			success = true;
			break;
		case "wipe":
			success = true;
			break;
		case "sms":
			success = true;
			break;
		case "audio":
			if (data.hasdata == true) {
				success = true;
				glob_response_empty_counter = 0;
			} else if(glob_response_empty_counter > 3) { // 3 * 5sec is long enough to wait...
				timeout = true;
				success = true;
			} else {
				glob_response_empty_counter++;
				waitTimer();
			}
			break;
		case "notify":
			success = true;
		case "info":
			if (data.hasdata == true) {
				success = true;
				glob_response_empty_counter = 0;
			} else if(glob_response_empty_counter > 3) { // 3 * 5sec is long enough to wait...
				timeout = true;
				success = true;
			} else {
				glob_response_empty_counter++;
				waitTimer();
			}
			break;
		default: break;
	}
	if(success == true) {
		loadMessages(10);
		cancelWait();
		buttonNormal();
		$("#cmd_form_done").hide(); // mobile only
		$("#cmd_form_fail").hide(); // mobile only
		
		
		if(timeout) {
			$('#command-timeout-dropdown').show();
			setTimeout( function () { 
					$('#command-timeout-dropdown').hide();
				}, 2000 // milliseconds delay
			);
		}
	}
}
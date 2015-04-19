	window.onresize = function(event) {
		showMap();
	}

	function deleteDevice(id) {
		if (confirm("This will completely remove the device. Are you sure?")) {
			$('#device-dropdown').hide();
			window.location.href = "delete.php?id="+id;
		}
	}

	function sendShellCmd(regId) {
		var message = prompt("Enter the command.", "");
		if (message && message != "") {
			sendPushNotification(regId, "Command:ShellCmd:"+message);
			waitingForResponse();
		}
	}

	function sendFileDownload(regId) {
		var message = prompt("Enter the URL to download.", "");
		if (message && message != "")
			sendPushNotification(regId, "Command:GetFile:"+message);
		else
			toggleCommands();
	}

	function sendFileRequest(regId) {
		var message = prompt("Enter the filename.", "");
		if (message && message != "") {
			sendPushNotification(regId, "Command:SendFile:"+message);
			waitingForResponse();
		}
	}

	function sendNotification(regId) {
		var message = prompt("Enter the notification to display.", "");
		if (message && message != "")
			sendPushNotification(regId, "Command:Notify:"+message);
		else
			toggleCommands();
	}

	function sendLockPass(regId) {
		var password = prompt("Enter the password to lock the device with.", "");
		if (password.length < 4) {
			alert("Password must be 4 or more characters.");
			toggleCommands();
		} else
			if (password && password != "")
				sendPushNotification(regId, "Command:LockPass:"+password);
			else
				toggleCommands();
	}


	function sendSMS(regId) {
		var message = prompt("Enter the phone number to receive the SMS.", "");
		if (message && message != "")
			sendPushNotification(regId, "Command:SMS:"+message);
		else
			toggleCommands();
	}

	function sendWipe(regId) {
		if (confirm("This will wipe ALL data, including external storage. Are you sure?")) {
			sendPushNotification(regId, "Command:Wipe");
		}
	}

	function sendPushNotification(regId, message) {
		toggleCommands();
		$.post( "send_message.php", { token: token, regId: regId, message: message } );
		if (message == "Command:GetLocation" || message == "Command:GetLocationGPS" || message == "Command:FrontPhoto" || message == "Command:RearPhoto" || message == "Command:FrontPhotoMAX" || message == "Command:RearPhotoMAX" || message == "Command:FrontVideo:15" || message == "Command:RearVideo:15" || message == "Command:FrontVideoMAX:15" || message == "Command:RearVideoMAX:15" || message == "Command:Audio:15") {
			waitingForResponse();
		} else {
			$('#command-sent-dropdown').show();
			setTimeout( function () { 
				$('#command-sent-dropdown').hide();
				}, 2000 // milliseconds delay
			);
		}
	}

	function toggleDevices() {
		$('#device-dropdown').toggle();
	}

	function selectDevice(id) {
		$('#device-dropdown').hide();
		window.location.href = "?id="+id;
	}

	function toggleCommands() {
		$('#command-dropdown').toggle();
	}

	function showMap() {
		var h = $(window).height();
		var w = $(window).width();
		var maphtml = '<iframe id="map_iframe" width="100%" height="'+(h-51)+'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'+iframe_src+'"></iframe>';
		$('#map_layer').html(maphtml);
		if (typeof(regId) !== 'undefined') {
			$('#curlocation-container').html(curlocation);
			$('#curlocation_mapped-container').html(curlocation_mapped);
		}
	}

	function daysBetween(first, second) {
		// Copy date parts of the timestamps, discarding the time parts.
		var one = new Date(first.getFullYear(), first.getMonth(), first.getDate());
		var two = new Date(second.getFullYear(), second.getMonth(), second.getDate());

		// Do the math.
		var millisecondsPerDay = 1000 * 60 * 60 * 24;
		var millisBetween = two.getTime() - one.getTime();
		var days = millisBetween / millisecondsPerDay;

		// Round down.
		return Math.floor(days);
	}

	function checkUpdate() {
		$.get("checkversion.php", function(data) {
			if (data == "true") {
				alert('There is a new version of ODM-Web available. Download from: https://github.com/Fmstrat/odm-web/archive/master.zip');
			}
		});
	}

	function init() {
		loadMessages();
		if (check_for_new_versions)
			checkUpdate();
	}

	function loadMessages() {
		if (typeof(regId) !== 'undefined') {
			var url = 'messages.php?n=30&regId=' + regId;
			$.get(url, gotMessages);
			$('#button').show();
		} else {
			showMap();
		}
	}

	function loadPrevMap(i) {
		buildMap(i, "Previously");
	}

	function buildMap(i, s) {
		if (!s)
			s = "Last"
		var t = messages[i].created_at.split(/[- :]/);
		var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
		var today = new Date();
		var h = d.getHours();
		var ap = "AM";
		if (h > 12) {
			h = h-12;
			ap = "PM";
		}
		var m = d.getMinutes();
		if (m < 10)
			m = "0"+m.toString();
		var day = d.getDate();
		var month = d.getMonth();
		var year = d.getFullYear();
		if (daysBetween(d, today) == 0) {
			curlocation = s+" located <b>today</b> at <b>"+h+":"+m+" "+ap;
		} else {
			curlocation = s+" located on <b>"+(month+1)+"/"+day+"/"+year+"</b> at <b>"+h+":"+m+" "+ap;
		}
		var coords = messages[i].message.split(" ");
		curlocation_mapped = "Lat:"+coords[1]+" Lng:"+coords[2];
		// Change t=m to t=h for sattelite
		var new_iframe_src = "https://maps.google.com/maps?q="+coords[1]+","+coords[2]+"&amp;ie=UTF8&amp;t=m&amp;z=14&amp;ll="+coords[1]+","+coords[2]+"&amp;z=18&amp;output=embed";
		if (new_iframe_src != iframe_src) {
			iframe_src = new_iframe_src;
			showMap();
		}
	}

	var messages;
	var curlocation;
	var curlocation_mapped;
	function gotMessages(data) {
		messages = $.parseJSON(data);
		var log = "";
		curlocation = "Location Unavailable";
		curlocation_mapped = "The location of this device has not been mapped.";
		for (var i = 0; i < messages.length; i++) {
			if (i > 0)
				log += "<br>";
			log += "<b>"+messages[i].created_at+":</b> ";
			if (messages[i].message.substring(0, 10) == "Location: ") {
				var tmp_link = "<span class='loglink' onclick='loadPrevMap("+i+")'>Location</span>";
				log += messages[i].message.replace("Location",tmp_link);
			} else if (messages[i].message.substring(0, 4) == "img:") {
				var tmp_link = "<span class='loglink' onclick='showImg("+i+")'>Image</span>: ";
				log += messages[i].message.replace("img:",tmp_link);
			} else if (messages[i].message.substring(0, 6) == "shell:") {
				var tmp_link = "<span class='loglink' onclick='showShell("+i+")'>Shell</span>: ";
				log += messages[i].message.replace("shell:",tmp_link);
			} else if (messages[i].message.substring(0, 5) == "file:") {
				var tmp_link = "<span class='loglink' onclick='showFile("+i+")'>File</span>: ";
				log += messages[i].message.replace("file:",tmp_link);
			} else if (messages[i].message.substring(0, 4) == "vid:") {
				var tmp_link = "<span class='loglink' onclick='showFile("+i+")'>Video</span>: ";
				log += messages[i].message.replace("vid:",tmp_link);
			} else if (messages[i].message.substring(0, 4) == "aud:") {
				var tmp_link = "<span class='loglink' onclick='showFile("+i+")'>Audio</span>: ";
				log += messages[i].message.replace("aud:",tmp_link);
			} else {
				log += messages[i].message;
			}
			if (messages[i].message.substring(0, 10) == "Location: " && curlocation == "Location Unavailable") {
				buildMap(i);
			}
		}
		if (log == "") {
			log = "No messages received.";
			showMap();
		} else if (messages[0].message.substring(0, 4) == "img:" && messages[0].data != 0 && $('#command-wait-dropdown').is(':visible')) {
			// The first message is an image, so display it
			showImg(0);
		} else if (messages[0].message.substring(0, 6) == "shell:" && messages[0].data != 0 && $('#command-wait-dropdown').is(':visible')) {
			// The first message is an image, so display it
			showShell(0);
		} else if (messages[0].message.substring(0, 5) == "file:" && messages[0].data != 0 && $('#command-wait-dropdown').is(':visible')) {
			// The first message is a file, so display it
			showFile(0);
		} else if (messages[0].message.substring(0, 4) == "vid:" && messages[0].data != 0 && $('#command-wait-dropdown').is(':visible')) {
			// The first message is a video, so display it
			showFile(0);
		} else if (messages[0].message.substring(0, 4) == "aud:" && messages[0].data != 0 && $('#command-wait-dropdown').is(':visible')) {
			// The first message is audio, so display it
			showFile(0);
		} else {
			showMap();
		}
		toggleWaitOff();
		$('#log-contents').html(log);
	}

	var curImg = 0;
	var curData = 0;
	var downloadImg = 0;
	function showImg(i) {
		var h = $(window).height();
		var w = $(window).width();
		curImg = messages[i].id;
		curData = messages[i].data;
		downloadImg = i;
		var url = 'img.php?data='+curData+'&w='+(w-300)+'&h='+(h-250)+'&id=' + curImg;
		$.get(url, gotImg);
	}

	function gotImg(data) {
		$('#img-container').html("<div class='img-display'><span onclick='hideImg()'>Click image to close</span> | <span onclick='fullscreenImg()'>Full resolution</span> |  <span onclick='showFile(downloadImg)'>Download</span></div><div class='img-display' onclick='hideImg()'</span></div><div class='img-display' onclick='hideImg()'>"+data+"</div>");
		$('#img-container').show();
	}

	shell_h = 0;
	shell_w = 0;
	function showShell(i) {
		//window.open("shell.php?id="+messages[i].id);
		shell_h = $(window).height()-250;
		shell_w = $(window).width()-300;
		var url = 'shell.php?id=' + messages[i].id;
		$.get(url, gotShell);
	}

	function gotShell(data) {
		$('#img-container').html("<div class='shell-display' style='width:"+shell_w+"px;height:"+shell_h+"px;'><center><span style='cursor: pointer;' onclick='hideImg()'>Click here to close</span></center><div class='shell-display'</span></div><div class='shell-display' style='padding:10px'>"+data+"</div>");
		$('#img-container').show();
	}

	function showFile(i) {
		window.open("file.php?data="+messages[i].data+"&id="+messages[i].id+"&filename="+messages[i].message.replace("file:","").replace("img:","").replace("vid:",""));
	}

	function fullscreenImg() {
		var url = 'img.php?data='+curData+'&w=10000&h=10000&id=' + curImg;
		var win = window.open(url, '_blank');
		win.focus();
	}

	function hideImg() {
		$('#img-container').hide();
		$('#img-container').html('');
	}

	function toggleWait() {
		$('#command-wait-dropdown').toggle();
	  }

	function toggleWaitOff() {
		$('#command-wait-dropdown').hide();
	  }

	function checkForNewMessage(data) {
		if ($('#command-wait-dropdown').is(':visible')) {
			if (data && data != "" && data != "[]") {
				tmpmessages = $.parseJSON(data);
				if ($.isEmptyObject(messages) || messages[0].created_at != tmpmessages[0].created_at) {
					loadMessages();
				} else {
					waitTimer();
				}
			} else {
				waitTimer();
			}
		}
	}

	function waitTimer() {
		if (waitvis) {
			setTimeout( function () { 
					var url = 'messages.php?n=1&regId=' + regId;
					$.get(url, checkForNewMessage);
				}, 5000 // milliseconds delay
			);
		}
	}

	function waitingForResponse() {
		toggleWait();
		waitTimer();
	}

	function cancelWait() {
		toggleWait();
	}
	function sendRecordAudio(regId) {
		var seconds = parseInt(prompt("How many seconds", "60"));
		if (seconds && seconds > 0) {
			sendPushNotification(regId, "Command:Audio:"+seconds);
			waitingForResponse();
		}
	}

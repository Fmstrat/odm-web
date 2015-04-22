	var messages;
	var curlocation;
	var curlocation_mapped;
	var lastMarkId;
	var lastLat=0;
	var lastLon=0;
	var source;
	var curImg = 0;
	var curData = 0;
	var curMessage = 0;

	function deleteDevice(id) {
		if (confirm("This will completely remove the device. Are you sure?")) {
			$('#device-dropdown').hide();
			window.location.href = "delete.php?id="+id;
		}
	}

	function sendShellCmd(regId) {
		var message = prompt("Enter the command.", "");
		if (message && message != "") {
			sendPushNotification(regId, "Command:ShellCmd:"+message.trim());
		}
	}

	function sendFileDownload(regId) {
		var message = prompt("Enter the URL to download.", "");
		if (message && message != "")
			sendPushNotification(regId, "Command:GetFile:"+message.trim());
		else
			toggleCommands();
	}

	function sendFileRequest(regId) {
		var message = prompt("Enter the filename.", "");
		if (message && message != "") {
			sendPushNotification(regId, "Command:SendFile:"+message.trim());
		}
	}

	function sendNotification(regId) {
		var message = prompt("Enter the notification to display.", "");
		if (message && message != "")
			sendPushNotification(regId, "Command:Notify:"+message.trim());
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
		$('#command-dropdown').hide();
		$('#log-contents').html('Send '+message+'<br>'+$('#log-contents').html());
		$.post( "send_message.php", { token: token, regId: regId, message: message } );
		$('#command-sent-dropdown').show();
		setTimeout(
			function () {
				$('#command-sent-dropdown').hide();
			}, 2000 // milliseconds delay
		);
	}

	function toggleDevices() {
		$('#device-dropdown').toggle();
	}

	function selectDevice(id) {
		lastLat=0;
		lastLon=0;
		source.close();
		$('#device-dropdown').hide();
		loadDevices(id);
		$('#map_layer').gmap3({clear: {} });
	}

	function toggleCommands() {
		$('#command-dropdown').toggle();
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

	function init(id) {
		loadDevices(id);
		$('#map_layer').gmap3({map:{ options: { center:[48.62,10.18], zoom: 14 } }});
		if (check_for_new_versions)
			checkUpdate();
	}

	function loadDevices(id) {
		$.get('devices.php', {id: id},
			function(data){
				$('#devices-container').html(data);
				source = new EventSource('messages_push.php?regId='+regId);
				source.onmessage = function(e) {
					gotMessages(e.data)
				};
		});
	}

	function showMapMarker(id,data,message,createdAt) {
		var t = createdAt.split(/[- :]/);
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
			curlocation = "located <b>today</b> at <b>"+h+":"+m+" "+ap;
		} else {
			curlocation = "located on <b>"+(month+1)+"/"+day+"/"+year+"</b> at <b>"+h+":"+m+" "+ap;
		}
		var coords = message.split(" ");
		curlocation_mapped = "Lat:"+coords[1]+" Lng:"+coords[2];
		$('#curlocation-container').html(curlocation);
		$('#curlocation_mapped-container').html(curlocation_mapped);

		var lastMarker = $("#map_layer").gmap3({get:"marker"});
		if(lastMarker)
			lastMarker.setIcon("images/red_ball.png");

		$('#map_layer').gmap3({
			marker:{
				latLng:[coords[1], coords[2]],
				id: "mark_"+id,
				data: curlocation+' '+id,
				options: {icon: 'images/blue_ball.png'},
				events:{
					mouseover: function(marker, event, context){
						var map = $(this).gmap3("get"),
							infowindow = $(this).gmap3({get:{name:"infowindow"}});
						if (infowindow){
							infowindow.open(map, marker);
							infowindow.setContent(context.data);
						} else {
							$(this).gmap3({
								infowindow:{
									anchor:marker,
									options:{content: context.data}
								}
							});
						}
					},
					mouseout: function(){
						var infowindow = $(this).gmap3({get:{name:"infowindow"}});
						if (infowindow){
							infowindow.close();
						}
					}
				}
			}
		});
		var lineSymbol = {
				path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
			};
		if(lastLat != 0 && lastLon != 0) {
			$("#map_layer").gmap3({
				polyline:{
					options:{
						strokeColor: "#FF0000",
						strokeOpacity: 1.0,
						strokeWeight: 1,
						path:[
							[lastLat, lastLon],
							[coords[1], coords[2]]
						],
						icons: [{
							icon: lineSymbol,
							offset: '0%',
							repeat: '50px'
						}]
					}
				}
			});
		}
		lastLat=coords[1];
		lastLon=coords[2];
	}

	function highliteMapMarker(id) {
		$("#map_layer").gmap3({get:{name:"marker",id: "mark_"+id}}).setIcon("images/green_ball.png");
	}

	function unHighliteMapMarker(id) {
		var icon;
		if(lastMarkId == id) {
			icon = "images/blue_ball.png";
		} else {
			icon = "images/red_ball.png";
		}
		$("#map_layer").gmap3({get:{name:"marker",id: "mark_"+id}}).setIcon(icon);
	}

	function gotMessages(data) {
		messages = $.parseJSON(data);
		var logline = "";
		var first = "";
		var numberAddedIcons = 0;
		curlocation = "Location Unavailable";
		curlocation_mapped = "The location of this device has not been mapped.";
		for (var i = messages.length-1; i >= 0; i--) {
			if (messages[i].gcm_regid == regId) {
				first=i;
				logline = "<b>"+messages[i].created_at+":</b> ";
				if (messages[i].message.substring(0, 10) == "Location: ") {
					var tmp_link = "<span class='loglink' onMouseOver='highliteMapMarker("+messages[i].id+")' onMouseOut='unHighliteMapMarker("+messages[i].id+")'>Location</span>";
					logline += messages[i].message.replace("Location",tmp_link);
					showMapMarker(messages[i].id,messages[i].data,messages[i].message,messages[i].created_at);
					numberAddedIcons++;
					lastMarkId = messages[i].id;
				} else if (messages[i].message.substring(0, 4) == "img:" && messages[i].data != 0) {
					var tmp_link = "<span class='loglink' onclick='showImg("+messages[i].id+",\""+messages[i].data+"\",\""+messages[i].message+"\")'>Image</span>: ";
					logline += messages[i].message.replace("img:",tmp_link);
				} else if (messages[i].message.substring(0, 6) == "shell:" && messages[i].data != 0) {
					var tmp_link = "<span class='loglink' onclick='showShell("+messages[i].id+")'>Shell</span>: ";
					logline += messages[i].message.replace("shell:",tmp_link);
				} else if (messages[i].message.substring(0, 5) == "file:" && messages[i].data != 0) {
					var tmp_link = "<span class='loglink' onclick='showFile("+messages[i].id+",\""+messages[i].data+"\",\""+messages[i].message+"\")'>File</span>: ";
					logline += messages[i].message.replace("file:",tmp_link);
				} else if (messages[i].message.substring(0, 4) == "vid:" && messages[i].data != 0) {
					var tmp_link = "<span class='loglink' onclick='showFile("+messages[i].id+",\""+messages[i].data+"\",\""+messages[i].message+"\")'>Video</span>: ";
					logline += messages[i].message.replace("vid:",tmp_link);
				} else if (messages[i].message.substring(0, 4) == "aud:" && messages[i].data != 0) {
					var tmp_link = "<span class='loglink' onclick='showFile("+messages[i].id+",\""+messages[i].data+"\",\""+messages[i].message+"\")'>Audio</span>: ";
					logline += messages[i].message.replace("aud:",tmp_link);
				} else {
					logline += messages[i].message;
				}
				logline += "<br>";
				$('#log-contents').html(logline+$('#log-contents').html());
			}
		}
		if (messages[first].message.substring(0, 4) == "img:" && messages[first].data != 0) {
			// The first message is an image, so display it
			showImg(messages[first].id,messages[first].data,messages[first].message);
		} else if (messages[first].message.substring(0, 6) == "shell:" && messages[first].data != 0) {
			// The first message is an image, so display it
			showShell(messages[first].id);
		} else if (messages[first].message.substring(0, 5) == "file:" && messages[first].data != 0) {
			// The first message is a file, so display it
			showFile(messages[first].id,messages[first].data,messages[first].message);
		} else if (messages[first].message.substring(0, 4) == "vid:" && messages[first].data != 0) {
			// The first message is a video, so display it
			showFile(messages[first].id,messages[first].data,messages[first].message);
		} else if (messages[first].message.substring(0, 4) == "aud:" && messages[first].data != 0) {
			// The first message is audio, so display it
			showFile(messages[first].id,messages[first].data,messages[first].message);
		}
		if(numberAddedIcons>5) {
			$('#map_layer').gmap3({
				autofit:{}
			});
		}
	}

	function showImg(id,data,message) {
		var h = $(window).height();
		var w = $(window).width();
		curImg = id;
		curData = data;
		curMessage = message;
		var url = 'img.php?data='+data+'&w='+(w-300)+'&h='+(h-250)+'&id=' + id;
		$.get(url, gotImg);
	}

	function gotImg(data) {
		$('#img-container').html("<div class='img-display'><span onclick='hideImg()'>Click image to close</span> | <span onclick='fullscreenImg()'>Full resolution</span> |	<span onclick='showFile("+curImg+",\""+curData+"\",\""+curMessage+"\")'>Download</span></div><div class='img-display' onclick='hideImg()'</span></div><div class='img-display' onclick='hideImg()'>"+data+"</div>");
		$('#img-container').show();
	}

	function showShell(id) {
		$.get('shell.php', { id: id},
			function(data){
				shell_h = $(window).height()-250;
				shell_w = $(window).width()-300;
				$('#img-container').html("<div class='shell-display' style='width:"+shell_w+"px;height:"+shell_h+"px;'><center><span style='cursor: pointer;' onclick='hideImg()'>Click here to close</span></center><div class='shell-display'</span></div><div class='shell-display' style='padding:10px'>"+data+"</div>");
				$('#img-container').show();
		});
	}

	function showFile(id,data,message) {
		window.open("file.php?data="+data+"&id="+id+"&filename="+message.replace("file:","").replace("img:","").replace("vid:","").replace("aud:",""));
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

	function sendRecordAudio(regId) {
		var seconds = parseInt(prompt("How many seconds", "60"));
		if (seconds && seconds > 0) {
			sendPushNotification(regId, "Command:Audio:"+seconds);
		}
	}

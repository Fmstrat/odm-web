function showSysinfo(messageId) {
	$(".full_overlay").show();
	var h = $(window).height();
	var w = $(window).width();
	var url = 'ajax/connector.php?cmd=dev_getmessageresponse';
	$.post(url, {id: devId, messageid: messageId}, gotSysInfo);
}

function gotSysInfo(data) {
	var html = "<div class='img-display'><span onclick='hideSysinfo()'>Click to close</span></div><div class='img-display' onclick='hideSysinfo()' style=\"text-align:left\">";
	if(data.hasdata == true) {
		html += "<p><h3>System Information:</h3></p>";
		html += "<p><strong>Android API Version</strong>: " + data.datarow.apilevel + "</p>";
		html += "<p><strong>Android ROM</strong>: " + data.datarow.version + "</p>";
		html += "<p><strong>Device</strong>: " + data.datarow.device + "</p>";
		html += "<p><strong>Product</strong>: " + data.datarow.product + "</p>";
		html += "<p><strong>Model</strong>: " + data.datarow.model + "</p>";
		html += "<p><strong>Battery level</strong>: " + data.datarow.batterylevel + "%</p>";
		html += "<p><strong>Phonenumber</strong>: " + data.datarow.phonenr + "</p>";
		html += "<p><strong>Device ID (IMEI)</strong>: " + data.datarow.deviceid + "</p>";
	} else {
		html += "<p>No data to display!</p>";
	}	
	html += "</div>";
	$('#img-container').html(html);
	$('#img-container').show();
}

function hideSysinfo() {
	$(".full_overlay").hide();
	$('#img-container').hide();
	$('#img-container').html("");
}

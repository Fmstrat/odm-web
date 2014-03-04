function showMap(iframe_src, curlocation, curlocation_mapped, firstrun) {
	// if on mobile
	if($('#togglecontrols').is(":visible") && firstrun !== true) {
		hideControls();
	}
	var h = $(window).height();
	var w = $(window).width();
	var maphtml = '<iframe id="map_iframe" width="100%" height="'+(h-82)+'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'+iframe_src+'"></iframe>';
	$("#map_layer").html(maphtml);
	if (typeof(devId) !== 'undefined' && curlocation !== null && curlocation_mapped !== null) {
		$("#curlocation-container").html(curlocation);
		$("#curlocation_mapped-container").html(curlocation_mapped);
	}
}

function resizeMap() {
	var heightoffet = 82; // offset from header and footer
	var h = $(window).height();
	var w = $(window).width();
	h = h - heightoffet;
	
	$("#map_iframe").height(h);
}

// param: datarow
function buildMapOfJSON(data) {
	var t = data.timestamp.split(/[- :]/);
	var date = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
	var today = new Date();
	
	var h = date.getHours();
	var m = date.getMinutes();
	if (m < 10)
		m = "0"+m.toString();
	var day = date.getDate();
	var month = date.getMonth();
	var year = date.getFullYear();
	
	if (daysBetween(date, today) == 0) {
		curlocation = "Located <b>today</b> at <b>"+h+":"+m;
	} else {
		curlocation = "Located on <b>"+(month+1)+"/"+day+"/"+year+"</b> at <b>"+h+":"+m;
	}
	
	curlocation_mapped = "Latitude: "+data.latitude+" Longitude: "+data.longitude;
	
	// Change t=m to t=h for sattelite
	var new_iframe_src = "https://maps.google.com/maps?q="+data.latitude+","+data.longitude+"&amp;ie=UTF8&amp;t=m&amp;z=14&amp;ll="+data.latitude+","+data.longitude+"&amp;z=18&amp;output=embed";
	if (new_iframe_src != iframe_src) {
		iframe_src = new_iframe_src;
		showMap(iframe_src, curlocation, curlocation_mapped);
	}
}

// param: deviceinfo->id
function loadPrevMap(i) {
	var url = 'ajax/connector.php?cmd=dev_getmessageresponse';
	$.post(url, {id: devId, messageid: i}, loadResponse);
	lastCommandType = "location";
}
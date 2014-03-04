function showImg(messageId) {
	$(".full_overlay").show();
	var h = $(window).height();
	var w = $(window).width();
	var url = 'ajax/connector.php?cmd=display_blob&type=photo&id=' + devId + '&messageid=' + messageId;
	$('#img-container').html("<div class='img-display'><span onclick='hideImg()'>Click image to close</span> | <span onclick='fullscreenImg(" + messageId + ")'>Full resolution</span></div><div class='img-display' onclick='hideImg()'><img width=\"640\" src=\"" + url + "\"></div>");
	$('#img-container').show();
}

function fullscreenImg(messageId) {
	var url = 'ajax/connector.php?cmd=display_blob&type=photo&id=' + devId + '&messageid=' + messageId;
	var win = window.open(url, '_blank');
	win.focus();
}

function hideImg() {
	$(".full_overlay").hide();
	$('#img-container').hide();
	$('#img-container').html("");
}

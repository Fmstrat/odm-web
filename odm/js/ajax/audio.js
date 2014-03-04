function playAudio(messageId) {
	$(".full_overlay").show();
	var h = $(window).height();
	var w = $(window).width();
	var url = 'ajax/connector.php?cmd=display_blob&type=audio&id=' + devId + '&messageid=' + messageId;
	$('#img-container').html("<div class='img-display'><span onclick='hideImg()'>Click to close</span></div><audio src=\"" + url + "\" preload=\"auto\" controls><p>Your browser does not support the audio element </p></audio>");
	$('#img-container').show();
}

function hideAudio() {
	$(".full_overlay").hide();
	$('#img-container').hide();
	$('#img-container').html("");
}
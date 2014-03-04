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

// our custom alert dialog
function showPrompt(callback, placeholder1, placeholder2, placeholder3) {
	$(".full_overlay").show();
	var pPrompt = $("#pPrompt");
	var form = $("#pForm");
	
	// set the placeholder
	form.find('#pForm_field1').attr("placeholder", placeholder1);
	if(placeholder2) // field 2 is optional
		form.find('#pForm_field2').attr("placeholder", placeholder2);
	else
		form.find('#pForm_field2').remove();
	if(placeholder3) // field 3 is optional
		form.find('#pForm_field3').attr("placeholder", placeholder3);
	else
		form.find('#pForm_field3').remove();
	
	// set button functions
	// Setting the DOM element's onclick to null removes 
	// the inline click handler
	$('#pForm_ok')[0].onclick = null;
	$('#pForm_ok').unbind("click");
	$('#pForm_ok').click(callback);
	
	pPrompt.show();
}

function promptDefaultCallbackOK() {
	$(".full_overlay").hide();
	var pPrompt = $("#pPrompt");
	$("#pForm")[0].reset();
	pPrompt.hide();
}

function promptDefaultCallbackCancel() {
	$(".full_overlay").hide();
	var pPrompt = $("#pPrompt");
	$("#pForm")[0].reset();
	pPrompt.hide();
}
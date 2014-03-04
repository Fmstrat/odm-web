function toggleWait() {
	$('#command-wait-dropdown').slideToggle("slow");
}

function toggleWaitOff() {
	$('#command-wait-dropdown').hide();
	$("#cmd_form_done").hide(); // mobile only
	$("#cmd_form_fail").hide(); // mobile only
}

function toggleDevices() {
	var $devdrop = $('#device-dropdown');
	
	if(!$("#device_button").hasClass("noclick")) {
		$devdrop.toggle();
	}
	
	$('#command-dropdown').hide();
}

function toggleNewDevice(){
	$('#device-dropdown').hide();
	$('#command-dropdown').hide();
		
	$('#new-device').slideToggle("slow");
}

function toggleCommands() {
	var $cmddrop = $('#command-dropdown');
	
	if(!$("#button").hasClass("noclick")) {
		$cmddrop.toggle();
	}
	
	$('#device-dropdown').hide();
}	

function hideMessageField(){
	$('#cmd_message_field').hide();
}

function toggleMessageField(placeholder){
	$('#cmd_message_field').attr("placeholder", placeholder);
	$('#cmd_message_field').slideToggle("slow");
}

function hideMessageField2(){
	$('#cmd_message_field2').hide();
}

function buttonLoading(){
	var src = "images/loading.gif";
	$("#button img").attr("src", src);
	$("#button").addClass("noclick");
	$("#device_button").addClass("noclick");
}

function buttonNormal(){
	var src = "images/send-cmd.png";
	$("#button img").attr("src", src);
	$("#button").removeClass("noclick");
	$("#device_button").removeClass("noclick");
}

function toggleMessageField2(placeholder){
	$('#cmd_message_field2').attr("placeholder", placeholder);
	$('#cmd_message_field2').slideToggle("slow");
}

function toggleControls() {
	if($('#togglecontrols').hasClass("dev_togglecontrolsup")) {
		$('#togglecontrols').removeClass("dev_togglecontrolsup");
	} else {
		$('#togglecontrols').addClass("dev_togglecontrolsup");
	}
	$('.dev_mainoverlay').slideToggle("slow");
}

function hideControls() {
	$('#togglecontrols').removeClass("dev_togglecontrolsup");
	$('.dev_mainoverlay').hide();
}

function selectDevice(id) {
	$('#device-dropdown').hide();
	window.location.href = "?id="+id;
}

function cmdChange() {
	var selectBox = document.getElementById("cmd_select_box");
	var selectedValue = selectBox.options[selectBox.selectedIndex].value;
	
	switch(selectedValue) {
		case "locate": hideMessageField(); hideMessageField2(); break;
		case "locategps": hideMessageField(); hideMessageField2(); break;
		case "lock": hideMessageField(); hideMessageField2(); break;
		case "lockpassword": toggleMessageField("Passwort"); hideMessageField2(); break;
		case "rearcam": hideMessageField(); hideMessageField2(); break;
		case "frontcam": hideMessageField(); hideMessageField2(); break;
		case "ring": hideMessageField(); hideMessageField2(); break;
		case "ringoff": hideMessageField(); hideMessageField2(); break;
		case "sms": toggleMessageField("Telefonnummer"); toggleMessageField2("Nachricht"); break;
		case "wipe": hideMessageField(); hideMessageField2(); break;
		case "notify": toggleMessageField("Nachrichtentext"); hideMessageField2(); break;
		case "audio": toggleMessageField("Aufnahmedauer"); hideMessageField2(); break;
		case "delete": hideMessageField(); hideMessageField2(); break;
		default: hideMessageField(); hideMessageField2(); break;
	}
	
	// hide status if new command chosen
	$("#cmd_form_done").hide(); // mobile only
	$("#cmd_form_fail").hide(); // mobile only
}

function cancelWait() {
	$('#command-wait-dropdown').hide();
	lastCommandId = null;
	lastCommandType = null;
	buttonNormal();
}
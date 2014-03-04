<?php
include '../include/core.php';

// use https://code.google.com/p/quick-json/ for parsing the result on android

function respond($json = array("result"=>false, "error"=>"unknown error")) {
	$encoded = json_encode($json);
	header('Content-type: application/json');
	exit($encoded);
}

$cmd = null;
if(isset($_GET["cmd"])) {
	$cmd = $_GET["cmd"];
} else {
	respond(array("result"=>false, "message"=>"commandexpected"));
}

//error_log("receiving message");
//error_log(print_r($_POST,true));
//error_log(print_r($_FILES, true));

//if($cmd !== "appversion") // do not auth if checking version
include '../include/checkpostlogin.php';

switch($cmd) {
	case "version":	
		respond(array("result"=>true, "apk_version"=>$apk_version, "proto_version"=>$proto_version));
		break;
	case "register":
		if (isset($_POST["dev_name"]) && isset($_POST["reg_id"])) {
			$name = $_POST["dev_name"];
			$gcm_regid = $_POST["reg_id"]; // GCM Registration ID
			
			if($user->getDevice("gcm_regid", $gcm_regid) == null) {
				$status = $user->createDevice($name, $gcm_regid); // user object is created in checkpostlogin.php
				if($status !== false)
					respond(array("result"=>true, "message"=>"reg", "token"=>$user->token));
				else 
					respond(array("result"=>false, "message"=>"device creation error"));
			} else {
				respond(array("result"=>true, "message"=>"rereg", "token"=>$user->token));
			}
		} else {
			respond(array("result"=>false, "message"=>"malformedrequest"));
		}
		break;
	case "message":
		if (isset($_POST["request_id"]) && isset($_POST["reg_id"]) && isset($_POST["json_data"])) {
			$reqid = $_POST["request_id"];
			$gcm_regid = $_POST["reg_id"]; // GCM Registration ID
			$json_data = json_decode($_POST["json_data"]);
			if($json_data === null || !isset($json_data->datarow)) {
				respond(array("result"=>false, "message"=>"malformeddata"));
			}
			
			$status = true;
			
			$device = $user->getDevice("gcm_regid", $gcm_regid);
			if($device === null) {
				respond(array("result"=>false, "message"=>"gcmidwrong"));
			}
			
			$info = $device->getInfo("id", $reqid); // could also be done via DeviceInfo($reqid); but is is more safe - we could verify the device
			if($info !== null) {
				$i = 0;
				foreach($json_data->datarow as $datarow) {
					$key = null;
					$value = null;
					$longvalue = null;
					$blob = null;
					
					if(isset($datarow->key))
						$key = $datarow->key;
					else
						respond(array("result"=>false, "message"=>"malformeddata"));
					if(isset($datarow->value))
						$value = $datarow->value;
					if(isset($datarow->longvalue))
						$longvalue = $datarow->longvalue;
					if(isset($datarow->blob) && isset($_FILES["blob"])) {
						$blobpath = $_FILES["blob"]['tmp_name'];
						$blob = file_get_contents($blobpath);
						$value = $datarow->blob; // this field contains the contenttype!
					}
					
					if($i == 0) {
						$res = $info->createInitialData($_SERVER["REMOTE_ADDR"], $json_data->type, $key, $value, $longvalue, $blob);
						$i++;
					} else {
						$res = $info->createData($key, $value, $longvalue, $blob);
					}
					
					if($res === FALSE) {
						$status = false;
					}
				}
				
				if($status !== false)
					respond(array("result"=>true, "message"=>"datasaved"));
				else 
					respond(array("result"=>false, "message"=>"datanotsaved"));
			} else {
				respond(array("result"=>false, "message"=>"requestidwrong"));
			}
		} else {
			respond(array("result"=>false, "message"=>"malformedrequest"));
		}
		break;
	case "location": // access for location history
		if (isset($_POST["reg_id"]) && isset($_POST["json_data"])) {
			$gcm_regid = $_POST["reg_id"]; // GCM Registration ID
			$json_data = json_decode($_POST["json_data"]);
			if($json_data === null || !isset($json_data->datarow)) {
				respond(array("result"=>false, "message"=>"malformeddata"));
			}
			
			$status = true;
			
			$device = $user->getDevice("gcm_regid", $gcm_regid);
			if($device === null) {
				respond(array("result"=>false, "message"=>"gcmidwrong"));
			}
			
			foreach($json_data->datarow as $datarow) {
				if(!isset($datarow->longitude) || !isset($datarow->latitude) || !isset($datarow->timestamp) || !isset($datarow->type))
					respond(array("result"=>false, "message"=>"malformeddata"));
			
				$altitude = null;
				if(isset($datarow->altitude))
						$altitude = $datarow->altitude;
						
				$accuracy = null;
				if(isset($datarow->accuracy))
						$accuracy = $datarow->accuracy;
				
			
				$res = $device->createLocation($datarow->longitude, $datarow->latitude, $altitude, $accuracy, $datarow->timestamp, $datarow->type);
				
				if($res === FALSE) {
					$status = false;
				}
			}
			
			if($status !== false)
				respond(array("result"=>true, "message"=>"datasaved"));
			else 
				respond(array("result"=>false, "message"=>"datanotsaved"));
			
		} else {
			respond(array("result"=>false, "message"=>"malformedrequest"));
		}
		break;
		
	default: respond(array("result"=>false, "error"=>"unknowncommand")); break;
}
?>
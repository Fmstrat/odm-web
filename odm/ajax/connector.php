<?php
include '../include/core.php';
include '../include/form_submit.php';

function respond($json = array("result"=>false, "message"=>"unknownerror")) {
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

if($cmd === "formsubmit" || $cmd === "qrcode") {}// do not auth if submitting a form (either reg or login)
else	
	include '../include/checklogin.php'; 

switch($cmd) {
	case "dev_remove": 
		if (isset($_GET["id"])) {
			$id = $_GET["id"];
			$dev = new Device($id);
			if($dev->checkSession()) {
				$dev->delete();
				respond(array("result"=>true));
			} else {
				respond(array("result"=>false, "message"=>"sessionidwrong"));
			}
		} else {
			respond(array("result"=>false, "message"=>"malformedrequest"));
		}
		break;
	case "logout":
		global $CURUSR;
		$CURUSR = null; // destroy current user obj
		session_destroy();
		header("Location: $WEB_BASE_PATH/odm.php?p=login"); // here we are not responding json!
		break;
	case "display_blob":
		if (isset($_GET["id"]) && isset($_GET["messageid"]) && isset($_GET["type"])) {
			$id = $_GET["id"];
			$type = $_GET["type"];
			$messageid = $_GET["messageid"];
			$info = new DeviceInfo($messageid);
			if($info->has_data == 1) {
				$info->loadData();
				foreach($info->data as $data) {
					if($data->key == $type) {
						$data->blob;
						header('Content-Type: ' . $data->value); // here we are not responding json!
						header('Content-Length: ' . strlen($data->blob));
						exit($data->blob);
					}
				}
			}
		} else {
			respond(array("result"=>false, "message"=>"malformedrequest"));
		}
		break;
	case "qrcode":
		header('Content-Type: image/png'); // here we are not responding json!
		exit(QRcode::png($GOOGLE_PLAY_STORE));
		break;
	case "dev_sendmessage":
		if (isset($_POST["id"]) && isset($_POST["message"]) && isset($_POST["type"])) {
			$id = $_POST["id"];
			$message = $_POST["message"];
			$type = $_POST["type"];
			$dev = new Device($id);
			$result = $dev->sendRequest($message, $type);
			respond(array("result"=>true, "message"=>"requestsend", "messageid"=>$result));
		} else {
			respond(array("result"=>false, "message"=>"malformedrequest"));
		}
		break;
	case "dev_getlog":
		if (isset($_POST["id"]) && isset($_POST["max"])) {
			$id = $_POST["id"];
			$limit = array(0,$_POST["max"]);
			$dev = new Device($id);
			$dev->loadInfo($limit);
			
			$datarow = array();
			$hasdata = false;
			
			foreach($dev->info as $info) {
				$hasdata = true;
				$row = array();
				$row["id"] = $info->id;
				$row["requ_date"] = $info->requ_date;
				$row["request"] = $info->request;
				$row["type"] = $info->type;
				$row["has_data"] = $info->has_data;
				$datarow[] = $row;
			}
			
			respond(array("result"=>true, "message"=>"dataloaded", "hasdata"=>$hasdata, "datarow"=>$datarow));
		} else {
			respond(array("result"=>false, "message"=>"malformedrequest"));
		}
		break;
	case "dev_getmessageresponse":
		if (isset($_POST["id"]) && isset($_POST["messageid"])) {
			$id = $_POST["id"];
			$messageid = $_POST["messageid"];
			$msg = new DeviceInfo($messageid);
			
			$datarow = array();
			$hasdata = false;
			
			// handle correct message type:
			switch($msg->type) {
				case "location":
					if($msg->has_data == 1) {
						$hasdata = true;
						$msg->loadData();
						foreach($msg->data as $data) {
							$datarow[$data->key] = $data->value;
						}
					}
					break;
				case "photo":
					if($msg->has_data == 1) {
						$hasdata = true;
						$msg->loadData();
						foreach($msg->data as $data) {
							$datarow[$data->key] = $data->value;
						}
					}
					break;
				case "info":
					if($msg->has_data == 1) {
						$hasdata = true;
						$msg->loadData();
						foreach($msg->data as $data) {
							$datarow[$data->key] = $data->value;
						}
					}
					break;
				default:
					break;
			}
			
			respond(array("result"=>true, "message"=>"dataloaded", "hasdata"=>$hasdata, "datarow"=>$datarow));
		} else {
			respond(array("result"=>false, "message"=>"malformedrequest"));
		}
		break;
	case "formsubmit":
		$result = submitForm();
		respond($result);
		break;
	default: respond(array("result"=>false, "message"=>"unknowncommand")); break;
}
?>
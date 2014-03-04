<?php
include_once(__DIR__ . '/device_info.class.php');
include_once(__DIR__ . '/device_location.class.php');
/**
 * All database tables MUST HAVE a id column as primary key!
 * There is no autodetection of the primary key!
 */
class Device extends DynamicDatabaseObject{
	protected $mapped_table_name = "sdm_device";
	private $mapped_table_name_info = "sdm_device_info";
	private $mapped_table_name_location = "sdm_device_location";
	
    public function __construct($id = null) {
		// call parent constructor
        parent::__construct($id);
		
		$this->_data["info"] = null;
		$this->_data["location"] = null;
    }
	
	public function loadLocation($limit = array(0,100)) {
		$return = null;
		if($this->_objDataCount > 0 && $this->__get("id") != null) {
			dbconnect();
			$sql = "SELECT `id` FROM `{$this->mapped_table_name_location}` WHERE `device_id`=? ORDER BY `id` DESC LIMIT ?,?"; // order newest entries first
			global $con;
			$stmt = $con->prepare($sql);
			$stmt->execute(array($this->__get("id"), $limit[0], $limit[1]));
			
			$check_count = $stmt->rowCount();
			if ($check_count > 0) {
				$this->_data["location"] = array();
				
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $row) {
					$this->_data["location"][] = new DeviceLocation($row["id"]);
				}
				
				$return = $check_count;
			} else {
				$this->_data["location"] = null;
			}
			dbclose();
		} else {
			$this->_data["location"] = null;
		}
		return $return;
	}
	
	public function loadInfo($limit = array(0,100)) {
		$return = null;
		if($this->_objDataCount > 0 && $this->__get("id") != null) {
			dbconnect();
			$sql = "SELECT `id` FROM `{$this->mapped_table_name_info}` WHERE `device_id`=? ORDER BY `id` DESC LIMIT ?,?"; // order newest entries first
			global $con;
			$stmt = $con->prepare($sql);
			$stmt->execute(array($this->__get("id"), $limit[0], $limit[1]));
			
			$check_count = $stmt->rowCount();
			if ($check_count > 0) {
				$this->_data["info"] = array();
				
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $row) {
					$this->_data["info"][] = new DeviceInfo($row["id"]);
				}
				
				$return = $check_count;
			} else {
				$this->_data["info"] = null;
			}
			dbclose();
		} else {
			$this->_data["info"] = null;
		}
		return $return;
	}
	
	public function getLocation($searchtype = "id", $searchstring , $limit = array(0,100)) {
		$return = null;
		if($this->_objDataCount > 0 && $this->__get("id") != null) {
			if($this->_data["location"] == null) {
				$this->loadLocation($limit);
			}
			
			if($this->_data["location"] != null) {
				foreach($this->_data["location"] as $location) {
					if($location->$searchtype == $searchstring) {
						$return = $location;
						break;
					}
				}
			} 
		}
		return $return;
	}
	
	public function getInfo($searchtype = "id", $searchstring , $limit = array(0,100)) {
		$return = null;
		if($this->_objDataCount > 0 && $this->__get("id") != null) {
			if($this->_data["info"] == null) {
				$this->loadInfo($limit);
			}
			
			if($this->_data["info"] != null) {
				foreach($this->_data["info"] as $info) {
					if($info->$searchtype == $searchstring) {
						$return = $info;
						break;
					}
				}
			} 
		}
		return $return;
	}
	
	public function sendRequest($message, $type, $token = null) {
		if($token === null) {
			$user = new User($this->__get("user_id"));
			$token = $user->token;
		}
			
		$mcrypt = new MCrypt();
		$key = $mcrypt->formatKey($token);
		$encrypted = $mcrypt->encrypt($message, $key);
		$registration_ids = array($this->__get("gcm_regid"));
		
		//create a new "request" info
		$info = $this->createInfo($message, $type);
		
		$messageA = array("message" => $encrypted, "requestid" => $info->id);
		$result = send_notification($registration_ids, $messageA);
		$info->gcm_result = $result;
		$info->update();
		
		return $info->id;
	}
	
	public function createInfo($request, $type = null, $requ_date = null) {
		$info = new DeviceInfo();
		$info->device_id = $this->__get("id");
		$info->request = $request;
		$info->has_data = 0;
		$info->type = $type; // type will normally be set when the response was received
		if($requ_date !== null)
			$info->requ_date = $requ_date;
		else
			$info->requ_date = date('Y-m-d H:i:s');
			
		$iid = $info->create();
			
		if($iid == null)
			return false;
				
		$this->_data["info"][] = $info;
		
		return $info;
	}
	
	public function createLocation($longitude, $latitude, $altitude = null, $accuracy = null, $timestamp, $type, $recv_date = null) {
		$location = new DeviceLocation();
		$location->longitude = $longitude;
		$location->latitude = $latitude;
		$location->altitude = $altitude;
		$location->accuracy = $accuracy;
		$location->timestamp = $timestamp;
		$location->type = $type;
		$location->device_id = $this->__get("id");		
		if($recv_date !== null)
			$location->recv_date = $recv_date;
		else
			$location->recv_date = date('Y-m-d H:i:s');
			
		$lid = $location->create();
			
		if($lid == null)
			return false;
				
		$this->_data["location"][] = $location;
		
		return $location;
	}
	
	// check if this device belongs to the user that is logged in
	public function checkSession() {
		if($_SESSION["user_id"] == $this->__get("user_id"))
			return true;
		
		return false;
	}
}
?>
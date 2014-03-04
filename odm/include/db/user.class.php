<?php
include_once(__DIR__ . '/device.class.php');
include_once(__DIR__ . '/user_setting.class.php');
/**
 * All database tables MUST HAVE a id column as primary key!
 * There is no autodetection of the primary key!
 */
class User extends DynamicDatabaseObject{
	protected $mapped_table_name = "sdm_user";
	private $mapped_table_name_device = "sdm_device";
	private $mapped_table_name_setting = "sdm_user_setting";
	
    public function __construct($id = null) {
		// call parent constructor
        parent::__construct($id);
		
		$this->_data["device"] = null;
		$this->_data["setting"] = null;
    }
	
	public function loadDevices() {
		$return = null;
		if($this->_objDataCount > 0 && $this->__get("id") != null) {
			dbconnect();
			$sql = "SELECT `id` FROM `{$this->mapped_table_name_device}` WHERE `user_id`=?";
			global $con;
			$stmt = $con->prepare($sql);
			$stmt->execute(array($this->__get("id")));
			
			$check_count = $stmt->rowCount();
			if ($check_count > 0) {
				$this->_data["device"] = array();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $row) {
					$this->_data["device"][] = new Device($row["id"]);
				}
				
				$return = $check_count;
			} else {
				$this->_data["device"] = null;
			}
			dbclose();
		} else {
			$this->_data["device"] = null;
		}
		return $return;
	}
	
	public function loadSettings() {
		$return = null;
		if($this->_objDataCount > 0 && $this->__get("id") != null) {
			dbconnect();
			$sql = "SELECT `id` FROM `{$this->mapped_table_name_setting}` WHERE `user_id`=?";
			global $con;
			$stmt = $con->prepare($sql);
			$stmt->execute(array($this->__get("id")));
			
			$check_count = $stmt->rowCount();
			if ($check_count > 0) {
				$this->_data["setting"] = array();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $row) {
					$this->_data["setting"][] = new UserSetting($row["id"]);
				}
				
				$return = $check_count;
			} else {
				$this->_data["setting"] = null;
			}
			dbclose();
		} else {
			$this->_data["setting"] = null;
		}
		return $return;
	}
	
	public function getDevice($searchtype = "id", $searchstring) {
		$return = null;
		if($this->_objDataCount > 0 && $this->__get("id") != null) {
			if($this->_data["device"] == null) {
				$this->loadDevices();
			}
			
			if($this->_data["device"] != null) {
				foreach($this->_data["device"] as $device) {
					if($device->$searchtype == $searchstring) {
						$return = $device;
						break;
					}
				}
			} 
		}
		return $return;
	}
	
	public function getSetting($searchtype = "id", $searchstring) {
		$return = null;
		if($this->_objDataCount > 0 && $this->__get("id") != null) {
			if($this->_data["setting"] == null) {
				$this->loadSettings();
			}
			
			if($this->_data["setting"] != null) {
				foreach($this->_data["setting"] as $setting) {
					if($setting->$searchtype == $searchstring) {
						$return = $setting;
						break;
					}
				}
			} 
		}
		return $return;
	}
	
	public function initByUsername($username) {
		$sql = "SELECT * FROM `{$this->mapped_table_name}` WHERE `username`=?";
		global $con;
		$stmt = $con->prepare($sql);
		$stmt->execute(array($username));
		
		$this->_objDataCount = $stmt->rowCount();
		if ($this->_objDataCount > 0) {
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $row) {
				$this->_objDataCount = count($row);
				foreach ($row as $key => $value) {
					$this->_data[$key] = $value;
				}
			}
			
			$this->saved = true;
		}
	}
	
	public function checkPassword($password) {
		// PASSWORD HASHING WITH BCRYPT - CURRENTLY not possible on android
		// TODO: find solution
		if (password_verify($password, $this->__get("hash")))
			return true;
		
		return false;
	}
	
	public function setPassword($password) {
		// PASSWORD HASHING WITH BCRYPT - CURRENTLY not possible on android
		// TODO: find solution
		$options = array('cost' => 11);
		$this->__set("hash", password_hash($password, PASSWORD_BCRYPT, $options));
	}
	
	function genenrate_salt(){
		$rndstring = "";
		$length = 64;
		$a = "";
		$b = "";
		$template = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		settype($length, "integer");
		settype($rndstring, "string");
		settype($a, "integer");
		settype($b, "integer");
		 
		for ($a = 0; $a <= $length; $a++) {
			$b = rand(0, strlen($template) - 1);
			$rndstring .= $template[$b];
		}
		return $rndstring;
	}
	
	// sha256 hashing
	function genenrate_password($salt,$pass){
		$password_hash = '';
	 
		$mysalt = $salt;
		$password_hash= hash('SHA256', "-".$mysalt."-".$pass."-");
	 
		return $password_hash;
	}
	
	public function generateToken($length=16) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		$this->__set("token", $randomString);
		return $randomString;
	}
	
	public function getDeviceCount() {	
		if($this->_data["device"] == null) {
			$this->loadDevices();
		}
			
		if(is_array($this->_data["device"]))
			return count($this->_data["device"]);
		
		return 0;
	}
	
	public function createDevice($name, $gcm_regid) {	
		if($this->_data["device"] == null)
			$this->loadDevices();			
		if(!is_array($this->_data["device"]))
			$this->_data["device"] = array();
			
		$dev = new Device();
		$dev->user_id = $this->__get("id");
		$dev->name = $name;
		$dev->active = 1;
		$dev->gcm_regid = $gcm_regid;
		
		$did = $dev->create();
			
		if($did == null)
			return false;
				
		$this->_data["device"][] = $dev;
		
		return $dev;
	}
}
?>
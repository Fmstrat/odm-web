<?php
include_once(__DIR__ . '/device_info_data.class.php');
/**
 * All database tables MUST HAVE a id column as primary key!
 * There is no autodetection of the primary key!
 */
class DeviceInfo extends DynamicDatabaseObject{
	protected $mapped_table_name = "sdm_device_info";
	private $mapped_table_name_data = "sdm_device_info_data";
	
    public function __construct($id = null) {
		// call parent constructor
        parent::__construct($id);
		
		$this->_data["data"] = null;
    }
	
	public function loadData() {
		$return = null;
		if($this->_objDataCount > 0 && $this->__get("id") != null && $this->__get("has_data") == 1) {
			dbconnect();
			$sql = "SELECT `id` FROM `{$this->mapped_table_name_data}` WHERE `device_info_id`=?";
			global $con;
			$stmt = $con->prepare($sql);
			$stmt->execute(array($this->_data["id"]));
			
			$check_count = $stmt->rowCount();
			if ($check_count > 0) {
				$this->_data["data"] = array();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $row) {
					$this->_data["data"][] = new DeviceInfoData($row["id"]);
				}
				
				$return = $check_count;
			} else {
				$this->_data["data"] = null;
			}
			dbclose();
		} else {
			$this->_data["data"] = null;
		}
		return $return;
	}
	
	public function getData($searchtype = "id", $searchstring) {
		$return = null;
		if($this->_objDataCount > 0 && $this->__get("id") != null && $this->__get("has_data") == 1) {
			if($this->_data["data"] == null) {
				$this->loadData();
			}
			
			if($this->_data["data"] != null) {
				foreach($this->_data["data"] as $infoData) {
					if($infoData->$searchtype == $searchstring) {
						$return = $infoData;
						break;
					}
				}
			} 
		}
		return $return;
	}
	
	public function createInitialData($ip, $type, $key, $value = null, $longvalue = null, $blob = null, $recv_date = null) {
		$data = new DeviceInfoData();
		$data->device_info_id = $this->__get("id");
		$data->key = $key;
		if($value !== null)
			$data->value = $value;
		if($longvalue !== null)
			$data->longvalue = $longvalue;
		if($blob !== null)
			$data->blob = $blob;
		$did = $data->create();
			
		if($did == null)
			return false;
		
		$this->_data["has_data"] = 1;
		if($recv_date !== null)
			$this->__set("recv_date", $recv_date);
		else
			$this->__set("recv_date",date('Y-m-d H:i:s'));
		$this->__set("type",$type);
		$this->__set("response_ip", $ip);
		$result = $this->update();
		
		$this->_data["data"][] = $data;
			
		return $data;
	}
	
	public function createData($key, $value = null, $longvalue = null, $blob = null) {
		$data = new DeviceInfoData();
		$data->device_info_id = $this->__get("id");
		$data->key = $key;
		if($value !== null)
			$data->value = $value;
		if($longvalue !== null)
			$data->longvalue = $longvalue;
		if($blob !== null)
			$data->blob = $blob;
		$did = $data->create();
			
		if($did == null)
			return false;
			
		$this->_data["data"][] = $data;
			
		return $data;
	}
}
?>
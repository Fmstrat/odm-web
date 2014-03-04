<?php
/**
 * All database tables MUST HAVE a id column as primary key!
 * There is no autodetection of the primary key!
 */
class DynamicDatabaseObject {
    protected $_data = array();
	protected $db_header = array();
	protected $_objDataCount = 0;
	protected $saved = false;
	protected $debug = true;
	protected $mapped_table_name = null;
	
    public function __construct($id = null) {
		dbconnect();		
		$this->getDbHeader(); // load db structure to object
		
		if($id != null) {
			$sql = "SELECT * FROM `{$this->mapped_table_name}` WHERE `id`=?";
			global $con;
			$stmt = $con->prepare($sql);
			$stmt->execute(array($id));
			
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
		} else {
			// INIT empty object
			foreach ($this->db_header as $field) {
				$this->_data[$field] = null;
			}
		}
		dbclose();
    }
	
	public function getDbHeader() {
		$sql = "SELECT `column_name` FROM INFORMATION_SCHEMA.COLUMNS WHERE `table_name` =  '{$this->mapped_table_name}' ORDER BY `table_name`, `ordinal_position`";
		global $con;
		$stmt = $con->prepare($sql);
		$stmt->execute(array());
		
		$this->_objDataCount = $stmt->rowCount();
		if ($this->_objDataCount > 0) {
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);				
			foreach ($rows as $row) {
				$this->db_header[] = $row["column_name"];
			}
		} else {
			throw new Exception('Cannot read column names of ' . $mapped_table_name);
		}
		
		return $this->db_header;
	}

    public function __get($name) {
		if($name == "sdmObjSize")
			return $this->_objDataCount;
			
        if (array_key_exists($name, $this->_data))
            return $this->_data[$name];

        return null;
    }

    public function __set($name, $value) {
		if($name != "id") {// do not overwrite our primary key!!
			$this->_data[$name] = $value;
			$this->saved = false;
		}
    }

    public function update(){
		$return = null;
        dbconnect();
		if($this->_objDataCount > 0 && !$this->saved) {
			$i = 0;
			$prepArray = array();
			$sql = "UPDATE `{$this->mapped_table_name}` SET ";			
			foreach($this->_data as $field => $value) {
				if($field != "id" && in_array($field, $this->db_header)) {
					if($i > 0)
						$sql .= ", ";
						
					$sql .= "`$field`=?";
					$prepArray[] = $value;
					$i++;
				}
			}
			$sql .= " WHERE `id`=?";
			$prepArray[] = $this->__get("id");
			
			global $con;
			try {
				$stmt = $con->prepare($sql);
				$stmt->execute($prepArray);
				
				$this->saved = true;
				$return = true;
			} catch(Exception $e) {
				error_log($e->getMessage());
				$this->log($sql);
				$this->saved = false;				
				$return = null;
			}
		}
		dbclose();
		return $return;
    }
	
	public function delete(){
		$return = null;
        dbconnect();
		if($this->_objDataCount > 0 && $this->__get("id")!= null) {
			$sql = "DELETE FROM `{$this->mapped_table_name}` WHERE `id`=?";			
						
			global $con;
			try {
				$stmt = $con->prepare($sql);
				$stmt->execute(array($this->__get("id")));				
				$this->saved = false;
				
				$return = true;
			} catch(Exception $e) {
				error_log($e->getMessage());
				$this->log($sql);
				$this->saved = false;
				
				$return = null;
			}
		}
		dbclose();
		return $return;
    }
	
	public function create(){
        dbconnect();
		if($this->_objDataCount > 0 && !$this->saved) {
			$i = 0;
			$prepArray = array();
			$sql = "INSERT INTO `{$this->mapped_table_name}` SET ";			
			foreach($this->_data as $field => $value) {
				if($field != "id" && in_array($field, $this->db_header)) {
					if($i > 0)
						$sql .= ", ";
						
					$sql .= "`$field`=?";
					$prepArray[] = $value;
					$i++;
				}
			}
			
			global $con;
			try {
				$stmt = $con->prepare($sql);
				$stmt->execute($prepArray);
				
				$this->_data["id"] = $con->lastInsertId(); // dont use __set here, id is writeprotected!
				$this->saved = true;
				$return = $this->__get("id");
			} catch(Exception $e) {
				$this->log($e->getMessage());
				$this->log($sql);
				
				$this->_data["id"] = null;
				$this->saved = false;
				
				$return = null;
			}
		}
		dbclose();
		return $return;
    }
	
	protected function log($val) {
		if($this->debug) {
			error_log(print_r($val, true));
			error_log(print_r(debug_backtrace(), true));
		}
	}
}
?>
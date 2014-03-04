<?php
include_once(__DIR__ . '/dynamic_database_object.class.php');
/**
 * All database tables MUST HAVE a id column as primary key!
 * There is no autodetection of the primary key!
 */
class DeviceLocation extends DynamicDatabaseObject{
	protected $mapped_table_name = "sdm_device_location";
	
    public function __construct($id = null) {
		// call parent constructor
        parent::__construct($id);
    }
}
?>
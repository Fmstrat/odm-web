<?php
include_once(__DIR__ . '/dynamic_database_object.class.php');
/**
 * All database tables MUST HAVE a id column as primary key!
 * There is no autodetection of the primary key!
 */
class UserSetting extends DynamicDatabaseObject{
	protected $mapped_table_name = "sdm_user_setting";
	
    public function __construct($id = null) {
		// call parent constructor
        parent::__construct($id);
    }
	
	// check if this setting belongs to the user that is logged in
	public function checkSession() {
		if($_SESSION["user_id"] == $this->__get("user_id"))
			return true;
		
		return false;
	}
}
?>
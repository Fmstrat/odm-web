<?php
include(__DIR__ .'/user.class.php');

class DeviceManager {
	/**
    * instance
    *
    * Statische Variable, um die aktuelle (einzige!) Instanz dieser Klasse zu halten
    *
    * @var Singleton
    */
	protected static $_instance = null;
   
	private $mapped_table_name = "sdm_user";
	
	private $users = null;
	private $current_user = null;
	
	/**
     * get instance
     *
     * Falls die einzige Instanz noch nicht existiert, erstelle sie
     * Gebe die einzige Instanz dann zurück
     *
     * @return   Singleton
     */
	public static function getInstance(){
		if (self::$_instance === null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
   
   /**
    * clone
    *
    * Kopieren der Instanz von aussen ebenfalls verbieten
    */
	protected function __clone() {}
 
   /**
    * constructor
    *
    * externe Instanzierung verbieten
    */   
	protected function __construct() {
		if(isset($_SESSION["user_id"])) {
			$this->current_user = new User($_SESSION["user_id"]);
			$this->users = array();
			$this->users[] = $this->current_user;
		} else {
			$this->loadUsers(); // TODO: is this a good idea?
		}
    }
	
	public function loadUsers() {
		$return = null;
		
		dbconnect();
		$sql = "SELECT `id` FROM `{$this->mapped_table_name}`"; // order newest entries first
		global $con;
		$stmt = $con->prepare($sql);
		$stmt->execute(array());
		
		$check_count = $stmt->rowCount();
		if ($check_count > 0) {
			$this->users = array();
			
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $row) {
				$this->users[] = new User($row["id"]);
			}
			
			$return = $check_count;
		} else {
			$this->users = null;
		}
		dbclose();
		
		return $return;
	}
	
	public function __get($name) {
        return $this->$name;
    }

    public function __set($name, $value) {
		$this->$name = $value;
    }
	
	/**
	 * Helpful functions
	 */
	 
	function getUserCount() {
		if(is_array($this->users))
			return  count($this->users);
		
		return 0;
	}
	
	function getUserByID($id) {
		if(is_array($this->users)) {
			foreach($this->users as $user) {
				if($user->id == $id) {
					return $user;
				}
			}
		} else {
			return false;
		}
	}
	
	function getUserByName($username) {
		if(is_array($this->users)) {
			foreach($this->users as $user) {
				if($user->username == $username) {
					return $user;
				}
			}
		} else {
			return null;
		}
	}
	
	function userExistsByName($username) {
		return $this->getUserByName($username) == null ? false : true;
	}
	
	function userExistsById($id) {
		return $this->getUserById($id) == null ? false : true;
	}
	
	function createUser($username, $email, $password) {
		if(!is_array($this->users)) {
			$this->users = array();
		}
		
		if(!$this->userExistsByName($username)) {
			$user = new User();
			$user->username = $username;
			$user->email = $email;
			$user->setPassword($password);
			$user->generateToken();
			$uid = $user->create();
			
			if($uid == null)
				return false;
				
			$this->users[] = $user;
				
			return $user;
		} else {
			return false;
		}
	}
}
?>
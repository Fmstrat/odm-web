<?php

	$con = null;

	function dbconnect() {
		global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_DATABASE, $con;
		if (defined('PDO::MYSQL_ATTR_MAX_BUFFER_SIZE')) {
			$con = new PDO('mysql:dbname='.$DB_DATABASE.';host='.$DB_HOST.';charset=utf8', $DB_USER, $DB_PASSWORD, array(PDO::MYSQL_ATTR_MAX_BUFFER_SIZE=>1024*1024*50));
		} else {
			$con = new PDO('mysql:dbname='.$DB_DATABASE.';host='.$DB_HOST.';charset=utf8', $DB_USER, $DB_PASSWORD);
		}
		$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	function dbclose() {
		global $con;
		$con = null;
	}

	// Register user
	function storeUser($name, $gcm_regid, $user_id) {
		global $con;
		$stmt = $con->prepare("select * from gcm_users where user_id=? and gcm_regid=?");
		$stmt->execute(array($user_id,$gcm_regid));
		$check_rows = $stmt->rowCount();
		if ($check_rows > 0) {
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $row) {
				$stmt2 = $con->prepare("update gcm_users set name = ? where gcm_regid=?");
				$stmt2->execute(array($name, $gcm_regid));
			}
		} else {
			$stmt = $con->prepare("INSERT INTO gcm_users(name, gcm_regid, user_id, created_at) VALUES(?, ?, ?, NOW())");
			$stmt->execute(array($name, $gcm_regid, $user_id));
		}
		$stmt = $con->prepare("select * from users where user_id=?");
		$stmt->execute(array($user_id));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$token = "";
		foreach ($rows as $row) {
			$token = $row['token'];
		}
		return $token;
	}

	// Insert message into database
	function storeMessage($message, $gcm_regid, $data) {
		global $con;
		$stmt = $con->prepare("INSERT INTO gcm_messages(message, gcm_regid, data, created_at) VALUES(?, ?, ?, NOW())");
		$stmt->execute(array($message, $gcm_regid, $data));
		$id = $con->lastInsertId();
		return $id;
	}

	function storeFile($id, $handle) {
		global $con;
		$stmt = $con->prepare("INSERT INTO gcm_data(id, data) VALUES(?, ?)");
		$stmt->bindParam(1, $id);
		$stmt->bindParam(2, $handle, PDO::PARAM_LOB);
		$stmt->execute();
	}

	function storeData($id, $data) {
		global $con;
		$stmt = $con->prepare("INSERT INTO gcm_data(id, data) VALUES(?, ?)");
		$stmt->execute(array($id, $data));
	}

	function getAllUsers($user_id) {
		global $con;
		$stmt = $con->prepare("select gu.*, u.token FROM gcm_users gu, users u where gu.user_id = u.user_id and u.user_id = ? order by gu.name, gu.created_at;");
		$stmt->execute(array($user_id));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	function getFilename($user_id, $id) {
		global $con;
		$filename = "";
		$stmt = $con->prepare("select m.message from gcm_messages m, gcm_users u where m.gcm_regid = u.gcm_regid and u.user_id = ? and m.id = ?");
		$stmt->execute(array($user_id, $id));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rows as $row) {
			$filename .= $row['message'];
		}
		return str_replace("img:", "", $filename);
	}

	function getImg($user_id, $id) {
		global $con;
		$img = "";
		//$stmt = $con->prepare("select d.data from gcm_data d, gcm_messages m, gcm_users u where d.id = m.id and m.gcm_regid = u.gcm_regid and u.user_id = ? and d.id = ?", array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false));
		$stmt = $con->prepare("select d.data from gcm_data d, gcm_messages m, gcm_users u where d.id = m.id and m.gcm_regid = u.gcm_regid and u.user_id = ? and d.id = ?");
		$stmt->execute(array($user_id, $id));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rows as $row) {
			$img .= $row['data'];
		}
		return $img;
	}

	function getMessages($gcm_regid, $limit) {
		global $con;
		$sql = "select message,created_at,id,data FROM gcm_messages where gcm_regid = ? order by created_at desc";
		if (isset($limit)) {
			$sql .= " limit $limit";
		}
		$stmt = $con->prepare($sql);
		$stmt->execute(array($gcm_regid));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	function getUserRecord($username) {
		global $con;
		$matchrow = null;
		$sql = "select user_id, hash, token from users where username = ?";
		$stmt = $con->prepare($sql);
		$stmt->execute(array($username));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rows as $row) {
			$matchrow = $row;
		}
		return $matchrow;
	}

	function updatePassword($username, $hash) {
		global $con;
		$token = generateRandomString();
		$stmt = $con->prepare("update users set hash = ?, token = ? where username = ?");
		$stmt->execute(array($hash, $token, $username));
		return $token;
	}
 
	function storeUsername($username, $hash) {
		global $con;
		$stmt = $con->prepare("select * from users where username = ?");
		$stmt->execute(array($username));
		$check_rows = $stmt->rowCount();
		if ($check_rows > 0) {
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $row) {
				return $row['token'];
			}
		} else {
			$token = generateRandomString();
			$stmt = $con->prepare("INSERT INTO users(username, hash, token, created_at) VALUES(?, ?, ?, NOW())");
			$stmt->execute(array($username, $hash, $token));
		}
		return $token;
	}

	function validateRegId($user_id, $gcm_regid) {
		global $con;
		$stmt = $con->prepare("select id from gcm_users where user_id = ? and gcm_regid = ?");
		$stmt->execute(array($user_id, $gcm_regid));
		$check_rows = $stmt->rowCount();
		if ($check_rows > 0)
			return true;
		else
			return false;
	}

	function deleteDevice($id, $user_id) {
		global $con;
		$sql = "select gcm_regid from gcm_users where id = ? and user_id = ?";
		$stmt = $con->prepare($sql);
		$stmt->execute(array($id, $user_id));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$gcm_regid = '';
		foreach ($rows as $row) {
			$gcm_regid = $row['gcm_regid'];
		}
		if ($gcm_regid != '') {
			$sql = "delete d.* from gcm_data d where d.id in (select m.id from gcm_messages m where m.gcm_regid = ?)";
			$stmt = $con->prepare($sql);
			$stmt->execute(array($gcm_regid));
			$sql = "delete from gcm_messages where gcm_regid = ?";
			$stmt = $con->prepare($sql);
			$stmt->execute(array($gcm_regid));
			$sql = "delete from gcm_users where id = ?";
			$stmt = $con->prepare($sql);
			$stmt->execute(array($id));
		}
	}

	function GUID() {
		if (function_exists('com_create_guid') === true) {
			return trim(com_create_guid(), '{}');
		}
		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}

	function generateRandomString($length = 16) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}

	function checkDatabase() {
		global $con;
		// Database is missing the token field. Add it, and create a token for encryption
		$sql = "show columns from users like 'token'";
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$check_rows = $stmt->rowCount();
		if ($check_rows == 0) {
			$sql = "alter table users add token varchar(255) not null after hash;";
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$sql = "select * from users;";
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$check_rows = $stmt->rowCount();
			if ($check_rows > 0) {
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $row) {
					$user_id = $row['user_id'];
					$stmt2 = $con->prepare("update users set token = ? where user_id = ?");
					$stmt2->execute(array(generateRandomString(), $user_id));
				}
			}
		}
		// Remove unrequired enckey from gcm_users
		$sql = "show columns from gcm_users like 'enckey'";
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$check_rows = $stmt->rowCount();
		if ($check_rows != 0) {
			$sql = "alter table gcm_users drop enckey;";
			$stmt = $con->prepare($sql);
			$stmt->execute();
		}
		// Expand the data field for larger submissions
		$sql = "show columns from gcm_data like 'data'";
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$check_rows = $stmt->rowCount();
		if ($check_rows != 0) {
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $row) {
				if ($row['Type'] == "blob") {
					$sql = "alter table gcm_data modify column data longblob not null;";
					$stmt = $con->prepare($sql);
					$stmt->execute();
				}
			}
		}
	}
?>

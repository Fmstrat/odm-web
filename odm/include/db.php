<?php

	$con = null;

	function dbconnect() {
		global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_DATABASE, $con;
		$con = new PDO('mysql:dbname='.$DB_DATABASE.';host='.$DB_HOST.';charset=utf8', $DB_USER, $DB_PASSWORD);
		$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	function dbclose() {
		global $con;
		$con = null;
	}

	// Register user
	function storeUser($name, $enckey, $gcm_regid, $user_id) {
		global $con;
		$stmt = $con->prepare("select * from gcm_users where gcm_regid=?");
		$stmt->execute(array($gcm_regid));
		$check_rows = $stmt->rowCount();
		if ($check_rows > 0) {
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $row) {
				if ($row['enckey'] == $enckey) {
					$stmt2 = $con->prepare("update gcm_users set name = ? where gcm_regid=?");
					$stmt2->execute(array($name, $gcm_regid));
				} else {
					return false;
				}
			}
		} else {
			$stmt = $con->prepare("INSERT INTO gcm_users(name, enckey, gcm_regid, user_id, created_at) VALUES(?, ?, ?, ?, NOW())");
			$stmt->execute(array($name, $enckey, $gcm_regid, $user_id));
		}
	}

	// Insert message into database
	function storeMessage($message, $gcm_regid, $data) {
		global $con;
		$stmt = $con->prepare("INSERT INTO gcm_messages(message, gcm_regid, data, created_at) VALUES(?, ?, ?, NOW())");
		$stmt->execute(array($message, $gcm_regid, $data));
		$id = $con->lastInsertId();
		return $id;
	}

	function storeData($id, $data) {
		global $con;
		$result = mysql_query("INSERT INTO gcm_data(id, data) VALUES($id, '$data')");
		$stmt = $con->prepare("INSERT INTO gcm_data(id, data) VALUES(?, ?)");
		$stmt->execute(array($id, $data));
	}

	function getAllUsers() {
		global $con;
		$stmt = $con->prepare("select * FROM gcm_users order by name, created_at");
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	function getImg($user_id, $id) {
		global $con;
		$img = "";
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
		$sql = "select message,created_at,id FROM gcm_messages where gcm_regid = ? order by created_at desc";
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
		$sql = "select user_id, hash from users where username = ?";
		$stmt = $con->prepare($sql);
		$stmt->execute(array($username));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rows as $row) {
			$matchrow = $row;
		}
		return $matchrow;
	}
 
	function storeUsername($username, $hash) {
		global $con;
		$stmt = $con->prepare("select * from users where username = ?");
		$stmt->execute(array($username));
		$check_rows = $stmt->rowCount();
		if ($check_rows > 0) {
			return false;
		} else {
			$stmt = $con->prepare("INSERT INTO users(username, hash, created_at) VALUES(?, ?, NOW())");
			$stmt->execute(array($username, $hash));
		}
		return true;
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
?>

<?php

	function send_notification($registration_ids, $message) {
		global $GOOGLE_API_KEY;
		$url = 'https://android.googleapis.com/gcm/send';
		$fields = array(
			'registration_ids' => $registration_ids,
			'data' => $message,
		);
		$headers = array(
			'Authorization: key=' . $GOOGLE_API_KEY,
			'Content-Type: application/json'
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Comment this for more security
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);
		if ($result === FALSE) {
			die('Curl failed: ' . curl_error($ch));
		}
		curl_close($ch);
		return $result;
	}

?>

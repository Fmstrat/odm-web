<?php

	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache');

	include 'include/config.php';
	include 'include/db.php';

	dbconnect();

	include 'include/checklogin.php';

	$regId = $_GET['regId'];
	$n=60;
	$lastMessageId = '';

	if (validateRegId($_COOKIE['user_id'], $regId)) {
		$startTime = time();
		$currentMessageId = getLastMessageID($regId);
		$lastMessageId=$currentMessageId;
		$messages = getMessages($regId, $n);
		echo 'id: ',$currentMessageId,PHP_EOL;
		echo 'data: ',json_encode($messages),"\n";
		echo PHP_EOL;
		ob_flush();
		flush();
		while(true){
			if ((time()-$startTime)>600) die();
			$currentMessageId = getLastMessageID($regId);
			if($lastMessageId<$currentMessageId) {
				$messages = getMessagesId($regId, $lastMessageId, $n);
				echo 'id: ',$currentMessageId,PHP_EOL;
				echo 'data: ',json_encode($messages),"\n";
				echo PHP_EOL;
				ob_flush();
				flush();
				$lastMessageId=$currentMessageId;
			}
			sleep(1);
		}
	}

	dbclose();
?>
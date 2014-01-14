<?php
	include 'include/config.php';
	include 'include/db.php';
	dbconnect();

	include 'include/checklogin.php';
	include 'include/version.php';

	function getBetween($content,$start,$end){
		$r = explode($start, $content);
		if (isset($r[1])) {
			$r = explode($end, $r[1]);
			return $r[0];
		}
		return '';
	}

	$url = 'https://raw.github.com/Fmstrat/odm-web/master/odm/include/version.php';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.0; S60/3.0 NokiaN73-1/2.0(2.0617.0.0.7) Profile/MIDP-2.0 Configuration/CLDC-1.1)");
	$result = curl_exec($ch);
	if ($result === FALSE) {
		die('Curl failed: ' . curl_error($ch));
	}
	curl_close($ch);
	$cur_version = getBetween($result,'<?php $version = ','; ?>');
	if ((int) $cur_version > (int) $version)
		echo "true";
	else
		echo "false";

	dbclose();

?>

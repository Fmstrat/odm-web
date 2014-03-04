<?php 
//INIT Language subsys
if (!function_exists("gettext")){
	die("gettext module missing!");
}
global $AVAILABLE_LANGUAGES;
$AVAILABLE_LANGUAGES = array();
$dh = opendir($APP_BASE_PATH . "/language/");
if($dh !== FALSE) {
	while (($entry = readdir($dh))!==false){
		//$langcode = str_ireplace(".UTF-8", "", $entry); // remove UTF8
		if (is_dir($APP_BASE_PATH ."/language/".$entry."/LC_MESSAGES") && is_file($APP_BASE_PATH ."/language/".$entry."/language.txt")){
			$fh = fopen($APP_BASE_PATH ."/language/".$entry."/language.txt", "r");
			$lang_title = fgets($fh);
			fclose($fh);
			$AVAILABLE_LANGUAGES[$entry] = trim($lang_title);
		}
		
	}
}
if(isset($_GET["locale"])) {
	$locale = $_GET["locale"];
} else if(isset($_SESSION["locale"])) {
	$locale = $_SESSION["locale"];
} else {
	$locale = "en_US.UTF-8";
}

if(!isset($AVAILABLE_LANGUAGES[$locale])) { // check if language exists
	$locale = "en_US.UTF-8";
}

putenv("LANG=" . $locale);
setlocale(LC_ALL, $locale);

$domain = "translation";
bindtextdomain($domain, $APP_BASE_PATH . "/language/");
bind_textdomain_codeset($domain, "UTF-8");
textdomain($domain);
?>
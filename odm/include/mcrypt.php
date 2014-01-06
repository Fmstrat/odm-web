<?php 

	class MCrypt {
		private $iv = 'com.nowsci.odmiv';
		//private $key = 'MyEncPasswordaaa';


		function __construct() {
		}

		function formatKey($key) {
			$newkey = "";
			if (strlen($key) > 16) {
				$newkey = substr($key, 0, 16);
			} else if (strlen($key) < 16) {
				$newkey = $key;
				while (strlen($newkey) < 16) {
					$newkey .= "0";
				}
			}
			return $newkey;
		}

		function encrypt($str, $key) {
			//$key = $this->hex2bin($key);    
			$iv = $this->iv;
			$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);
			mcrypt_generic_init($td, $key, $iv);
			$encrypted = mcrypt_generic($td, $str);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			return bin2hex($encrypted);
		}

		function decrypt($code, $key) {
			//$key = $this->hex2bin($key);
			$code = $this->hex2bin($code);
			$iv = $this->iv;
			$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);
			mcrypt_generic_init($td, $key, $iv);
			$decrypted = mdecrypt_generic($td, $code);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			return utf8_encode(trim($decrypted));
		}

		protected function hex2bin($hexdata) {
			$bindata = '';
			for ($i = 0; $i < strlen($hexdata); $i += 2) {
				$bindata .= chr(hexdec(substr($hexdata, $i, 2)));
			}
			return $bindata;
		}

	}
?>

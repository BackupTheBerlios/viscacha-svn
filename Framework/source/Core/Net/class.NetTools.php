<?php
Core::loadClass('Core.Net.IDNA');

/**
 * Class to validate several types of data.
 *
 * @package		Core
 * @subpackage	Net
 * @author		Matthias Mohr
 * @since		1.0
 */
class NetTools {

	public static function normalizeHost($host) {
		$idna = new IDNA();
		$host = SystemEnvironment::toUtf8($host);
		$host = $idna->encode($host);
		return $host;
	}

	public static function fsockopen($host, $port, $timeout) {
		$host = self::normalizeHost($host);
		$fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
		return array($fp, $errno, $errstr, $host);
	}

	public static function checkMX($host) {
		if (empty($host)) {
			return false;
		}
		$host_idna = self::normalizeHost($host);
		if (SystemEnvironment::functionExists('checkdnsrr')) {
			if (checkdnsrr($host_idna, 'MX') === false) {
				return false;
			}
			else {
				return true;
			}
		}
		else {
	       @exec("nslookup -querytype=MX {$host_idna}", $output);
	       while(list($k, $line) = each($output)) {
	           # Valid records begin with host name
	           if(preg_match("~^(".preg_quote($host)."|".preg_quote($host_idna).")~i", $line)) {
	               return true;
	           }
	       }
	       return false;
	   }
	}

}
?>
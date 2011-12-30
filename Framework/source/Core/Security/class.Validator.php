<?php
Core::loadClass('Core.Net.NetTools');
Core::loadClass('Core.Net.IPv4');

/**
 * Class to validate several types of data.
 *
 * Some parts of this code are from Zend Framework 1.5!
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since		1.0
 */
class Validator {
	/**
	 * Fehlermeldung (only for checkAll() / checkRequest())
	 */
	const MESSAGE = 1;
	/**
	 * Minimale Länge
	 */
	const MIN_LENGTH = 2;
	/**
	 * Maximale Länge
	 */
	const MAX_LENGTH = 3;
	/**
	 * Regulärer Ausdruck
	 */
	const REGEXP = 4;
	/**
	 * Liste mit vordefinierten Elementen, Case Sensitive
	 */
	const LIST_CS = 5;
	/**
	 * Liste mit vordefinierten Elementen, NOT Case Sensitive
	 */
	const LIST_CI = 5;
	/**
	 * Callback function
	 */
	const CALLBACK = 6;
	/**
	 * Minimaler Wert
	 */
	const MIN_VALUE = 7;
	/**
	 * Maximaler Wert
	 */
	const MAX_VALUE = 8;
	/**
	 * Typ der Variable.
	 * @see Request::get()
	 */
	const VAR_TYPE = 9;
	/**
	 * Compares with an other element from the request data (equality)
	 */
	const COMPARE_EQUAL = 10;
	/**
	 * Declares the checks as optional: When data is given, checks have to be true, wen no data is given, checks are ignored.
	 */
	const OPTIONAL = 11;
	/**
	 * Declares multiple check with different error messages per field
	 */
	const MULTIPLE = 12;
	/**
	 * Compares for equality
	 */
	const EQUALS = 13;
	/**
	 * Compares for length
	 */
	const LENGTH = 14;
	/**
	 * Callback for anonymous functions / closures
	 */
	const CLOSURE = 15;

	/**
	 * Callback: Passwort-Prüfung
	 * @see Validator::checkPassword()
	 */
	const CB_PW = 'Validator::checkPassword';
	/**
	 * Callback: IP-Prüfung
	 * @see Validator::checkIP()
	 */
	const CB_IP = 'Validator::checkIP';
	/**
	 * Callback: URL-Prüfung
	 * @see Validator::checkURL()
	 */
	const CB_URL = 'Validator::checkURL';
	/**
	 * Callback: E-Mail-Prüfung
	 * @see Validator::checkMail()
	 */
	const CB_MAIL = 'Validator::checkMail';
	/**
	 * Callback: Only alphanumerical chars
	 * @see ctype_alnum()
	 */
	const CB_ALNUM = 'ctype_alnum';
	/**
	 * Callback: Only alphabtical chars
	 * @see ctype_alpha()
	 */
	const CB_ALPHA = 'ctype_alpha';
	/**
	 * Callback: Only digits
	 * @see ctype_digit()
	 */
	const CB_NUM = 'ctype_digit';

	/**
	 * Regular Expression: Checks for unsigned id (int > 0)
	 */
	const RE_ID = '~^[1-9]{1}[\d]*$~';


	public static function checkRequest($options) {
		$data = array();
		foreach ($options as $key => $o) {
			$type = VAR_NONE;
			if (isset($o[Validator::VAR_TYPE])) {
				$type = $o[Validator::VAR_TYPE];
			}
			$data[$key] = Request::get($key, $type);
		}
		return array(
			'error' => self::checkAll($data, $options),
			'data' => $data
		);
	}

	public static function checkAll($data, $allOptions) {
		$error = array();
		foreach ($allOptions as $key => $options) {
			if (!isset($data[$key])) {
				$data[$key] = null;
			}

			$checks = array();
			if (isset($options[Validator::MULTIPLE])) {
				$checks = $options[Validator::MULTIPLE];
			}
			else {
				$checks = array($options);
			}

			foreach ($checks as $o) {
				if (self::check($data[$key], $o) == false) {
					if (isset($o[Validator::MESSAGE])) {
						$error[$key] = $o[Validator::MESSAGE];
					}
					else {
						$error[$key] = "Das Feld '{$key}' entspricht leider nicht den Vorgaben.";
					}
				}
			}
		}
		return $error;
	}

	public static function check($value, $options) {
		// Check the optional part before to safe time
		if (isset($options[Validator::OPTIONAL]) && $options[Validator::OPTIONAL] == true && empty($value)) {
			return true;
		}

		foreach ($options as $type => $option) {
			$return = true;
			switch ($type) {
				case Validator::CALLBACK:
					if (is_string($option) && strpos($option, '::') !== false) {
						$option = explode('::', $option);
					}
					$return = call_user_func($option, $value);
				break;
				case Validator::COMPARE_EQUAL:
					$vtype = VAR_NONE;
					if (isset($options[Validator::VAR_TYPE])) {
						$vtype = $options[Validator::VAR_TYPE];
					}
					$query = Request::get($option, $vtype);
					$return = ($value == $query);
				break;
				case Validator::LIST_CI:
					$option = array_map('strtolower', $option);
					$value = strtolower($value);
					$return = in_array($value, $option);
				break;
				case Validator::LIST_CS:
					$return = in_array($value, $option);
				break;
				case Validator::MAX_LENGTH:
					$len = strlen($value);
					$return = ($len <= $option);
				break;
				case Validator::MIN_LENGTH:
					$len = strlen($value);
					$return = ($len >= $option);
				break;
				case Validator::LENGTH:
					$len = strlen($value);
					$return = ($len == $option);
				break;
				case Validator::MAX_VALUE:
					$return = ($value <= $option);
				break;
				case Validator::MIN_VALUE:
					$return = ($value >= $option);
				break;
				case Validator::EQUALS:
					$return = ($value == $option);
				break;
				case Validator::REGEXP:
					$match = preg_match($option, $value);
					$return = ($match > 0);
				break;
				case Validator::CLOSURE:
					$return = $option($value);
				break;
				// Don't know what you want me to do?! (VAR_TYPE, MESSAGE, ...)
			}
			if ($return == false) { // Something is wrong
				return false;
			}
		}
		return true; // Everything is ok
	}

	/**
	 * Checks whether a password is strong (true) enough or not (false).
	 *
	 * @param string $pw Password
	 * @return boolean
	 */
	public static function checkPassword($pw) {
		$pwo = Core::getObject('Core.Security.Password');
		$quality = $pwo->check($pw);
		$min_quality = Config::get('security.pwcheck');
		return ($quality >= $min_quality);
	}

	/**
	 * Verify the syntax of the given URL.
	 *
	 * @param string URL to validate
	 * @return boolean true if the url is valid
	 **/
	public static function checkURL($url) {
		if (preg_match("~^https?://([^:@/]+:[^:@/]+@)?([^@:/]+\.[a-z]{2,6})(:\d+)?/?([a-zA-Z0-9\-\.:_\?\,;/\\\+&%\$#\=\~\[\]]*)?$~i", $url, $parts)) {
			if (empty($parts[2]) || !self::checkHostname($parts[2])) {
				return false;
			}
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks whether a host name is valid.
	 *
	 * @param string $host Host name
	 * @return boolean
	 */
	public static function checkHostname($host) {
		$host_idna = NetTools::normalizeHost($host);
		$parts = explode('.', $host_idna);
		$tld = array_pop($parts);
		if (!self::checkTLD($tld)) {
			return false;
		}
		foreach ($parts as $part) {
			if (!preg_match('~^[a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1}$~i', $part)) {
				return false;
			}
		}
		return true;
	}


	/**
	 * Checks whether an e-mail is valid or not.
	 *
	 * If you set the second parameter to true, the MX record of the domain will
	 * be checked.
	 *
	 * @param string Email
	 * @param boolean Check MX Record (default is null to get default setting)
	 * @return boolean true if the email address is valid
	 **/
	public static function checkMail($email, $check_mx = null) {
		if (!preg_match('/^(.+)@([^@]+)$/', $email, $matches)) {
			return false;
		}
		else {
			$local = $matches[1];
			// Dot-atom characters are: 1*atext *("." 1*atext)
			// atext: ALPHA / DIGIT / and "!", "#", "$", "%", "&", "'", "*", "-", "/", "=", "?", "^", "_", "`", "{", "|", "}", "~"
			$atext = 'a-zA-Z0-9\x21\x23\x24\x25\x26\x27\x2a\x2b\x2d\x2f\x3d\x3f\x5e\x5f\x60\x7b\x7c\x7d';
			if (!preg_match('/^['.$atext.']+(\x2e+['.$atext.']+)*$/', $local)) {
				return false;
			}

			$host  = $matches[2];
			if (!self::checkHostname($host)) {
				return false;
			}
			// Check MX record
			if ($check_mx === null) {
				$check_mx = Config::get('mail.check_mx');
			}
			if ($check_mx) {
				return NetTools::checkMX($host);
			}
			else {
				return true;
			}
		}
	}

	/**
	 * Validate the syntax of the given IPv4 adress.
	 *
	 * @param string IPv4 adress
	 * @return boolean true if syntax is valid, otherwise false
	 * @see	IPv4::check()
	 **/
	public static function checkIP($ip) {
		return IPv4::check($ip);
	}

	/**
	 * Checks whether the host name ends with a valid top level domain or checks the validity of the given tld.
	 *
	 * @param string $host Host name or TLD
	 * @return boolean
	 */
	private static function checkTLD($host) {
		if (strpos($host, '.') !== false) {
			$parts = explode('.', $host);
			$tld = strtolower(end($parts));
		}
		else {
			$tld = strtolower($host);
		}

		return (preg_match('~^[a-z]{2,}$~i', $tld) > 0); // New style for ICANN plans (custom tlds for companys, cities etc.)
	}

}
?>

<?php
/**
 * Class to secure content of variables (and vice-versa).
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */
class Sanitize {

	public static function save($value, $type, $standard = null) {
		$var = $standard;
		if ($value !== null) {
			if ($type == VAR_DB || $type == VAR_ARR_DB) {
				$var = Sanitize::saveDb($value);
			}
			elseif ($type == VAR_HTML || $type == VAR_ARR_HTML) {
				$var = Sanitize::saveHTML($value);
			}
			elseif ($type == VAR_INT || $type == VAR_ARR_INT) {
				$var = Sanitize::saveInt($value);
			}
			elseif ($type == VAR_ALNUM || $type == VAR_ARR_ALNUM) {
				$var = Sanitize::saveAlNum($value, true);
			}
			elseif ($type == VAR_URI || $type == VAR_ARR_URI) {
				$var = Sanitize::saveAlNum($value, false);
			}
			else {
				$var = Sanitize::removeNullByte($value);
			}
		}
		else {
			if ($standard === null) {
				if ($type == VAR_DB || $type == VAR_ALNUM || $type == VAR_HTML || $type == VAR_URI) {
					$var = '';
				}
				elseif ($type == VAR_INT) {
					$var = 0;
				}
				elseif ($type == VAR_ARR_INT || $type == VAR_ARR_DB || $type == VAR_ARR_ALNUM || $type == VAR_ARR_NONE || $type == VAR_ARR_HTML || $type == VAR_ARR_URI) {
					$var = array();
				}
				else {
					$var = null;
				}
			}
		}
		return $var;
	}

	/**
	 * Escapces (database specific) chars in a string and removes null bytes.
	 *
	 * @param string Variable to check
	 * @return string Checked variable
	 **/
	public static function saveDb($var) {
		if (is_array($var)) {
			foreach ($var as $key => $value) {
				$var[$key] = self::saveDb($value);
			}
		}
		else {
			$var = self::removeNullByte($var);
			if (Core::_(DB) !== null) {
				$var = Core::_(DB)->escapeString($var);
			}
			else {
				$var = addslashes($var);
			}
		}
		return $var;
	}

	/**
	 * Validates a string. Removes all characters that are not alphanumeric (A-Z, 0-9, _, -).
	 *
	 * When specifying $strict = true, "-" and "_" are not allowed
	 *
	 * @param string Variable to check
	 * @return string Checked variable
	 **/
	public static function saveAlNum($var, $strict = false) {
		if (is_array($var)) {
			foreach ($var as $key => $value) {
				$var[$key] = self::saveAlNum($value);
			}
		}
		else {
			$var = preg_replace("/[^\w\d\-]+/", '', $var);
		}
		return $var;
	}

	/**
	 * Validates an integer.
	 *
	 * @param int Variable to check
	 * @return int Checked variable
	 **/
	public static function saveInt($var) {
		if (is_array($var)) {
			foreach ($var as $key => $value) {
				$var[$key] = self::saveInt($value);
			}
		}
		else {
			$var = intval(trim($var));
		}
		return $var;
	}


	/**
	 * Applies htmlentities and removes null bytes.
	 *
	 * @param string Variable to check
	 * @return string Checked variable
	 **/
	public static function saveHTML($var) {
		if (is_array($var)) {
			foreach ($var as $key => $value) {
				$var[$key] = self::saveHTML($value);
			}
		}
		else {
			$var = self::removeNullByte($var);
			$var = htmlentities($var, ENT_QUOTES, Config::get('intl.charset'));
		}
		return $var;
	}

	/**
	 * Removes all null bytes ( \0 ).
	 *
	 * @param mixed Variable to check
	 * @return mixed Checked variable
	 **/
	public static function removeNullByte($data) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$data[$key] = self::removeNullByte($value);
			}
		}
		else {
			$data = str_replace("\0", '', $data);
		}
		return $data;
	}

}


?>

<?php
/**
 * Hash helper.
 *
 * @package		Core
 * @subpackage	Security
 * @author		Matthias Mohr
 * @since 		1.0
 */
class Hash {

	/**
	 * Generates a salted hash configured by the standard config file.
	 *
	 * @param string String to hash
	 * @return Hashed string
	 */
	public static function generate($str) {
		$str += Config::get('security.hashsalt');
		return hash(Config::get('security.hashalgo'), $str);
	}

	/**
	 * Generates a random hash with a length of 32 chars.
	 *
	 * @return Hash
	 */
	public static function getRandom() {
		return md5(uniqid('', true));
	}

}

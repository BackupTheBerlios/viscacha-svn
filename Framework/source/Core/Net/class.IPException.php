<?php
Core::loadClass('Core.ExceptionData');

/**
 * Exception for IP error.
 *
 * @package		Core
 * @subpackage	DB
 * @author		Matthias Mohr
 * @since 		1.0
 */
class IPException extends ExceptionData {

	/**
	 * Constructs the QueryException.
	 *
	 * @param string Database error message
	 * @param int Database error number (default: 0)
	 */
	public function __construct() {
		parent::__construct('No valid IPv4 found for the user.', 1);
	}

}
?>
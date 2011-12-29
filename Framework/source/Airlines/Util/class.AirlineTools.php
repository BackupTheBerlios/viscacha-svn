<?php
/**
 * Helper
 *
 * @package		Airlines
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */

class AirlineTools {

	public static function buildUri($data) {
		return URI::build('airlines/airlines/' . $data['id'] . '-' . URI::clean($data['name']));
	}

}
?>
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

	public static function buildUri($id, $name, $raw = false) {
		$uri = 'airlines/airlines/' . $id . '-' . URI::clean($name);
		if (!$raw) {
			$uri = URI::build($uri);
		}
		return $uri;
	}

}
?>
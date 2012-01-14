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

	public static function buildUri(CustomData $data, $raw = false) {
		$uri = 'airlines/airlines/' . $data->getId() . '-' . URI::clean($data->getData('name'));
		if (!$raw) {
			$uri = URI::build($uri);
		}
		return $uri;
	}

}
?>
<?php
/**
 * Class to validate several types of data.
 *
 * @package		Core
 * @subpackage	Net
 * @author		Matthias Mohr
 * @since		1.0
 */
class URI {

	public static function build($uri, $host = false) {
		$uri = Request::getObject()->normalizeURI($uri);
		$root = Config::get("general.url");
		if ($host == false) {
			$root = parse_url($root, PHP_URL_PATH);
		}
		return $root . $uri;
	}

	public static function frontPage() {
		return self::build('');
	}

}
?>
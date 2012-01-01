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

	public static function clean($url, $toLower = true, $spacer = '-') {
		if ($toLower == true) {
			$url = strtolower($url);
		}

		// International umlauts
		$url = str_replace (array('á', 'à', 'â', 'Á', 'À', 'Â'),			'a', $url);
		$url = str_replace (array('ç', 'Ç'), 								'c', $url);
		$url = str_replace (array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ë', 'Ê'),	'e', $url);
		$url = str_replace (array('í', 'ì', 'î', 'ï', 'Í', 'Ì', 'Î', 'Ï'),	'i', $url);
		$url = str_replace (array('ó', 'ò', 'ô', 'Ó', 'Ò', 'Ô'), 			'o', $url);
		$url = str_replace (array('ú', 'ù', 'û', 'Ú', 'Ù', 'Û'), 			'u', $url);
		// German umlauts
		$url = str_replace (array('ä', 'Ä'), 'ae', $url);
		$url = str_replace (array('ö', 'Ö'), 'oe', $url);
		$url = str_replace (array('ü', 'Ü'), 'ue', $url);
		$url = str_replace (array('ß'), 'ss', $url);
		// Replace some special chars with delimiter
		$url = preg_replace('/[\+\s\r\n\t]+/', $spacer, $url);
		// Replace multiple delimiter chars with only one char
		$url = preg_replace('/['.preg_quote($spacer, '/').']+/', $spacer, $url);
		// Remove html and other special chars
		$url = preg_replace(array('/<[^>]*>/', '/[^a-z0-9\-\._'.preg_quote($spacer, '/').']/i'), '', $url);

		return $url;
	}

	public static function build($uri, $host = false) {
		$uri = Request::getObject()->normalizeURI($uri);
		if (strpos($uri, '://') === false) {
			$root = Config::get("general.url");
			if ($host == false) {
				$root = parse_url($root, PHP_URL_PATH);
			}
			return $root . $uri;
		}
		else {
			return $uri;
		}
	}

	public static function frontPage() {
		return self::build('');
	}

}
?>
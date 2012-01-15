<?php
/**
 * Manages system environment variables etc.
 *
 * @package		Core
 * @subpackage 	System
 * @author		Matthias Mohr
 * @since 		1.0
 */
class SystemEnvironment {

	/**
	 * Tells whether the operating system is Windows (true) or not (false).
	 * @var boolean
	 */
	private static $windows;

	/**
	 * Constructs the SystemEnvironment object and does some initial work.
	 */
	public function __construct() {
		if (empty($_SERVER['DOCUMENT_ROOT']) == true) {
			$_SERVER['DOCUMENT_ROOT'] = $this->documentRoot();
		}
	}

	/**
	 * Returns the DOCUMENT_ROOT.
	 *
	 * This function fixes a problem with Windows where PHP does not have $_SERVER['DOCUMENT_ROOT']
	 * built in. getDocumentRoot returns what $_SERVER['DOCUMENT_ROOT'] should have. It should work on
	 * other builds, such as Unix, but is best used with Windows.
	 *
	 * @author Allan Bogh <Buckwheat469@hotmail.com>
	 * @return string The document root path
	 **/
	private function documentRoot(){
		//sets up the localpath
		$localpath = getenv("SCRIPT_NAME");
	 	$localpath = substr($localpath, strpos($localpath, '/', iif(strlen($localpath) >= 1, 1, 0)), strlen($localpath));

		//realpath sometimes doesn't work, but gets the full path of the file
		$absolutepath = realpath($localpath);
		if((isset($absolutepath) == false || $absolutepath == "") && isset($_SERVER['ORIG_PATH_TRANSLATED']) == true) {
			$absolutepath = $_SERVER['ORIG_PATH_TRANSLATED'];
		}

		//checks if Windows is being used to replace the \ to /
		if(self::isWindows() == true) {
			$absolutepath = str_replace("\\","/", $absolutepath);
		}

		//prepares the document root string
		$docroot = substr($absolutepath, 0, strpos($absolutepath, $localpath));

		return $docroot;
	}

	/**
	 * Tells whether the operating system is Windows (true) or not (false).
	 *
	 * @return boolean Returns true if the OS seems to be Windows.
	 */
	public static function isWindows() {
		if (is_bool(self::$windows) == false) {
			if (defined('OS_WINDOWS') == true) {
				self::$windows = true;
			}
			elseif (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
				self::$windows = true;
			}
			elseif (isset($_SERVER['OS']) == true && stripos($_SERVER['OS'],'Windows') !== false) {
				self::$windows = true;
			}
			elseif (function_exists('php_uname') == true && stripos(@php_uname('s'), 'Windows') !== false) {
				self::$windows = true;
			}
			else {
				self::$windows = false;
			}
		}
		return self::$windows;
	}

	/**
	 * Checks the list of defined functions, both built-in and user-defined, for the function name.
	 *
	 * A function name may exist even if the function itself is unusable due to configuration or compiling options.
	 * This implementaion fixes problems with suhosin blacklist.
	 *
	 * @see function_exists()
	 * @return boolean
	 */
	public static function functionExists($func) {
		if (extension_loaded('suhosin')) {
			$suhosin = @ini_get("suhosin.executor.func.blacklist");
			if (empty($suhosin) == false) {
				$suhosin = explode(',', $suhosin);
				$suhosin = array_map('trim', $suhosin);
				$suhosin = array_map('strtolower', $suhosin);
				return (function_exists($func) == true && array_search($func, $suhosin) === false);
			}
		}
		return function_exists($func);
	}

	public static function fromUtf8($string) {
		if (SystemEnvironment::functionExists('mb_convert_encoding')) {
			$string = mb_convert_encoding($string, Config::get('intl.charset'), 'UTF-8');
		}
		else if (SystemEnvironment::functionExists('iconv')) {
			$string = iconv('UTF-8', Config::get('intl.charset'), $string);
		}
		else {
			$string = utf8_decode($string);
		}
		return $string;
	}

	public static function toUtf8($string) {
		if (SystemEnvironment::functionExists('mb_convert_encoding')) {
			$string = mb_convert_encoding($string, 'UTF-8', Config::get('intl.charset'));
		}
		else if (SystemEnvironment::functionExists('iconv')) {
			$string = iconv(Config::get('intl.charset'), 'UTF-8', $string);
		}
		else {
			$string = utf8_encode($string);
		}
		return $string;
	}
}
?>

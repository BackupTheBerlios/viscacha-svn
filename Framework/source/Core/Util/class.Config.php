<?php
/**
 * Config-Class manages the configuration data that is saved on the filesystem.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 * @static
 */
class Config {

	/**
	 * Contains all config data.
	 * @var array
	 */
	private static $data = null;
	/**
	 * Contains temporary config data.
	 * @var array
	 */
	private static $temp = array();
	/**
	 * Contains changed config data.
	 * Each change is an array with key, value and type.
	 * @var array
	 */
	private static $changes = array();
	/**
	 * Contains the relative path to the config file.
	 * @var string
	 */
	const FILENAME = './data/config.php';

	/**
	 * Returns the value for the configurwation key specified as parameter.
	 *
	 * The group is specified before the dot, the key is specfied after the dot.
	 * Examples: mail.check_mx, general.email, general.url, core.version, ...
	 *
	 * This function returns null if the key is not found and additionally, a notice will be thrown.
	 *
	 * @param string Config key in the format Group.Key
	 * @return mixed Value of the specified key
	 **/
	public static function get($key){
		if (self::$data == null) {
			self::baseConfig();
		}
		if (isset(self::$data[$key]) == true) {
			return self::$data[$key];
		}
		else {
			Core::throwError("Config::get() - Value for specified key not found.", INTERNAL_NOTICE);
			return null;
		}
	}

	/**
	 * Updates the value for the configuration key specified as parameter.
	 *
	 * If the third parameter is null (it is the default value) the script tries to get
	 * the value by name (without group!) from the input data (query string). If no data is found
	 * the default value from Variables::get() for the specified type will be used.
	 *
	 * This function throws a notice if the key is not found.
	 *
	 * The data won't be saved by this function! You have to call Config::save() for this!
	 *
	 * @see Variables::get()
	 * @see Config::save()
	 * @param string Config key in the format Group.Key
     * @param int Type of variable (one of the standard types that are accepted by Request::get().
     * @param mixed Value to change to specified config key.
	 **/
	public static function set($key, $type = VAR_HTML, $value = null) {
		if (self::$data == null) {
			self::baseConfig();
		}
		list($sgroup, $name) = explode('.', $key, 2);
		if ($value == null) {
			$value = Request::get($name, $type);
		}
		if (isset(self::$data[$key]) == true) {
			self::$data[$key] = $value;
			if ($type == VAR_INT || $type == VAR_ARR_INT) {
				$value = Sanitize::saveInt($value);
			}
			self::$changes[$key] = array(
				'key' => $key,
				'value' => $value,
				'type' => $type
			);
		}
		else {
			Core::throwError("Config::set() - Specified key does not exist.");
		}
	}

	/**
	 * Saves the changes to the filesystem.
	 **/
	public static function save() {
		$changeBase = count(array_intersect_key(self::$changes, self::$data));
		if ($changeBase > 0) {
			$file = Core::constructObject('Core.FileSystem.FileArray', new File(self::FILENAME));
			$array = $file->get();
		}
		foreach (self::$changes as $elem) {
			if (isset($array) == true && isset(self::$data[$elem['key']]) == true) {
				$array[$elem['key']] = $elem['value'];
			}
		}
		if (isset($file) == true) {
			$file->save($array);
		}
	}

	/**
	 * Returns the temporary config element for the specified key.
	 *
	 * The temporary config data is empty at the beginning.
	 * You have to set elements before you can get elements from this function.
	 * There are no restriction concerning the key (unlike for the normal config data).
	 *
	 * If there is no config data with the specified key NULL will be returned.
	 *
	 * @param string Key of the element
	 */
	public static function getTemp($key) {
		if (isset(self::$temp[$key]) == true) {
			return self::$temp[$key];
		}
		else {
			return null;
		}
	}

	/**
	 * Returns the temporary config element for the specified key.
	 *
	 * The temporary config data is empty at the beginning.
	 * You have to set elements before you can get elements from this function.
	 * There are no restriction concerning the key (unlike for the normal config data),
	 * but it is recommended to use only strings that contain alphabetic and/or numerical characters.
	 * If there is already a key with this name it will be overwritten.
	 *
	 * @param string Key of the element
	 */
	public static function setTemp($key, $value) {
		self::$temp[$key] = $value;
	}

	/**
	 * Returns an array containing the required config data.
	 *
	 * If the setting core.installed is not equal 1 an error will be thrown.
	 *
	 * @return array Configuration from the file specified in the constant Config::FILENAME.
	 **/
	public static function baseConfig() {
		if (is_array(self::$data) == false) {
			require(self::FILENAME);
			self::$data = $config;
		}
		return self::$data;
	}

}

?>

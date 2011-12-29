<?php
/**
 * Critical Error (High priority). Value: 8
 */
define('INTERNAL_ERROR', 8);
/**
 * Warning (Medium priority). Value: 4
 */
define('INTERNAL_WARNING', 4);
/**
 * Notice (Low priority). Value: 2
 */
define('INTERNAL_NOTICE', 2);
/**
 * Debug information (Very low priority; just for logfiles). Value: 0
 */
define('INTERNAL_DEBUG', 0);


define('VAR_NONE', 0);
define('VAR_HTML', 1);
define('VAR_DB', 2);
define('VAR_ALNUM', 3);
define('VAR_INT', 4);
define('VAR_URI', 5);

define('VAR_ARR_NONE', 100);
define('VAR_ARR_HTML', 101);
define('VAR_ARR_DB', 102);
define('VAR_ARR_ALNUM', 103);
define('VAR_ARR_INT', 104);
define('VAR_ARR_URI', 105);


/**
 * This is the static core class with core functions.
 *
 * @package		Core
 * @author		Matthias Mohr
 * @since 		1.0
 */
class Core {

	/**
	 * Saves the instances of objects
	 *
	 * @var array
	 * @static
	 */
	private static $instances = array();
	/**
	 * Saves the stored instances of objects
	 *
	 * @var array
	 * @static
	 */
	private static $nInstances = array();

	public static function route() {
		$class = Request::getObject()->getRequestedClass();
		$module = Core::constructObject($class);
		if ($module !== null) {
			$module->route();
		}
		else {
			self::throwError("Could not instantiate object of type '{$class}' for this route.");
		}
	}

	/**
	 * Easy access wrapper for Core::getNObject().
	 *
	 * @param string $objId Name of the stored object
	 * @see Core::getNObject()
	 */
	public static function _($objId) {
		return self::getNObject($objId);
	}

	/**
	 * Gets an instance of an object with the stored name passed as parameter.
	 *
	 * This method loads a stored Object.
	 * This object has to be stored before with storeObject().
	 * On failure null will be returned and a warning will be thrown.
	 * The parameter is case sensitive.
	 *
	 * Alternative: You can directly use the objects via Core::$_DB (in this case the parameter is 'DB')
	 *
	 * @param 	string Stored name of object
	 * @return 	object Returns the object or null on failure
	 */
	public static function getNObject($id) {
		if (isset(self::$nInstances[$id]) && is_object(self::$nInstances[$id])) {
			return self::$nInstances[$id];
		}
		else {
			self::throwError("Stored instance of object with name '{$id}' not found.");
			return null;
		}

	}

	/**
	 * Stores an object with an specified name.
	 *
	 * If no name or no string as second argument is specified the class name is used instead.
	 * If a stored object with the specified name is existant it will be overwritten.
	 * The second parameter is case sensitive.
	 *
	 * The object name will be declared as a constant, so it is not possible to use names that are already used as constant.
	 *
	 * @see 	Core::constructObject()
	 * @param	object	Object to store
	 * @param	string	Name for the object
	 */
	public static function storeNObject($object, $name = null) {
		if (is_string($name) == false) {
			$name = get_class($object);
		}
		self::$nInstances[$name] = $object;
		$upper = strtoupper($name);
		if (!defined($upper)) {
			define($upper, $name);
		}
	}

	/**
	 * Deletes an object with an specified name.
	 *
	 * If thee specified name is not existant nothing will happen.
	 * The parameter is case sensitive.
	 *
	 * @param	string	Stored name of an object
	 */
	public static function unsetNObject($name) {
		if (isset(self::$nInstances[$name]) == true) {
			unset(self::$nInstances[$name]);
		}
	}

	/**
	 * Gets an instance of an object with the class name passed as parameter from the internal object array.
	 *
	 * If no instance is constructed yet, a new instance will be created and stored to the internal object array.
	 * To create a new instance the class is loaded from the source folder. Null will be returned on failure.
	 * The parameter is case sensitive. For the format of the package name see Core::constructObject().
	 *
	 * @see Core::constructObject()
	 * @param string Name of package
	 * @return object Returns the object or null on failure
	 */
	public static function getObject($objectName) {
		$parts = explode('.', $objectName);
		$className = array_pop($parts);
		if (!isset(self::$instances[$objectName]) || !is_object(self::$instances[$objectName])) {
			self::loadClass($objectName);
			if (class_exists($className) == true) {
				self::$instances[$objectName] = new $className();
			}
			else {
				self::throwError("Can not find class with name '{$className}'");
				return null;
			}
		}
		return self::$instances[$objectName];
	}

	/**
	 * Gets a new instance of an object with the class name passed as parameter.
	 *
	 * To create a new instance the class is loaded from the source folder.
	 * Null will be returned on failure.
	 *
	 * Example names of classes:<br />
	 * - Core.Core is this class<br />
	 * - Core.System.ClassManager is the ClassManager class in Viscacha/System/class.ClassManager.php<br />
	 * The package names are case sensitive!
	 *
	 * @param string $objectName Name of package
	 * @param mixed First parameter for the constructor of the object. (Optional)
	 * @param mixed Second parameter for the constructor of the object. (Optional)
	 * @param mixed Third parameter for the constructor of the object. (Optional)
	 * @param mixed n-th parameter for the constructor of the object. (Optional)
	 * @return object Returns the object or null on failure
	 * @todo Replace eval variant with call_user_func_array().
	 */
	public static function constructObject() {
		$numArgs = func_num_args();
		if ($numArgs == 0) {
			Core::throwError("Missing argument 1 for Core::constructObject()");
		}

		$objectName = func_get_arg(0);
		$parts = explode('.', $objectName);
		$className = array_pop($parts);
		self::loadClass($objectName);

		if (class_exists($className) == true) {
			if ($numArgs == 1) {
				return new $className();
			}
			elseif ($numArgs == 2) { // Just a bit faster then the eval shit...
				return new $className(func_get_arg(1));
			}
			else {
				$argString = array();
				$argList = func_get_args();
				for ($x=1; $x < $numArgs; $x++) {
					$argString[] = '$argList['.$x.']';
				}
				$argString = implode(',', $argString);
				return eval("return new {$className}({$argString});");
			}
		}
		else {
			self::throwError("Can not find class with name '{$className}'");
			return null;
		}
	}

	public static function constructObjectArray(array $objectNames) {
		$objects = array();
		foreach ($objectNames as $name) {
			$objects[$name] = self::constructObject($name);
		}
		return $objects;
	}

	/**
	 * Updates or sets an instance of an object to the internal object array.
	 *
	 * On failure ($objectName is invalid or $object is not an object) the object will not be
	 * set and the old will be kept. Furthermore a warning will be thrown.
	 * The first parameter is case sensitive. For the format of the package name see Core::constructObject().
	 *
	 * @see		Core::constructObject()
	 * @param 	string Name of package
	 * @param 	object Object to add
	 **/
	public static function updateObject($objectName, $object){
		if (is_object($object) == false) {
			self::throwError("Argument #2 of method updateObject has to be an object.");
		}
		else {
			if (self::classExists($objectName) === false) {
				self::throwError("Argument #1 of method updateObject is an invalid Package Name.");
			}
			else {
				self::$instances[$objectName] = $object;
			}
		}
	}

	/**
	 * Checks if a class for the specified package name exists.
	 *
	 * If the class exists the path will be returned.
	 * If the class does not exists boolean FALSE will be returned.
	 * The parameter is case sensitive. For the format of the package name see Core::constructObject().
	 *
	 * @see 	Core::constructObject()
	 * @param	string Name of package
	 * @return 	mixed  Path to class of FALSE.
	 **/
	public static function classExists($class){
		$classData = explode('.', $class);
		array_unshift($classData, 'source');
		$className = array_pop($classData);
		$folders = implode('/', $classData);
		$file = "{$folders}/class.{$className}.php";
		if (file_exists($file) == true) {
			return $file;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks if a interface for the specified interface name exists.
	 *
	 * If the interface exists the path will be returned.
	 * If the interface does not exists boolean FALSE will be returned.
	 * The parameter is case sensitive. For the format of the package name see Core::loadInterface().
	 *
	 * @see 	Core::loadInterface()
	 * @param 	string Name of package
	 * @return 	mixed  Path to interface of FALSE.
	 **/
	public static function interfaceExists($interface){
		$interfaceData = explode('.', $interface);
		array_unshift($interfaceData, 'source');
		$interfaceName = array_pop($interfaceData);
		$folders = implode('/', $interfaceData);
		$file = "{$folders}/interface.{$interfaceName}.php";
		if (file_exists($file) == true) {
			return $file;
		}
		else {
			return false;
		}
	}

	/**
	 * Loads a class from the filesystem that can be used afterwards.
	 *
	 * On failure the object won't be included and a warning will be thrown.
	 * The parameter is case sensitive. For the format of the package name see Core::constructObject().
	 *
	 * @see 	Core::constructObject()
	 * @param 	string Name of package
	 **/
	public static function loadClass($class){
		$file = self::classExists($class);
		if ($file == false) {
			self::throwError("Can not find class source file '{$class}'");
		}
		else {
			include_once($file);
			if (class_exists(self::getLastName($class)) == false) {
				self::throwError("Included source file does not contain a class with the name '{$class}'", INTERNAL_ERROR);
			}
		}
	}

	/**
	 * Loads a interface from the filesystem that can be used afterwards.
	 *
	 * On failure the interface will not be loaded and the script will be aborted with an INTERNAL_ERROR.
	 *
	 * Example names of interfaces:<br />
	 * - Viscacha.DB.DBAL is the interface DBAL in the directory DB in the Package Viscacha.<br />
	 * - Viscacha.Cache.CacheObject is the interface CacheObject in the directory Cache in the Package Viscacha.<br />
	 * The interface names are case sensitive!
	 *
	 * @param string Name of interface
	 **/
	public static function loadInterface($interface){
		$file = self::interfaceExists($interface);
		if ($file == false) {
			self::throwError("Can not find interface source file '{$interface}' at {$file}", INTERNAL_ERROR);
		}
		else {
			require_once($file);
			if (interface_exists(self::getLastName($interface)) == false) {
				self::throwError("Included source file does not contain an interface with the name '{$interface}'", INTERNAL_ERROR);
			}
		}
	}

	/**
	 * Triggers an error message.
	 *
	 * The constants INTERNAL_ERROR (8), INTERNAL_WARNING (4), INTERNAL_NOTICE (2) and INTERNAL_DEBUG (0) can be used for the second parameter.
	 * Default value for the second parameter is INTERNAL_WARNING (4).
	 * INTERNAL_DEBUG does not throw any message to the user, it only adds the message to the log file.
	 *
	 * @param string Error message
	 * @param int Error reporting level
	 **/
	public static function throwError($error, $warning = INTERNAL_WARNING){
		$line = __LINE__;
		$file = __FILE__;
		if (function_exists('debug_backtrace') == true) {
			$backtraceInfo = debug_backtrace();
			if (isset($backtraceInfo[0]) == true) {
	        	$file = $backtraceInfo[0]["file"];
	        	$line = $backtraceInfo[0]["line"];
			}
		}

		if ($warning == INTERNAL_ERROR) {
			$warning = E_USER_ERROR;
		}
		elseif ($warning == INTERNAL_NOTICE) {
			$warning = E_USER_NOTICE;
		}
		elseif ($warning == INTERNAL_DEBUG) {} // Do nothing...
		else {
			$warning = E_USER_WARNING;
		}

		if ($warning != INTERNAL_DEBUG) {
			$errorHandler = Core::getObject('Core.System.ErrorHandling');
			$errorHandler->errorHandler($warning, $error, $file, $line);
		}
		else {
			$debug = Core::getObject('Core.System.Debug');
			$debug->add($error);
		}
	}

	/**
	 * This method has to be used in all __destruct() methods which need the current working directory.
	 *
	 * @see http://bugs.php.net/bug.php?id=34206
	 * @see http://www.php.net/language.oop5.decon
	 */
	public static function destruct() {
		$dir = Config::getTemp('cwd');
		if (!empty($dir)) {
			@chdir($dir);
		}
	}

	/**
	 * Gets the class or interface name of the specified internal package name.
	 *
	 * @param string Package name
	 * @return string Name of class or interface.
	 */
	private static function getLastName($name) {
		$data = explode('.', $name);
		return array_pop($data);
	}

}
?>

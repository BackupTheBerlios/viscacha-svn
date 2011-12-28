<?php
/**
 * Request class
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */
final class Request {

	const FILENAME = './data/routes.php';

	protected $routes;
	protected $requestedClass;
	protected $originalModule;
	protected $args;

	private static $instance = NULL;
 
	public static function getObject() {
		if (self::$instance === NULL) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {
		$this->loadRoutes();
		$this->populatePathInfo();
	}

	private function __clone() {}

	public function normalizeURI($uri) {
		// Remove default package from URI
		$package = preg_quote($this->routes['DefaultPackage'], '~');
		$uri = preg_replace("~^/?{$package}(/|$)~i", '', $uri);

		// Remove default module from URI
		if (isset($this->routes['Routable'][$package])) {
			$key = array_search($this->routes['DefaultModule'], $this->routes['Routable'][$package]);
			if ($key !== null) {
				$key = preg_quote($key, '~');
				$uri = preg_replace("~^/?{$key}(/|$)~i", '', $uri);
			}
		}
		
		return $uri;
	}

	public function getRequestedClass() {
		return $this->requestedClass;
	}

	public function getOriginalModule() {
		return $this->originalModule;
	}

	public function getArg($index) {
		if (isset($this->args[$index]))
			return $this->args[$index];
		else
			return null;
	}

	public function getArgs() {
		return $this->args;
	}

	protected function loadRoutes() {
		require(self::FILENAME);
		$this->routes = $config;
		if (empty($this->routes['DefaultPackage'])) {
			Core::throwError("No default package in routing table found.", INTERNAL_ERROR);
		}
		if (empty($this->routes['DefaultModule'])) {
			Core::throwError("No default module in routing table found.", INTERNAL_ERROR);
		}
		if (empty($this->routes['Routable'])) {
			Core::throwError("No routes in routing table found.", INTERNAL_ERROR);
		}
		if (!Core::classExists($this->convertModuleToClass($this->routes['DefaultPackage'], $this->routes['DefaultModule']))) {
			Core::throwError("Default route is invalid", INTERNAL_ERROR);
		}
	}

	protected function populatePathInfo() {
		$query = array();
		if (!empty($_SERVER['PATH_INFO'])) {
			$query = explode('/', trim($_SERVER['PATH_INFO'], '/'));
		}

		$package = $this->routes['DefaultPackage'];
		$this->originalModule = $this->routes['DefaultModule'];
		$this->requestedClass = $this->convertModuleToClass($package, $this->originalModule);
		$this->args = array();

		if (count($query) > 0) {
			$name = $this->getRealPackageName($query[0]);
			if (!empty($name)) {
				$package = $name;
				array_shift($query);
			}

			if (!empty($this->routes['Routable'][$package])) {
				$this->transformPathToModule($this->routes['Routable'][$package], $package, $query);
			}

			$this->args = $query;
		}
	}

	protected function transformPathToModule($modules, $package, &$query) {
		$uriToUse = '';
		foreach ($modules as $uri => $className) {
			if (count($query) > 0) {
				if (strcasecmp($uri, $query[0]) == 0) {
					array_shift($query);
					if (is_array($className)) {
						$this->transformPathToModule($modules[$uri], $package, $query);
						return; // we are skipping this level
					}
					else {
						$uriToUse = $uri;
					}
				}
			}
		}

		if (!empty($modules[$uriToUse])) {
			$class = $this->convertModuleToClass($package, $modules[$uriToUse]);
			if (Core::classExists($class)) {
				$this->requestedClass = $class;
				$this->originalModule = $uriToUse;
			}
			else {
				Core::throwError("Routed class '{$class}' not found.");
			}
		}
	}

	protected function getRealPackageName($name) {
		foreach ($this->routes['Routable'] as $package => $modules) {
			if (strcasecmp($package, $name) == 0) {
				if (!empty($modules)) {
					return $package;
				}
				else {
					return null;
				}
			}
		}
		return null;
	}

	protected function convertModuleToClass($package, $name) {
		return "{$package}.Modules.{$name}";;
	}

	/**
	 * Returns a value from the $_REQUEST array.
	 *
	 * You have to specify the index of the $_REQUEST-array. The second parameter
	 * is the type of the variable. Default is VAR_NONE. You can specify thw
	 * following constants: VAR_NONE, VAR_DB, VAR_INT, VAR_ALNUM, VAR_ARR_NONE,
	 * VAR_ARR_STR, VAR_ARR_INT, VAR_ARR_ALNUM, VAR_HTML, VAR_ARR_HTML.
	 * With the third parameter you can specify a default value, which will be set
	 * when the $_REQUEST-array does not contain an entry with the specified index.
	 *
	 * @param string Index
	 * @param int Type of variable
	 * @param mixed Default value
	 * @return mixed Value for specified index
	 * @static
	 **/
	public static function get($index, $type = VAR_NONE, $standard = null) {
		$value = null;
		if (is_int($index)) {
			$value = Request::getObject()->getArg($index);
		}
		else if (isset($_REQUEST[$index])) {
			$value = $_REQUEST[$index];
		}

		if ($value !== null) {
			if ($type == VAR_DB || $type == VAR_ARR_DB) {
				$var = Sanitize::saveDb($value);
			}
			elseif ($type == VAR_HTML || $type == VAR_ARR_HTML) {
				$var = Sanitize::saveHTML($value);
			}
			elseif ($type == VAR_INT || $type == VAR_ARR_INT) {
				$var = Sanitize::saveInt($value);
			}
			elseif ($type == VAR_ALNUM || $type == VAR_ARR_ALNUM) {
				$var = Sanitize::saveAlNum($value, true);
			}
			elseif ($type == VAR_URI || $type == VAR_ARR_URI) {
				$var = Sanitize::saveAlNum($value, false);
			}
			else {
				$var = Sanitize::removeNullByte($value);
			}
		}
		else {
			if ($standard == null) {
				if ($type == VAR_DB || $type == VAR_ALNUM || $type == VAR_HTML || $type == VAR_URI) {
					$var = '';
				}
				elseif ($type == VAR_INT) {
					$var = 0;
				}
				elseif ($type == VAR_ARR_INT || $type == VAR_ARR_DB || $type == VAR_ARR_ALNUM || $type == VAR_ARR_NONE || $type == VAR_ARR_HTML || $type == VAR_ARR_URI) {
					$var = array();
				}
				else {
					$var = null;
				}
			}
			else {
				$var = $standard;
			}
		}
		return $var;
	}
	
}
?>
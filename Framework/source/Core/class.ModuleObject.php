<?php
/**
 * This is a general module object. All modules should extend it.
 *
 * @package		Core
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */
abstract class ModuleObject {

	/**
	 * Contains internal version number of this module.
	 * @var string
	 */
	protected $version;
	/**
	 * Contains name of this module.
	 * @var string
	 */
	protected $module;
	/**
	 * Contains name of the package.
	 * @var string
	 */
	protected $package;

	/**
	 * Constructs a new ModuleObject.
	 *
	 * Note: Before you call this constructor, you have to set the variable $module!
	 **/
	public function __construct($package) {
		$this->version = '1.0.0';
		$this->module = get_class($this);
		$this->package = $package;

		// Set the current working dir into temporary config data.
		// We need the value later in all __destruct methods because the cwd will be invalid there.
		// Set back with: Core::destruct()
		$cwd = getcwd();
		if ($cwd == false) {
			$cwd = Config::get("general.path");
		}
		Config::setTemp('cwd', $cwd);
	}

	/**
	 * Destructs the ModuleObject.
	 */
	public function __destruct() {
		Core::destruct();
	}

	/**
	 * Default entry point
	 **/
	public function route() {
		$method = Request::getObject()->getArg(0);
		// This checks whether there is a public method with the specified name
		if (in_array($method, get_class_methods($this))) {
			$this->$method();
		}
		else {
			$this->main();
		}
	}

	/**
	 * Default page
	 **/
	public abstract function main();

	/**
	 * Returns the internal version number of this module.
	 *
	 * @return	string	Version number
	 */
	protected function version() {
		return $this->version;
	}

}
?>

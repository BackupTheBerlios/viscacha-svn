<?php
Core::loadClass('Core.Util.Template.Template');
Core::loadClass('Cms.Util.CmsTools');
Core::loadClass('Cms.Util.Breadcrumb');
Core::loadClass('Cms.Auth.Session');

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
		// Start Benchmark
		$debug = Core::getObject('Core.System.Debug');
		$debug->startClock($this->module);

		// Set attributes
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

		$debug = Core::getObject('Core.System.Debug');
		$debug->stopClock($this->module);
	}

	/**
	 * Default entry point
	 */
	public function route() {
		$method = Request::getObject()->get(0);
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
	 */
	public abstract function main();

}
?>

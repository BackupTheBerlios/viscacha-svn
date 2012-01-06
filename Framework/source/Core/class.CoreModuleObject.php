<?php
Core::loadClass('Core.Net.HTTPHeader');

/**
 * This is a general module object. All modules should extend it.
 *
 * @package		Core
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */
abstract class CoreModuleObject extends ModuleObject {

	/**
	 * Constructs a new CoreModuleObject with database connection etc.
	 *
	 * Note: Before you call this constructor, you have to set the variable $module!
	 *
	 * An Exception will be thrown if the construction of the database driver fails (Code: 1).
	 *
	 * @throws Exception
	 * @todo Verbesserung der Exception
	 **/
	public function __construct($package) {
		parent::__construct($package);

		// Start Benchmark
		$debug = Core::getObject('Core.System.Debug');
		$debug->startClock($this->module);

		// HTTP Headers and GZIP Output
		$headers = new HTTPHeader(Config::get('http.gzip'));
		Core::storeNObject($headers, 'HTTPHeader');

		// Establish database connection and store object
		$driver = Config::get('db.driver');
		$db = Core::constructObject("Core.DB.{$driver}");
		if ($db == null) {
		   throw new Exception("Could not construct database driver of type {$driver}.");
		}
		$db->connect(Config::get('db.username'), Config::get('db.password'), Config::get('db.host'), Config::get('db.port'), Config::get('db.socket'));
		$db->selectDB(Config::get('db.database'), Config::get('db.prefix'));
		Core::storeNObject($db, 'DB');
	}

	/**
	 * Destructs the ModuleObject.
	 */
	public function __destruct() {
		parent::__destruct();
		$debug = Core::getObject('Core.System.Debug');
		$debug->stopClock($this->module);
		Core::unsetNObject('HTTPHeader');
	}

}
?>

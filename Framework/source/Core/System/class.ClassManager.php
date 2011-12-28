<?php
/**
 * This class creates an index of all classes available in the "source" directory.
 * When a class is loaded, it just performs simple index lookup to determine the path of each class file.
 *
 * @package		Core
 * @subpackage 	System
 * @author		Matthias Mohr
 * @since 		1.0
 */
class ClassManager {

	private $index;

	/**
	 * Constructs the ClassManager and loads (or builds) the index.
	 */
	public function __construct() {
		$this->loadIndex();
	}

	/**
	 * Loads the class with the given class name from the index.
	 *
	 * @param	string	Class Name
	 * @return	mixed	Filename or null on failure
	 * @access	public
	 */
	public function loadFile($className) {
		if (isset($this->index[$className]) == true) {
			if(file_exists($this->index[$className]) == true) {
				$filename = $this->index[$className];
				include_once($filename);
			}
			else {
				$this->deleteIndex();
				Core::throwError('ClassManager index seems to be outdated. File for class '.$className.' not found: '.$this->index[$className].'. New index scan scheduled. Please refresh this page.', INTERNAL_ERROR);
				$filename = null;
			}
		}
		else {
			$this->deleteIndex();
			Core::throwError('ClassManager has no class with name '.$className.' indexed. New index scan scheduled. Please refresh this page.', INTERNAL_ERROR);
			$filename = null;
		}
		return $filename;
	}

	/**
	 * Returns the index.
	 *
	 * The index is an array. The keys are the class names and the values are the paths for the classes.
	 *
	 * @return	array	Index
	 * @access	public
	 */
	public function getIndex() {
		return $this->index;
	}

	/**
	 * Loads the index from cache (or rebuild it and then get it from the cache).
	 *
	 * The index is after this method stored in an array in $this->index.
	 *
	 * @param	boolean	true if index should be rebuilt, false if cache should be used (standard).
	 * @access	private
	 */
	private function loadIndex($rebuild = false) {
		$cache = Core::getObject('Core.Cache.CacheServer');
		$classesCache = $cache->load('classes', 'Core.Cache.Items');
		if ($rebuild == true) {
			$classesCache->delete();
		}
		$this->index = $classesCache->get();
	}

	/**
	 * Deletes the index cache.
	 *
	 * @access	private
	 */
	private function deleteIndex() {
		$cache = Core::getObject('Core.Cache.CacheServer');
		$classesCache = $cache->load('classes');
		$classesCache->delete();
	}

}

?>

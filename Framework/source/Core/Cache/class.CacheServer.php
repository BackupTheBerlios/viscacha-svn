<?php
Core::loadClass('Core.Cache.CacheItem');

/**
 * The CacheServer manages the CacheItems.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		1.0
 */
class CacheServer {

	private $cachedir;
	private $sourcedir;
	private $data;

	/**
	 * Constructs a new Cache Manager. In this class all loaded CacheItems will be cached.
	 *
	 * @param string Path to the cache data folder
	 * @param string Path to the cache source folder {@see CacheServer::setSourceDir()}
	 **/
	public function __construct($cachedir = 'data/cache/', $sourcedir = 'Core.Cache.Items') {
		$this->cachedir = $cachedir;
		$this->data = array();
		$this->setSourceDir($sourcedir);
	}

	/**
	 * Sets the default source diretory for cache files.
	 *
	 * Format the directory like the normal format for class includes {@see Core::constructObject()}
	 *
	 * @param string Cache class source directory
	 */
	public function setSourceDir($sourcedir) {
		$this->sourcedir = $this->parseSourceDir($sourcedir);
	}

	private function parseSourceDir($sourcedir) {
		$sourcedir = 'source.'.$sourcedir;
		return str_replace('.', DIRECTORY_SEPARATOR, $sourcedir).DIRECTORY_SEPARATOR;
	}

	/**
	 * Loads a cache class file.
	 *
	 * @param string Name of cache file
	 * @param string Cache class source directory in format used in setSourceDir-method.
	 * @see CacheServer::setSourceDir()
	 */
	public function loadClass($name, $sourcedir = null) {
		if ($sourcedir != null) {
			$sourcedir = $this->parseSourceDir($sourcedir);
		}
		else {
			$sourcedir = $this->sourcedir;
		}
		$name = "cache_{$name}";
		$file = "{$sourcedir}class.{$name}.php";
		if (!class_exists($name) && file_exists($file)) {
			include_once($file);
		}
	}


	/**
	 * Returns the default directory where the cache source files will be stored.
	 *
	 * @return string Cache source files
	 */
	public function getSourceDir() {
		return $this->sourcedir;
	}

	/**
	 * Returns the current directory where the cache data files will be stored.
	 *
	 * @return string Cache data files
	 */
	public function getCacheDir() {
		return $this->cachedir;
	}

	/**
	 * This method loads a cache item and adds it to the cache manager.
	 *
	 * If the cache class file is not found or the cache file is corrupt a new
	 * CacheItem will be constructed. An USER_NOTICE will be thrown.
	 *
	 * @param string Name of the cache file (class.cache_ will be added before the name and .php will be added after the name)
	 * @param string Path to the folder with the cache files or null for default source directory.
	 * @return object
	 **/
	public function load($name, $sourcedir = null) {
		$this->loadClass($name, $sourcedir);
		$className = "cache_{$name}";
		if (class_exists($className)) {
			$object = new $className($name, $this->cachedir);
		}
		elseif (class_exists($name)) {
			$object = new $name($name, $this->cachedir);
		}
		else {
			Core::throwError('Cache Class of type "'.$name.'" could not be loaded.', INTERNAL_NOTICE);
			$object = new CacheItem($name, $this->cachedir);
		}
		$this->data[$name] = $object;
		return $object;
	}

	/**
	 * This method removes a cache item from the cache manager.
	 *
	 * If the cache manager has no cache item with the specified name nothing will be done.
	 *
	 * @param string Name of the cache file
	 **/
	public function unload($name) {
		if (isset($this->data[$name]) == true) {
			unset($this->data[$name]);
		}
	}
}
?>

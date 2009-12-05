<?php
/**
 * Viscacha - Flexible Website Management Solution
 *
 * Copyright (C) 2004 - 2010 by Viscacha.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

Core::loadClass('Core.Kernel.Singleton');
Core::loadClass('Core.Cache.CacheObject');

/**
 * The CacheServer manages the CacheObjects.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		0.8
 */
class CacheServer extends Singleton {

	private $cacheDir;
	private $sourceDir;
	private $data;

	/**
	 * Constructs a new Cache Manager. In this class all loaded CacheObjects will be cached.
	 *
	 * @param string Path to the cache data folder
	 * @param string Path to the cache source folder
	 **/
	public function __construct($cacheDir = 'data/cache/', $sourceDir = 'source/Core/Cache/Items') {
		$this->setCacheDir($cacheDir);
		$this->data = array();
		$this->setSourceDir($sourceDir);
	}

	/**
	 * Sets the default source diretory for cache files.
	 *
	 * @param string Cache class source directory
	 */
	public function setSourceDir($sourceDir) {
		$this->sourceDir = rtrim($sourceDir, '\\/').'/';
	}

	/**
	 * Sets the default cache directory for the cached data.
	 *
	 * @param string Directory for cached data
	 */
	public function setCacheDir($cacheDir) {
		$this->cacheDir = rtrim($cacheDir, '\\/').'/';
	}


	/**
	 * Returns the default directory where the cache source files will be stored.
	 *
	 * @return string Cache source files
	 */
	public function getSourceDir() {
		return $this->sourceDir;
	}

	/**
	 * Returns the current directory where the cache data files will be stored.
	 *
	 * @return string Cache data files
	 */
	public function getCacheDir() {
		return $this->cacheDir;
	}

	/**
	 * This method loads a cache item and adds it to the cache manager.
	 *
	 * If the cache class file is not found a new CacheObject will be constructed.
	 *
	 * @param string Name of the cache
	 * @return object
	 **/
	public function load($className) {
		$this->loadClass($className);
		if (class_exists($className)) {
			$object = new $className($className, $this->cacheDir);
		}
		else {
			$object = new CacheObject($className, $this->cacheDir);
		}
		$this->data[$className] = $object;
		return $object;
	}

	/**
	 * This method removes a cache item from the cache manager.
	 *
	 * If the cache manager has no cache item with the specified name nothing will be done.
	 *
	 * @param string Name of the cache file
	 **/
	public function unload($className) {
		if (isset($this->data[$className]) == true) {
			unset($this->data[$className]);
		}
	}


	/**
	 * Loads a cache class file.
	 *
	 * @param string Name of cache file
	 * @see CacheServer::setSourceDir()
	 */
	private function loadClass($name) {
		if (class_exists($name) == false) {
			$file = "{$this->sourceDir}class.{$name}.php";
			if (File::exists($file) == true) {
				include_once($file);
			}
		}
	}

}
?>
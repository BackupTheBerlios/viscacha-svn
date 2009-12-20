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
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

Core::loadClass('Core.Kernel.Singleton');

/**
 * Maps all classes to their location in the source folder.
 *
 * This class creates an index of all classes available in the Viscacha "source" directory.
 * When a class is loaded (via __autoload), it just performs simple index lookup to determine the
 * path of each class file.
 *
 * @package		Core
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class ClassManager extends Singleton {

	/**
	 * Array contains the class names and the location.
	 *
	 * @var array
	 */
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
	 * @return	string	Filepath
	 * @throws	ClassManagerException
	 */
	public function loadFile($className) {
		$filename = null;
		if (isset($this->index[$className]) == true) {
			if(file_exists($this->index[$className]) == true) {
				$filename = $this->index[$className];
				include_once($filename);
			}
			else {
				// Class name is indexed, but no source file available
				// Force a rebuild as the index seems to be outdated
				$this->deleteIndex();
				$e = new ClassManagerException(
					"ClassManager index seems to be outdated. ".
						"File for class '{$className}' not found: ".$this->index[$className],
					1
				);
				$e->setIndex($this->index);
				throw $e;
			}
		}
		else {
			// No class with this name indexed, force a rebuild
			$this->deleteIndex();
			$e = new ClassManagerException(
				"ClassManager has no class with name {$className} indexed.",
				2
			);
			$e->setIndex($this->index);
			throw $e;
		}
		return $filename;
	}

	/**
	 * Returns the array that contains the class names and the location.
	 *
	 * The keys are the class names and the values are the paths for the classes.
	 *
	 * @return	array	Array containing class names and file paths
	 */
	public function getIndex() {
		return $this->index;
	}

	/**
	 * Loads the index from cache (or rebuild it and then get it from the cache).
	 *
	 * @param	boolean	Force to rebuild the cache (= true) or use the cache (= false).
	 */
	private function loadIndex($rebuild = false) {
		$cache = CacheServer::getInstance();
		$classesCache = $cache->load('ClassManagerCache');
		if ($rebuild == true) {
			$classesCache->delete();
		}
		$this->index = $classesCache->get();
	}

	/**
	 * Deletes the index cache.
	 */
	private function deleteIndex() {
		$cache = CacheServer::getInstance();
		$classesCache = $cache->load('ClassManagerCache');
		$classesCache->delete();
	}

}

?>

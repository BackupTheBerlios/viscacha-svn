<?php
/**
 * Viscacha - Flexible Website Management Solution
 *
 * Copyright (C) 2004 - 2010 by Viscacha.org
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package		Core
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License
 */

Core::loadClass('Core.Kernel.Singleton');

/**
 * Maps all classes to their location in the source folder.
 *
 * This class creates an index of all classes available in the Viscacha "source" directory.
 * When a class is loaded (via __autoload for example), it just performs simple index lookup to
 * determine the path of each class file.
 *
 * @package		Core
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class ClassManager {

	const FILE_PATTERN = '~[\\/](class|interface)\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\.php$~i';

	private $index;
	private $cacheFile;
	private static $instance;

	/**
	 * Constructs the ClassManager and loads (or builds) the index.
	 */
	public function __construct() {
		$this->index = array();
		$this->cacheFile = VISCACHA_CACHE_DIR.'ClassManager.ser';
		$this->loadIndex();
	}

	/**
	 * Loads the class with the given class name from the index.
	 *
	 * @param	string	Class Name
	 */
	public static function autoload($className) {
		if (self::$instance == null) {
			self::$instance = new self();
		}
		self::$instance->loadFile($className);
	}

	/**
	 * Loads the class with the given class name from the index.
	 *
	 * @param	string	Class Name
	 * @param	boolean	Try to rebuild on failure
	 * @throws	ClassManagerException
	 */
	public function loadFile($className, $rebuildOnError = true) {
		if (array_key_exists($className, $this->index) == true) {
			$filename = $this->index[$className];
			if(file_exists($filename) == true) {
				include_once($filename);
			}
			else {
				// Class name is indexed, but no source file available
				$error = array(
					"ClassManager index seems to be outdated. ".
						"File for class '{$className}' not found: ".$filename,
					1
				);
			}
		}
		else {
			// No class with this name indexed
			$error = array("ClassManager has no class with name {$className} indexed.", 2);
		}

		if ($rebuildOnError == true) {
			// Force a rebuild as the index seems to be invalid
			$this->deleteCache();
			$this->loadIndex();
			$this->loadFile($className, false);
		}
		else {
			// Rebuild failed, throw exception
			Core::loadClass('Core.Kernel.ClassManagerException');
			$e = new ClassManagerException($error[0], $error[1]);
			$e->setIndex($this->index);
			throw $e;
		}
	}

	/**
	 * Generates the data for the cache.
	 */
	private function loadIndex() {
		if (file_exists($this->cacheFile) == false) {
			$this->scanSourceFolder(realpath(VISCACHA_SOURCE_DIR));
			$this->saveCache();
		}
		else {
			$this->loadCache();
		}
	}

	/**
	 * Saves the index to the cache file.
	 */
	private function saveCache() {
		file_put_contents($this->cacheFile, serialize($this->index));
	}

	/**
	 * Loads the index from the cache file.
	 */
	private function loadCache() {
		if (file_exists($this->cacheFile) == true) {
			$data = file_get_contents($this->cacheFile);
			$this->index = unserialize($data);
		}
	}

	/**
	 * Deletes the cache file.
	 */
	private function deleteCache() {
		if (file_exists($this->cacheFile) == true) {
			unlink($this->cacheFile);
		}
	}

	/**
	 * Scans recursively all Source folders for classes.
	 *
	 * @param	string 	Directory to search in
	 * @return	array	Array containing all files matching the pattern
	 */
	private function scanSourceFolder($dir) {
		$handle = dir($dir);
		while (false !== ($entry = $handle->read())) {
			if ($entry != '.' && $entry != '..') {
				$path = $dir.DIRECTORY_SEPARATOR.$entry;
				if (is_dir($path) == true) {
					$this->scanSourceFolder($path);
				}
				elseif (is_file($path)) {
					$this->parse($path);
				}
			}
		}
		$handle->close();
	}

	/**
	 * Retrieves the classname for a given file.
	 *
	 * @param string File to scan for class name.
	 * @todo token_get_all war bei Implementierung nicht Unicode-kompatibel. Entferne Workaround...
	 */
	private function parse($file) {
		$result = preg_match(self::FILE_PATTERN, $file, $match);

		if (function_exists('token_get_all') == false) {
			// use the file names as an indicator for the class name
			if ($result > 0 && !empty($match[2])) {
				if (isset($this->index[$match[2]]) == true) {
					ErrorHandling::getDebug()->addText(
						"Class with name '{$match[2]}' found more than once."
					);
				}
				$this->index[$match[2]] = $file;
			}
		}
		else {
			if (file_exists($file) == true) {
				$next = false;
				$tokens = token_get_all(file_get_contents($file));
				foreach ($tokens as $token) {
					if (!isset($token[0])) {
						continue;
					}
					// next T_STRING token after this one is our desired class name
					if ($token[0] == T_CLASS || $token[0] == T_INTERFACE) {
						$next = true;
					}
					if ($token[0] == T_STRING && $next === true) {
						// Workaround for unicoede incompatible token_get_all function
						// Remove workaround when function is unicode compatible
						settype($token[1], 'unicode');
						// End Workaround
						if (isset($this->index[$token[1]]) == true) {
							ErrorHandling::getDebug()->addText(
								"Class with name '{$token[1]}' found more than once."
							);
						}
						$this->index[$token[1]] = $file;
						// We found what we need, stop the search
						break;
					}
				}
			}
		}
	}

}
?>
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

Core::loadClass('Core.Cache.CacheItem');

/**
 * Caches the classes for the ClassesManager.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		1.0
 * @todo		Better Documentation
 * @todo		Check implementation (glob, token_get_all, ...)
 */
class ClassManagerCache extends CacheItem {

	private $classes;

	public function __construct($name = __CLASS__, $path = CACHE_DEFAULT_DIR) {
		parent::__construct($name, $path);
		$this->classes = array();
	}

	public function load() {
		$files = $this->scanSourceFolder(new Folder('./source/'));
		foreach($files as $file) {
			$this->parse($file);
		}
	}

	/**
	 * Scans recursively all Source folders for classes.
	 *
	 * @param	string 	Directory to search in
	 * @return	array	Array containing all pattern-matched files.
	 * @todo	Escape path in glob
	 */
	private function scanSourceFolder(Folder $dir) {
		$files = $dir->getFiles(Folder::RETURN_PATHS, Folder::FILTER_GLOB, '{class,interface}.*.php');
		$folders = $dir->getFolders(Folder::RETURN_OBJECTS);

		foreach ($folders as $subDir) {
			$subFiles = $this->scanSourceFolder($subDir);
			$files = array_merge($files, $subFiles);
		}

		return $files;
	}

	/**
	 * Retrieves the classname for a given file.
	 *
	 * @param string File to scan for class name.
	 */
	private function parse($file) {
		if (function_exists('token_get_all') == false) {
			// use the file names as an indicator
			if (preg_match('~(class|interface)\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\.php$~i', $file, $match) == 1) {
				if (!empty($match[2])) {
					if (isset($this->data[$match[2]]) == true) {
						Core::addLog(
							'Class with name '.$match[2].' was found more than once. '.
								"Only file {$file} has been indexed!"
						);
					}
					$this->data[$match[2]] = $file;
				}
			}
		}
		else {
			$file = new File($file);
			if ($file->exists() == true) {
				$next = false;
				$tokens = token_get_all($file->read());
				foreach ($tokens as $token) {
					if (!isset($token[0])) {
						continue;
					}
					// next token after this one is our desired class name
					if ($token[0] == T_CLASS || $token[0] == T_INTERFACE) {
						$next = true;
					}
					if ($token[0] == T_STRING && $next === true) {
						if (isset($this->data[$token[1]]) == true) {
							Core::addLog(
								'Class with name '.$token[1].' was found more than once. '.
									"File '{$file} has been indexed and replaced the other entry!"
							);
						}
						$this->data[$token[1]] = $file->relPath();
						$next = false;
					}
				}
			}
		}
	}

}
?>
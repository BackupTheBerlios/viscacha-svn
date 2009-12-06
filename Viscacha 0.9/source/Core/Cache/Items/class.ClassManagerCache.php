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

	private $next;
	private $classes;

	public function __construct($name = __CLASS__, $path = CACHE_DEFAULT_DIR) {
		parent::__construct($name, $path);
		$this->next = false;
		$this->classes = array();
	}

	public function load() {
		$files = $this->scanSourceFolder('source');
		foreach($files as $file){
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
	private function scanSourceFolder($dir) {
		$files = glob($dir.'/{class,interface}.*.php', GLOB_BRACE);
		$folders = Folder::getFolders($dir);

		foreach ($folders as $subDir) {
			$subFiles = $this->scanSourceFolder($subDir);
			$files = array_merge($files, $subFiles);
		}

		return $files;
	}

	/**
	 * Looks for class names in a PHP code
	 *
	 * @param string File to scan for class name.
	 * @throws CoreException
	 * @todo Reine PHP Alternative um Klassen zu erkennen (RegExp)
	 * @todo Replace Core::throwError
	 */
	private function parse($file) {
		if (function_exists('token_get_all') == false) {
			// Klassenerkennung-Regexp:
			// (abstract\s+)?class\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\s+(extends|implements)\s+[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*){0,2}\s*\{
			throw CoreException(
				"Function token_get_all() is not supported. You can't use the ClassManager."
			);
		}
		$file = new File($file);
		if ($file->exists() == true) {
			$code = $file->read();
			$tokens = @token_get_all($code);
			foreach ($tokens as $token) {
				if (!isset($token[0])) {
					continue;
				}
				// next token after this one is our desired class name
				if ($token[0] == T_CLASS) {
					$this->next = true;
				}
				if ($token[0] == T_STRING && $this->next === true) {
					if (isset($this->data[$token[1]]) == true) {
						Core::throwError(
							'Class with name '.$token[1].' was found more than once. '.
								"Only file {$file} has been indexed!",
							INTERNAL_NOTICE
						);
					}
					$this->data[$token[1]] = $file->relPath();
					$this->next = false;
				}
			}
		}
	}

}
?>

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
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * FolderIterator to iterate through files and/or folders.
 *
 * Information: This class is named FileFolderIterator as PHP has already a 'FilesystemIterator'.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 */
class FileFolderIterator implements Iterator {

	protected $current;
	protected $filter;
	protected $handle;
	protected $mode;
	protected $path;

	/**
	 * Constructs a new FileFolderIterator for a specified path (folder).
	 *
	 * The default behaviour is set. The iterator will return objects and no filter will be applied.
	 *
	 * @param string Path to a folder
	 */
	public function __construct($path) {
		$this->mode = FileSystem::RETURN_OBJECTS;
		$this->filter = null;
		$this->path = FileSystem::unifyPath($path).Folder::SEPARATOR;
		$this->handle = opendir($this->path);
		$this->current = false;
	}

	/**
	 * Destructs an object and closes the handle.
	 */
	public function  __destruct() {
		FileSystem::resetWorkingDir();
		if ($this->handle) {
			closedir($this->handle);
		}
	}

	/**
	 * Sets a regular expression filter for the Iterator.
	 *
	 * The parameter is a complete regular expression pattern that is used to filter the result. The
	 * pattern is applied only on the file/folder name, not on the whole path.
	 *
	 * @param string Filter to apply or null to disable the filter
	 */
	public function setFilter($filter = null) {
		$this->filter = $filter;
	}

	/**
	 * The returned array will contain objects of the type Folder, but if the second parameter is
	 * set to Folder::RETURN_PATHS just the paths are returned. The keys of the array are the folder
	 * names.
	 *
	 * @param <type> $mode
	 */
	public function setMode($mode = FileSystem::RETURN_OBJECTS) {
		$this->mode = $mode;
	}

	/**
	 * Rewinds back to the first element of the Iterator.
	 */
	public function rewind() {
		if ($this->handle) {
			rewinddir($this->handle);
		}
		$this->next(); // Set to first element
	}

	/**
	 * Returns the current element.
	 *
	 * @return mixed
	 */
	public function current() {
		$fullpath = $this->path.$this->current;
		if ($this->mode == FileSystem::RETURN_PATHS) {
			return $fullpath;
		}
		else {
			return $this->constructObject($fullpath);
		}
	}

	/**
	 * Return the key of the current element.
	 *
	 * @return scalar Returns scalar on success, integer 0 on failure.
	 */
	public function key() {
		if ($this->handle) {
			return $this->current;
		}
		else {
			return 0;
		}
	}

	/**
	 * Moves the current position to the next element.
	 */
	public function next() {
		if ($this->handle) {
			do {
				$entry = readdir($this->handle);
			} while ($entry !== false && $this->matchesFilter($entry) == false);
			$this->current = $entry;
		}
		else {
			$this->current = false;
		}
	}

	/**
	 * Checks if current position is valid
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function valid() {
		return ($this->handle && $this->current !== false);
	}

	/**
	 * Returns the Iterator results as an array.
	 *
	 * We assume that no file has the same name as a folder. If this should be possible on any
	 * operating system this can lead to unexpected behaviour (file or folder missing).
	 *
	 * @return array
	 */
	public function toArray() {
		$data = array();
		foreach ($this as $key => $value) {
			$data[$key] = $value;
		}
		return $data;
	}

	/**
	 * Checks whether a file/folder can be used in this Iterator.
	 *
	 * Checks the name against the filter etc.
	 *
	 * @param string File/Folder name to check
	 * @return boolean Returns true for a valid, false for an invalid file/folder.
	 */
	protected function matchesFilter($name) {
		if ($name == '.' || $name == '..') {
			return false; // This and the parent directory
		}
		elseif (is_file($this->path.$name) == false && is_dir($this->path.$name) == false) {
			return false; // Not a dir and not a file
		}
		elseif ($this->filter === null) {
			return true; // No filter
		}
		elseif($this->filter !== null && preg_match($this->filter, $name) > 0) {
			return true; // Filter set and name matches the filter
		}
		else {
			return false; // Filter set and name doesn't match the filter
		}
	}

	/**
	 * Constructs a new object of the correct type to return when Objects should be returned.
	 *
	 * @param string Path for the new object
	 * @return File|Folder
	 */
	protected function constructObject($path) {
		if (is_file($path)) {
			return new File($path);
		}
		else {
			return new Folder($path);
		}
	}

}
?>

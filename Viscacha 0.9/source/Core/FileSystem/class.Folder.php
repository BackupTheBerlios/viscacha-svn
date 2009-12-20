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

Core::loadClass('Core.FileSystem.FileSystemBaseUnit');

/**
 * Folder handling class with ftp fallback if configured.
 *
 * Static methods are also implemented in the Folders class.
 *
	 * Information: This class is named Folder because PHP has a built-in class named 'Directory'.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 */
class Folder extends FileSystemBaseUnit {

	const FILTER_NONE = 0;
	const FILTER_GLOB = 1;
	const FILTER_PCRE = 2;

	const RETURN_OBJECTS = 0;
	const RETURN_PATHS = 1;

	const SEPARATOR = DIRECTORY_SEPARATOR;

	/**
	 * Creates a new object of type Folder.
	 *
	 * The folder given as parameter need not exist.
	 * The folder can be a relative or a absolute path.
	 * If you specify a file the parent folder is used.
	 *
	 * @param	string	Path to a folder.
	 */
	public function __construct($dir) {
		if (is_file($dir)) {
			$dir = dirname($dir);
		}
		$this->path = $dir;
	}

	/**
	 * Returns the name of the file/folder.
	 *
	 * @return	string	Name of the file/folder
	 */
	public function name() {
 		return basename($this->absPath());
	}

	/**
	 * Folder will be created and Permissions will be set to the specified permissions.
	 *
	 * If the folder exists just the permissions will be set correctly.
	 * See FileSystemBaseUnit::setPermissions on how to set the permissions correctly.
	 * This function will also return false if the chmod are not set correctly.
	 * Folders are created recursively.
	 *
	 * This function implements the ftp fallback!
	 *
	 * @see Folder::setPermissions()
	 * @see File::setPermissions()
	 * @param int Permissions to set for the directory (default is 777)
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function create($permissions = 777) {
		if ($this->exists() == false) {
			if (!mkdir($this->path, $permissions, true)) {
				$ftp = FileSystem::initializeFTP();
				// Make sure the dir is created recursively as this is not natively supported by ftp
				$folders = preg_split('~[\\/]+~', $this->ftpPath(), -1, PREG_SPLIT_NO_EMPTY);
				$root = self::SEPARATOR;
				foreach ($folders as $folder) {
					$root .= $folder.self::SEPARATOR;
					if ($ftp->exists($root) == false) {
						$ftp->mkdir($root);
					}
				}
				$this->setPermissions($permissions);
			}
		}
		else {
			$this->setPermissions($permissions);
		}
		return ($this->exists() && $this->getPermissions() == $permissions);
	}

	/**
	 * Deletes the folder completely with all contents.
	 *
	 * Does nothing when the folder does not exist and returns true.
	 *
	 * This function implements the ftp fallback!
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function delete() {
		if ($this->exists()) {
			// Remove the content
			$this->clear();
			if (rmdir($this->path) == false) {
				$ftp = FileSystem::initializeFTP();
				return $ftp->rmdir($this->ftpPath());
			}
		}
		return true;
	}

	/**
	 * Folder will be cleared (all files and subfolders will be deleted).
	 *
	 * After calling this command the folder is completely empty.
	 *
	 * This function implements the ftp fallback!
	 *
	 * @returns	boolean Returns TRUE on success or FALSE on failure.
	 */
	public function clear() {
		foreach ($this->getContents(self::OBJECTS) as $content) {
			$content->delete();
		}
		return $this->isEmpty();
	}

	/**
	 * This returns an array with the folders and files in the directory (not recursive).
	 *
	 * The returned array will contain objects of the types File and Folder.
	 * This is the merged result of the functions Folder::getFolders() and Folder::getFiles().
	 *
	 * @param int Function that uses the pattern (one of the Folder::FILTER_* constants)
	 * @param string Search pattern
	 * @return array
	 * @see Folder::getFolders()
	 * @see Folder::getFiles()
	 * @todo Check what happens if there are the same indice (folder and file named x for example)
	 * @todo Implement
	 */
	public function getContents($mode = self::RETURN_OBJECTS, $type = self::FILTER_NONE, $pattern = null) {
		return array_merge($this->getFolders($type, $pattern), $this->getFiles($type, $pattern));
	}

	/**
	 * Returns an array with all folders directly in this folder (not recursive).
	 *
	 * All array elements are Folder objects.
	 * The keys of the array are the folder names.
	 * The array is sorted by the keys.
	 *
	 * @see ksort()
	 * @return array
	 * @todo Implement
	 */
	public function getFolders($mode = self::RETURN_OBJECTS, $type = self::FILTER_NONE, $pattern = null) {
		$path = $this->absPath().DIRECTORY_SEPARATOR;
		if ($pattern == null) {
			$type = self::FILTER_NONE;
		}
		if ($type != self::FILTER_GLOB) {
			$d = dir($path);
			$folders = array();
			while (false !== ($entry = $d->read())) {
				if (is_dir($path.$entry) && $entry != '.' && $entry != '..') {
					if ($type == self::FILTER_NONE || ($type == self::FILTER_PCRE && preg_match($pattern, $entry))) {
						if ($mode == self::RETURN_OBJECTS) {
							$folders[$entry] = new Folder($path.$entry);
						}
						else {
							$folders[$entry] = $path.$entry;
						}
					}
				}
			}
			$d->close();
		}
		else {
			// Glob
		}
		ksort($folders);
		return $folders;
	}

	/**
	 * Returns an array with all files directly in this folder (not recursive).
	 *
	 * You can limit the files by giving an extension as paramter.
	 *
	 * All array elements are File objects.
	 * The keys of the array are the filenames.
	 * The array is sorted by the keys.
	 *
	 * @return array
	 * @todo Implement
	 */
	public function getFiles($mode = self::RETURN_OBJECTS, $type = self::FILTER_NONE, $pattern = null) {
		$path = $this->absPath().Folder::SEPARATOR;
		$d = dir($path);
		$files = array();
		while (false !== ($entry = $d->read())) {
			if (is_file($path.$entry)) {
				if ($mode == self::RETURN_OBJECTS) {
					$files[$entry] = new File($path.$entry);
				}
				else {
					$files[$entry] = $path.$entry;
				}
			}
		}
		ksort($files);
		$d->close();
		return $files;
	}

	/**
	 * Checks whether the directory is empty or not.
	 *
	 * @return boolean
	 */
	public function isEmpty() {
		$d = dir($this->path);
		while (false !== ($entry = $d->read())) {
			if ($entry != '.' && $entry != '..') {
				$d->close();
				return false;
			}
		}
		$d->close();
		return true;
	}

	/**
	 * Copy a file, or recursively copy a folder and its contents.
	 *
	 * After executing this method successfully, this object will not point to the new folder!
	 * This method can only copy files and folders, all other things will be ignored.
	 * If the source path does not exist this function returns false. If the function could only
	 * do a partial copy the function returns false after trying to copy the remaining files.
	 * New folders are created with the default permissions set by Folder::create().
	 *
	 * This function implements the ftp fallback!
	 *
	 * @see Folder::create()
	 * @param string Destination path
	 * @return bool Returns true on success, false on failure
	 */
	public function copy($dest) {
		if ($this->exists() == false) {
			return false;
		}
		$folder = new Folder($dest);
		// Create destination directory if missing
		if ($folder->exists() == false && $folder->create() == false) {
			return false;
		}
		$ret = true;
		if ($this->exists() && !$this->isEmpty()) {
			foreach ($this->getContents() as $content) {
				if ($content->copy($dest.self::SEPARATOR.$content->name()) == false) {
					$ret = false;
				}
			}
		}
		return $ret;
	}


	/**
	 * Checks whether a folder exists or not.
	 *
	 * @return boolean Returns TRUE if the folder exists; FALSE otherwise.
	 */
	public function exists() {
		return (file_exists($this->path) && is_dir($this->path));
	}

}
?>
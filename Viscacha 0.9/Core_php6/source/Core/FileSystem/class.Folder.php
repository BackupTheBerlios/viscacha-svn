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
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License
 */

/**
 * Folder handling class with ftp fallback if configured.
 *
 * Information: This class is named Folder because PHP has a built-in class named 'Directory'.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 */
class Folder extends FileSystemNode {

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
	public function __construct($dir, $ftpFallback = true) {
		if (is_file($dir)) {
			$dir = dirname($dir);
		}
		parent::__construct($dir, $ftpFallback);
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
	 * If the folder exists just the permissions will be set.
	 * See FileSystemNode::setPermissions() on how to specify the permissions correctly.
	 * This function will also return false if the chmod are not set correctly.
	 * Folders are created recursively.
	 *
	 * This function implements the ftp fallback!
	 *
	 * @see Folder::setPermissions()
	 * @param int Permissions to set for the directory (default is 777)
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function create($permissions = 777) {
		$chmodResult = false;
		if ($this->exists() == false) {
			if (mkdir($this->path, octdec($permissions), true) == false && $this->ftp == true) {
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
				$chmodResult = $this->setPermissions($permissions);
			}
		}
		else {
			$chmodResult = $this->setPermissions($permissions);
		}

		/*
		 * We should check the permissions like below, but windows does not support chmods properly
		 * and therefore this condition would fail always for chmods other than 666 and 777.
		 * return ($this->exists() && $this->getPermissions() == $permissions);
		 */
		return ($this->exists() && $chmodResult);
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
		if ($this->exists() == false) {
			return true;
		}

		// Remove the content
		$clear = $this->clear();
		if ($clear == true && rmdir($this->path) == true) {
			return true;
		}
		elseif ($clear == true && $this->ftp == true) {
			$ftp = FileSystem::initializeFTP();
			if ($ftp !== null) {
				return $ftp->rmdir($this->ftpPath());
			}
		}
		return false;
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
		foreach ($this->getContents() as $content) {
			$content->delete();
		}
		return $this->isEmpty();
	}

	/**
	 * Returns an FileFolderIterator for the files and folders in this directory (not recursive).
	 *
	 * The returned iterator will return objects of the types File and Folder, but if the second
	 * parameter is set to FileSystem::RETURN_PATHS just the paths are returned.
	 *
	 * The first parameter is a regular expression pattern that is used to filter the result. This
	 * method does not allow the extension pattern that is allowed by Folder::getFileIterator().
	 *
	 * @param int Whether to return objects or paths
	 * @param string Search pattern (Regular Expression)
	 * @return FileFolderIterator
	 * @see Folder::getFileIterator()
	 * @see Folder::getContents()
	 */
	public function getFileFolderIterator($pattern = null, $mode = FileSystem::RETURN_OBJECTS) {
		$iterator = new FileFolderIterator($this->path);
		if ($pattern !== null) {
			$iterator->setFilter($pattern);
		}
		$iterator->setMode($mode);
		return $iterator;
	}

	/**
	 * Returns a FolderIterator for the folders in this folder (not recursive).
	 *
	 * The returned Iterator will return objects of the type Folder, but if the second parameter is
	 * set to FileSystem::RETURN_PATHS just the paths are returned.
	 *
	 * The first argument is a regular expression pattern that is used to filter the result. The
	 * pattern is applied only on the folder name, not on the whole path.
	 *
	 * @param int Whether to return objects or paths
	 * @param string Search pattern (Regular Expression)
	 * @return FolderIterator
	 * @see Folder::getFolders()
	 */
	public function getFolderIterator($pattern = null, $mode = FileSystem::RETURN_OBJECTS) {
		$iterator = new FolderIterator($this->path);
		if ($pattern !== null) {
			$iterator->setFilter($pattern);
		}
		$iterator->setMode($mode);
		return $iterator;
	}

	/**
	 * Returns an FileIterator for the files directly in this folder (not recursive).
	 *
	 * The returned Iterator will return objects of the type File, but if the second parameter is
	 * set to FileSystem::RETURN_PATHS just the paths are returned.
	 *
	 * The first argument is a regular expression pattern that is used to filter the result. The
	 * pattern is applied only on the file name, not on the whole path.
	 * If the first parameter starts with a dot a faster filter only on the file extension will be
	 * set. This causes that a regular expression pattern can't start with a dot!
	 *
	 * @param int Whether to return objects or paths
	 * @param string Search pattern (regular expression or a file extension starting with a dot)
	 * @return array Returns the array or null on failrue
	 */
	public function getFileIterator($pattern = null, $mode = FileSystem::RETURN_OBJECTS) {
		$iterator = new FileIterator($this->path);
		$iterator->setMode($mode);
		if ($pattern !== null) {
			if ($pattern !== null && strlen($pattern) > 0 && $pattern[0] == '.') {
				$extension = substr($pattern, 1);
				$iterator->setExtensionFilter($extension);
			}
			elseif ($pattern !== null) {
				$iterator->setFilter($pattern);
			}
		}
		return $iterator;
	}

	/**
	 * Returns an array containing files and folders in the directory (not recursive).
	 *
	 * The first argument is a regular expression pattern that is used to filter the result.
	 * This method does not allow the extension pattern that is allowed by Folder::getFiles().
	 *
	 * The returned array will contain objects of the types File and Folder, but if the second
	 * parameter is set to Folder::RETURN_PATHS just the paths are returned.
	 *
	 * Note that the returned array does have numeric keys and not the file and folder names like
	 * the FileFolderIterator.
	 *
	 * It is recommended to use Folder::getFileFolderIterator() if possible as this just calls
	 * FileFolderIterator::toArray() using an additional loop.
	 *
	 * @param int Whether to return objects or paths
	 * @param string Search pattern (regular expression or a file extension starting with a dot)
	 * @return array
	 * @see Folder::getFileFolderIterator()
	 * @see FileFolderIterator::toArray()
	 */
	public function getContents($pattern = null, $mode = self::RETURN_ITERATOR) {
		return array_values($this->getFileFolderIterator($pattern, $mode)->toArray());
	}

	/**
	 * Returns an array containing folders directly in this folder (not recursive).
	 *
	 * The first argument is a regular expression pattern that is used to filter the result. The
	 * pattern is applied only on the folder name, not on the whole path.
	 *
	 * The returned array will contain objects of the type Folder, but if the second parameter is
	 * set to Folder::RETURN_PATHS just the paths are returned.
	 *
	 * Note that the returned array does have numeric keys and not the file and folder names like
	 * the FolderIterator.
	 *
	 * It is recommended to use Folder::getFolderIterator() if possible as this just calls
	 * FolderIterator::toArray() using an additional loop.
	 *
	 * @param int Whether to return objects or paths
	 * @param string Search pattern (Regular Expression)
	 * @return array
	 * @see Folder::getFolderIterator()
	 * @see FolderIterator::toArray()
	 */
	public function getFolders($pattern = null, $mode = self::RETURN_ITERATOR) {
		return array_values($this->getFolderIterator($pattern, $mode)->toArray());
	}

	/**
	 * Returns an array containing files directly in this folder (not recursive).
	 *
	 * The first argument is a regular expression pattern that is used to filter the result. The
	 * pattern is applied only on the file name, not on the whole path.
	 * If the first parameter pattern starts with a dot a faster filter only on the file extension
	 * is applied. This causes that a regular expression pattern can't start with a dot!
	 *
	 * The returned array will contain objects of the type File, but if the second parameter is set
	 * to Folder::RETURN_PATHS just the paths are returned.
	 *
	 * Note that the returned array does have numeric keys and not the file and folder names like
	 * the FileIterator.
	 *
	 * It is recommended to use Folder::getFileIterator() if possible as this just calls
	 * FileIterator::toArray() using an additional loop.
	 *
	 * @param int Whether to return objects or paths
	 * @param string Search pattern (regular expression or a file extension starting with a dot)
	 * @return array
	 * @see Folder::getFileIterator()
	 * @see FileIterator::toArray()
	 */
	public function getFiles($pattern = null, $mode = FolderIterator::RETURN_OBJECTS) {
		return array_values($this->getFileIterator($pattern, $mode)->toArray());
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
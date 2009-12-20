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
 * This is a File System Base Unit, it is the base for files and folders and is extended by both
 * classes.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 * @todo		Check whether the use of clearstatcache() could make sense
 * @abstract
 */
abstract class FileSystemBaseUnit {

	const PERMISSIONS_NUMBER = 0;
	const PERMISSIONS_STRING = 1;

	/**
	 * Given Path to the file system base unit (file/folder).
	 * @var string
	 */
	protected $path;

	/**
	 * Returns the file/folder path as specified in the constructor.
	 *
	 * @return string
	 * @see	FileSystemBaseUnit::relPath()
	 */
	public function __toString() {
		return $this->relPath();
	}

	/**
	 * Tells whether the file/folder is readable (and exists).
	 *
	 * @return	boolean	Returns TRUE if the file/folder exists and is readable.
	 */
	public function readable() {
		return ($this->exists() && is_readable($this->path));
	}

	/**
	 * Tells whether the file/folder is executable (and exists).
	 *
	 * @return	boolean	Returns TRUE if the file/folder exists and is executable.
	 */
	public function executable() {
		return ($this->exists() && is_executable($this->path));
	}

	/**
	 * Tells whether the file/folder is writable (and exists).
	 *
	 * @return	boolean	Returns TRUE if the file/folder exists and is writable.
	 */
	public function writable() {
		return ($this->exists() && is_writable($this->path));
	}

	/**
	 * Returns the path of the file/folder how it was specified before.
	 *
	 * @return string Returns variable FileSystemBaseUnit::$path.
	 */
	public function relPath() {
		return $this->path;
	}

	/**
	 * Returns canonicalized absolute pathname to the file/folder.
	 *
	 * @return string
	 * @see FileSystem::unifyPath()
	 */
	public function absPath() {
		return FileSystem::unifyPath($this->path);
	}

	/**
	 * Returns canonicalized absolute pathname to the file/folder for ftp usage.
	 */
	public function ftpPath() {
		return $this->ftpizePath($this->path);
	}

	/**
	 * @todo Implement
	 */
	protected function ftpizePath($path) {
		$path = FileSystem::unifyPath($path);
		return str_ireplace($webRoot, $ftpRoot, $path);
	}

	/**
	 * Attempts to change the mode of the file/folder to the given mode.
	 *
	 * Unlike the PHP chmod function you must not specify the chmod as octal number.
	 * Correct: 777, 755, 666, 400, 0; Wrong: 0777, 0755, 0666, 007.
	 * Does nothing and returns false when the file/folder does not exist.
	 * This function is useless on a Windows OS.
	 *
	 * This function implements the ftp fallback!
	 *
	 * @param int|string Permissions/Chmod to set
	 * @return boolean true on success, false on failure
	 */
	public function setPermissions($chmod) {
		if ($this->exists() == true) {
			$chmod = octdec($chmod); // Convert it to octal for PHP
			if (chmod($this->path, $chmod) == false) {
				$ftp = FileSystem::initializeFTP();
				return $ftp->chmod($this->ftpPath(), $chmod);
			}
			else {
				return true;
			}
		}
		else {
			return false;
		}
	}

	/**
	 * Returns the permission of a file/folder.
	 *
	 * The Chmod is returned as integer (default, e.q. 777, 755, 666, ...) or as string
	 * (e.q. rwxrwxrwx). To choose the return type you have to set the parameter to
	 * FileSystemBaseUnit::PERMISSIONS_NUMBER (default) or FileSystemBaseUnit::PERMISSIONS_STRING.
	 * 
	 * @return int|string Permissions of the file/folder as integer or string
	 */
	public function getPermissions($type = self::PERMISSIONS_NUMBER) {
		if ($this->exists() == false) {
			return null;
		}
		// If you only want the permissions (lowest three octal numbers) you can use a 
		// bitwise AND to mask the bits
		$mode = fileperms($this->path) & 511;
		if ($type == self::PERMISSIONS_STRING) {
			$trans = array(
				'0'=>'---',
				'1'=>'--x',
				'2'=>'-w-',
				'3'=>'-wx',
				'4'=>'r--',
				'5'=>'r-x',
				'6'=>'rw-',
				'7'=>'rwx',
			);
			$mode = strval($this->chmod);
			$strMode = '';
			for($i = 0; $i < 3; $i++) {
				$strMode .= $trans[$mode[$i]];
			}
			return $strMode;
		}
		else {
			$mode = (int) substr(sprintf('%d', fileperms($this->path)), -3);
		}
		return $mode;
	}

	/**
	 * Attempts to move the file/folder to the specified path.
	 *
	 * After executing this method successfully, this object will point to the new file/folder!
	 * To just rename a file/folder please consider using the FileSystemBaseUnit::rename() function.
	 * If there is already a file/folder with the name specified as parameter nothing will be done
	 * and false will be returned.
	 *
	 * This function implements the ftp fallback!
	 *
	 * @see Folder::rename()
	 * @see File::rename()
	 * @param string Path to the new location.
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function move($dest) {
		if ($this->exists() == false || file_exists($dest) == false) {
			return false;
		}
		else if (rename($this->path, $dest) == true) {
			$this->path = $dest;
			return true;
		}
		else {
			$ftp = FileSystem::initializeFTP();
			if ($ftp !== null && $ftp->rename($this->ftpPath(), $this->ftpize($dest)) === true) {
				$this->path = $dest;
				return true;
			}
			else {
				return false;
			}
		}
	}

	/**
	 * Attempts to rename the file/folder to the specified name.
	 *
	 * The parameter newname should only consists of the pure file/folder name without a path!
	 * To move and rename a folder use the method FileSystemBaseUnit::move(). After executing this
	 * method successfully, this object will point to the new file/folder! If there is already a
	 * file with the name specified as parameter nothing will be done and false will be returned.
	 *
	 * This function implements the ftp fallback!
	 * 
	 * @see File::move()
	 * @see Folder::move()
	 * @param string New name for the file/folder
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function rename($newname) {
		if ($this->exists() === false) {
			return false;
		}
		else {
			$parentDir = dirname($this->absPath());
			$to = $parentDir.Folder::SEPARATOR.$newname;
			if (file_exists($to) === false) {
				return $this->move($to);
			}
			else {
				return false;
			}
		}
	}

	public abstract function copy($dest);

	public abstract function delete();

	public abstract function create($permissions = 0);

	public abstract function exists();

}
?>

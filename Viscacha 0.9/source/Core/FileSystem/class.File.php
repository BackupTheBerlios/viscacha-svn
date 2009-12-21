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
 * File handling class with ftp fallback if configured.
 *
 * Static methods are also implemented in the Files class.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 */
class File extends FileSystemBaseUnit {

	const READ_STRING = 0;
	const READ_BINARY = 1;
	const READ_LINES = 2;

	/**
	 * File handle.
	 * @var resource
	 */
	private $handle;

	/**
	 * Creates a new object of type File.
	 *
	 * The file given as parameter must not exist.
	 * The file path can be a relative or an absolute path.
	 *
	 * @param	string	Path to a file.
	 */
	public function __construct($path) {
		$this->path = $path;
		$this->handle = null;
	}

	/**
	 * Closes all open file handles.
	 */
	public function __destruct() {
		if (is_resource($this->handle) == true) {
			fclose($this->handle);
		}
	}

	/**
	 * Returns object of type Folder that represents the folder the file is located in.
	 *
	 * @return	Folder	Folder the file is in
	 */
	public function folder() {
		$path = dirname($this->path);
		return new Folder($path);
	}

	/**
	 * Returns the filename.
	 *
	 * The parameter specifies if the extension will be returned after the filename.
	 * If the parameter is true (which is the default behaviour) the extension will be returned, if the parameter is false it will be removed.
	 *
	 * @param 	boolean	With extension (true, default) or without (false).
	 * @return	string	Filename
	 */
	 public function name($extension = true) {
	 	if ($extension == true) {
	 		return basename($this->path);
	 	}
	 	else {
	 		return basename($this->path, $this->extension(true));
	 	}
	 }

	/**
	 * Returns the file extension without leading dot and in lowercase.
	 *
	 * @param	boolean	true to add the leading dot, false (without dot) is default
	 * @return	string	File Extension
	 */
	public function extension($dot = false) {
		return ($dot ? '.' : '').strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
	}

	/**
	 * Returns the size of the file in bytes, or FALSE in case of an error.
	 *
	 * Note: Because PHP's integer type is signed and many platforms use 32bit integers,
	 * File::size() may return unexpected results for files which are larger than 2GB.
	 *
	 * @return	int Size of file in bytes
	 */
	public function size() {
		if ($this->exists() == false) {
			return false;
		}
	 	return filesize($this->path);
	 }

	/**
	 * File will be created.
	 *
	 * If the file exists, the command will be ignored and true will be returned.
	 *
	 * This function implements the ftp fallback!
	 *
	 * @return	boolean Returns TRUE on success or FALSE on failure.
	 * @todo Add documentation about permissions
	 */
	public function create($permissions = 666) {
		if ($this->exists() == false) {
			return $this->write('');
		}
		else {
			return true;
		}
	}

	/**
	 * Writes a string to a file.
	 *
	 * Before writing to the file, it will be created if needed and the chmod will be set to 666.
	 *
	 * This function implements the ftp fallback!
	 *
	 * @param	string	String to write to the file.
	 * @param 	boolean Set this to true to append data to the file.
	 * @return	boolean Returns TRUE on success or FALSE on failure.
	 * @todo Implement (incl. ftp)
	 */
	public function write($content, $append = false) {
		if ($append == true) {
			$append = FILE_APPEND;
		}
		else {
			$append = null;
		}
		$this->setPermissions('666');
		if (file_put_contents($this->path, $content, $append) === false) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Reads a file (in three different ways).
	 *
	 * If the parameter is -1 (FILE_COMPLETE) or if no parameter is given, the complete file will be returned at once.<br />
	 *
	 * If the parameter is -2 (FILE_LINES) or -3 (FILE_LINES_TRIM), the function returns the file in an array.
	 * Each element of the array corresponds to a line in the file.
	 * If the parameter is -2 (FILE_LINES) the newline/carriage return will be still attached, -3 (FILE_LINES_TRIM) removes them at the end.<br />
	 *
	 * If the parameter is > 0, the function reads up to the specified amount of bytes from the file pointer.
	 * When the end of file (EOF) is reached the function returns null.
	 * If the file does not exist or an error occurs, null will be returned.
	 *
	 * Example:
	 * <code>
	 *	$f = new File('file.txt');
	 *	while ($data = $f->read(1024)) {
	 *		echo $data;
	 *	}
	 * </code>
	 *
	 * @param	int	Type to read the file. Standard: -1 (FILE_COMPLETE)
	 * @return	mixed	Requested content (as array or string) or null
	 * @todo Better trim for FILE_LINES_TRIM (replace foreach with ...?)
	 * @todo Check implementation
	 */
	public function read($type = self::READ_STRING) {
		if ($this->readable() == false) {
			return null;
		}
		if ($type == self::READ_STRING) {
			$contents = file_get_contents($this->path);
			if ($contents != false) {
				return $contents;
			}
			else {
				return null;
			}
		}
		elseif ($type == self::READ_LINES || $type == FILE_LINES_TRIM) {
			// When files are bigger than 8 MB then use another method to read file into array.
			if ($this->size() <= 8*1024*1024) {
				$array = file($this->path);
			}
			else {
				$array = array();
				$this->handle = fopen($this->path, 'rb');
				if (is_resource($this->handle) == false) {
					return null;
				}
				while (feof($this->handle) == false) {
					$array[] = fgets($this->handle);
				}
				fclose($this->handle);
			}
			if ($array != false) {
				if ($type == FILE_LINES_TRIM) {
					foreach ($array as $key => $value) {
						$array[$key] = rtrim($value, "\r\n");
					}
				}
				return $array;
			}
			else {
				return null;
			}
		}
		elseif ($type > 0) {
			if (is_resource($this->handle) == false) {
				$this->handle = fopen($this->path, 'rb');
				if (is_resource($this->handle) == false) {
					return null;
				}
			}
			if (feof($this->handle) == false) {
				$part = fread($this->handle, $type);
				if ($part != false) {
					return $part;
				}
				else {
					return null;
				}
			}
			else {
				fclose($this->handle);
				return null;
			}
		}
		else {
			FileSystem::addDebug("The specified type ({$type}) to read the file '{$this->path}' is not supported.");
			return null;
		}
	}

	/**
	 * Returns the last modification time of the file as unix timestamp.
	 *
	 * Returns null if file does not exist.
	 * 
	 * @return int Last file modification time or null
	 */
	public function modTime() {
		if ($this->exists() == false) {
			return null;
		}
		$time = filemtime($this->path);
		return ($time !== false ? $time : null);
	}

	/**
	 * Returns the last access time of the file as unix timestamp.
	 *
	 * Returns null if file does not exist.
	 *
	 * @return int Last file access time or null
	 */
	public function accessTime() {
		if ($this->exists() == false) {
			return null;
		}
		$time = fileatime($this->path);
		return ($time !== false ? $time : null);
	}

	/**
	 *
	 * @param int
	 * @param int
	 * @todo Implement
	 */
	public function setTime($accessTime = null, $modTime = null) {
		return touch($this->file, $modTime, $accessTime);
	}

	/**
	 * Deletes the file.
	 *
	 * Does nothing and returns true when the file does not exist.
	 *
	 * This function implements the ftp fallback!
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function delete() {
		if ($this->exists() == true) {
			if (unlink($this->path) == false) {
				$ftp = FileSystem::initializeFTP();
				return $ftp->delete($this->ftpPath());
			}
		}
		return true;
	}

	/**
	 * Copy a file to a destination.
	 *
	 * After executing this method successfully, this object will not point to the new file!
	 * If the specified destination does not exist this function returns false.
	 *
	 * This function implements the ftp fallback!
	 *
	 * @param string Destination path
	 * @return bool Returns true on success, false on failure
	 */
	public function copy($dest) {
		if ($this->exists() == false) {
			return false;
		}
		if (copy($this->path, $dest) == false) {
			$fp = fopen($this->path, "r");
			$ftp = FileSystem::initializeFTP();
			if (is_resource($fp) == true && $ftp !== null) {
				$ret = $ftp->put($fp, $this->ftpizePath($dest));
				fclose($fp);
				return $ret;
			}
		}
		return false;
	}

	/**
	 * Moves an uploaded file to another destination.
	 *
	 * @param string Destination
	 * @return boolean true on success, false on failure.
	 * @see move_uploaded_file()
	 */
	public function moveUploaded($destination) {
		if (is_uploaded_file($this->path) == false) {
			return false;
		}
		return move_uploaded_file($this->path, $destination);
	}

	/**
	 * Checks whether a file exists or not.
	 *
	 * @return boolean Returns TRUE if the file exists; FALSE otherwise.
	 */
	public function exists() {
		return (file_exists($this->path) && is_file($this->path));
	}

}
?>
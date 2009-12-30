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
 * File handling class with optional ftp fallback if configured.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 */
class File extends FileSystemBaseUnit {

	const UNICODE = 0;
	const BINARY = 1;

	const READ_STRING = 0;
	const READ_LINES = -1;
	const READ_LINES_FILLED = -2;

	/**
	 * File handle.
	 * @var resource
	 */
	private $handle;

	/**
	 * Creates a new object of type File.
	 *
	 * The specified path needn't exist and can be a relative or an absolute path.
	 *
	 * @param	string	Path to a file.
	 * @param	boolean	Set to false to disable ftp fallback, true to enable ftp fallback (default).
	 */
	public function __construct($path, $ftpFallback = true) {
		parent::__construct($path, $ftpFallback);
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
	 * File will be created and Permissions will be set to the specified permissions.
	 *
	 * If the file exists just the permissions will be set.
	 * See FileSystemBaseUnit::setPermissions() on how to specify the permissions correctly.
	 * This function will also return false if the chmod are not set correctly.
	 * If the containing folder does not exist, it won't be created!
	 *
	 * This function implements the ftp fallback!
	 *
	 * @see File::setPermissions()
	 * @param int Permissions to set for the file (default is 666)
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function create($permissions = 666) {
		$chmodResult = false;
		if ($this->exists() == false) {
			if ($this->write('') == true) {
				$chmodResult = $this->setPermissions($permissions);
			}
		}
		else {
			$chmodResult = $this->setPermissions($permissions);
		}

		/*
		 * We should check the permissions like below, but windows does not support chmods properly
		 * and therefore this condition would fail always for chmods other than 666 and 777.
		 *
		 * return ($this->exists() && $this->getPermissions() == $permissions);
		 */
		return ($this->exists() && $chmodResult);
	}

	/**
	 * Writes a string to a file.
	 *
	 * If the file is not writable the chmod is set to 666 before the write operation.
	 * The second parameter is the mode of writing (Binary or Unicode). You have to use one of the
	 * class constants File::BINARY or File::UNICODE (default).
	 *
	 * This function implements the ftp fallback!
	 *
	 * @param string String to write to the file.
	 * @param int Mode to write the file: Unicode (default) or Binary
	 * @param boolean Set this to true to append data to the file.
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 * @todo Implement ftp fallback (using tmpfile, pay attention with Binary/Unicode mode)
	 */
	public function write($content, $mode = self::UNICODE, $append = false) {
		$mode = ($mode == self::BINARY) ? FILE_BINARY : FILE_TEXT;
		if ($append == true) {
			$flags = $mode | FILE_APPEND;
		}
		else {
			$flags = $mode;
		}
		if ($this->writable() == false) {
			$this->setPermissions(666);
		}
		if (file_put_contents($this->path, $content, $flags) === false) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Reads a file (in three different ways).
	 *
	 * 1. If the first parameter is 0 (File::READ_STRING, default) the complete file will be
	 * returned at once as string. The function returns the content of the file as string, if the
	 * file does not exist or another error occured, boolean false will be returned. This is similar
	 * to PHP's file_get_contents() function.
	 *
	 * 2. If the first parameter is -1 (File::READ_LINES) or -2 (File::READ_LINES_FILLED), the
	 * function returns the file as an array. Each element of the array corresponds to a line in the
	 * file. The newline and/or carriage return chars will be removed from the line endings. If you
	 * use the option -2 (File::READ_LINES_FILLED) only lines with content will be returned, all
	 * empty lines will be skipped. The function returns the content of the file as array, if the
	 * file does not exist or another error occured, boolean false will be returned. Both options
	 * are similar to PHP's file() function.
	 *
	 * 3. If the first parameter is greater than zero (> 0), the function reads up to the specified
	 * amount of bytes from the file pointer opened before. When the end of file (EOF) is reached
	 * the function returns null, if the (partial) reading process was successful the function
	 * returns true, not the content. On failure false will be returned. The content is "returned"
	 * as the third parameter of this function.
	 *
	 * The third parameter of this fucntion is only used when the first parameter is greater than
	 * zero (see the third way to read a file).
	 *
	 * Warning: This function may return Boolean FALSE, but may also return a non-Boolean value
	 * which evaluates to FALSE, such as 0 or "". Use the === operator for testing the return value
	 * of this function.
	 *
	 * The second parameter is the mode of reading (Binary or Unicode). You have to use one of the
	 * class constants File::BINARY or File::UNICODE (default).
	 *
	 * Examples:
	 * <code>
	 *	$f = new File('file.txt');
	 *	while ($f->read(1024, File::UNICODE, $data) === true) {
	 *		echo $data;
	 *	}
	 * </code>
	 * <code>
	 *	$f = new File('blank.gif');
	 *	$content = $f->read(File::READ_STRING, File::BINARY);
	 *  if ($content !== false) {
	 *    echo $content;
	 *  }
	 * </code>
	 * <code>
	 *	$f = new File('core.log');
	 *	$content = $f->read(File::READ_LINES_FILLED);
	 *  if ($content !== false) {
	 *    print_r($content);
	 *  }
	 * </code>
	 *
	 * @param int Type to read the file, default: 0 (File::READ_STRING)
	 * @param int Mode to read a file: Unicode (default) or Binary
	 * @param string Only used when first parameter is > 0, contains partial content of the file.
	 * @return mixed Requested Content or false (or only the bool state when first param is > 0)
	 * @todo Check whether $fopenMode is really correct for php 6 binary/unicode handling
	 */
	public function read($type = File::READ_STRING, $mode = self::UNICODE, &$data = null) {
		if ($this->readable() == false) {
			return false;
		}

		if ($mode == self::BINARY) {
			$mode = FILE_BINARY;
			$fopenMode = 'rb';
		}
		else {
			$mode = FILE_TEXT;
			$fopenMode = 'r';
		}

		if ($type == self::READ_STRING) {
			$contents = file_get_contents($this->path, $mode);
			if ($contents !== false) {
				return $contents;
			}
			else {
				return false;
			}
		}
		elseif ($type == self::READ_LINES || $type == self::READ_LINES_FILLED) {
			// When filesize is > 8 MB we use another method to read the file into an array.
			if ($this->size() <= 8*1024*1024) {
				if ($type == self::READ_LINES_FILLED) {
					$flags = FILE_SKIP_EMPTY_LINES | $mode;
				}
				else {
					$flags = $mode;
				}
				$array = file($this->path, $flags);
			}
			else {
				$array = array();
				$this->handle = fopen($this->path, $fopenMode);
				if (is_resource($this->handle) == false) {
					return false;
				}
				while (feof($this->handle) == false) {
					$line = fgets($this->handle);
					$line = Strings::trimLineBreaks($line);
					if ($type == self::READ_LINES || !empty($line)) {
						$array[] = $line;
					}
				}
				fclose($this->handle);
			}
			// Remove line breaks
			$array = array_map(array('Strings', 'trimLineBreaks'), $array);
		}
		elseif ($type > 0) {
			if (is_resource($this->handle) == false) {
				$this->handle = fopen($this->path, $fopenMode);
				if (is_resource($this->handle) == false) {
					return false;
				}
			}
			if (feof($this->handle) == false) {
				$data = fread($this->handle, $type);
				if ($data !== false) {
					return true;
				}
				else {
					fclose($this->handle);
					return false;
				}
			}
			else {
				// Reached end of file (EOF)
				fclose($this->handle);
				return null;
			}
		}
		else {
			FileSystem::getDebug()->addText(
				"The specified type ({$type}) to read the file '{$this->path}' is not supported."
			);
			return false;
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
	 * Attempts to set the access and modification times of the file.
	 * 
	 * Note that both times will be set regardless of the number of specified parameters.
	 * The default value for both parameters is the current timestamp returned by time().
	 * If the file does not exist, it won't be created and false will be returned.
	 *
	 * This function has some drawbacks as it relies on the touch() function of PHP, see the
	 * corresponding documentation page for more information.
	 *
	 * @see http://www.php.net/touch
	 * @param int Timestamp for Access Time
	 * @param int Timestamp for Modification Time
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setTime($accessTime = null, $modTime = null) {
		if ($this->exists()) {
			$accessTime = ($accessTime !== null) ? $accessTime : time();
			$modTime = ($modTime !== null) ? $modTime : time();
			return touch($this->file, $modTime, $accessTime);
		}
		else {
			return false;
		}
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
		if ($this->exists() == false) {
			return true;
		}

		if (unlink($this->path) == true) {
			return true;
		}
		elseif ($this->ftp == true) {
			$ftp = FileSystem::initializeFTP();
			if ($ftp !== null) {
				return $ftp->delete($this->ftpPath());
			}
		}
		return false;
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

		if (copy($this->path, $dest) == true) {
			return true;
		}
		elseif ($this->ftp == true) {
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
	 * Checks whether a file exists or not.
	 *
	 * @return boolean Returns TRUE if the file exists; FALSE otherwise.
	 */
	public function exists() {
		return (file_exists($this->path) && is_file($this->path));
	}

}
?>
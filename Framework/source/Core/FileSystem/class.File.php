<?php
/**
 * Read complete file at once. Value is: -1
 */
define('FILE_COMPLETE', -1);

/**
 * Read each line into an entry to an array with the newline/carriage return still attached. Value is: -2
 */
define('FILE_LINES', -2);

/**
 * Read each line into an entry to an array, but trims the attached newline/carriage return. Value is: -3
 */
define('FILE_LINES_TRIM', -3);

Core::loadClass('Core.FileSystem.CHMOD');
Core::loadClass('Core.FileSystem.FileSystem');

/**
 * File functions.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 */
class File {

	/**
	 * Time the file was modified last. Value is: 1
	 */
	const FILE_EDIT = 1;

	/**
	 * Time the file was accessed last. Value is: 2
	 */
	const FILE_ACCESS = 2;

	/**
	 * Given Path to file.
	 * @var string
	 */
	private $path;
	/**
	 * File handle.
	 * @var resource
	 */
	private $handle;

	/**
	 * Creates a new object of type File.
	 *
	 * The file given as parameter must not exist.
	 * The file can be a relative or a absolute path.
	 *
	 * @param	string	Path to a file.
	 */
	public function __construct($file) {
		$this->path = $file;
		$this->handle = null;
	}

	/**
	 * Closes all open file handles.
	 */
	public function __destruct() {
		Core::destruct();
		if (is_resource($this->handle) == true) {
			fclose($this->handle);
		}
	}

	/**
	 * Returns the complete content of the file if existing.
	 *
	 * If the file does not exist, an empty string will be returned.
	 *
	 * @return	string	Contents of the file as a string.
	 */
	public function __toString() {
		if ($this->exists() == true) {
			return $this->read();
		}
		else {
			return '';
		}
	}

	/**
	 * Returns the path of the file how it was specified before.
	 *
	 * @return string Returns variable $path.
	 */
	public function relPath() {
		return $this->path;
	}

	/**
	 * Returns canonicalized absolute pathname to the file.
	 *
	 * This works with non-existant paths.
	 *
	 * @return string
	 */
	public function absPath() {
		return FileSystem::realPath($this->path);
	}

	/**
	 * Returns object of type Folder that represents the folder the file is located in.
	 *
	 * @return Folder	Directory the file is in
	 */
	public function dir() {
		$path = dirname($this->path);
		return new Folder($path);
	}

	/**
	 * Returns the filename.
	 *
	 * The parameter specifies if the extension will be returned after the filename.
	 * If the parameter is true (which is the default behaviour) the extension will be returned, if the parameter is false it will be removed.
	 *
	 * @param 	boolean	With extension (true) ot without (false).
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
	 * Returns the extension of the file.
	 *
	 * Conversion lowercase will be done.
	 *
	 * @param 	boolean	true to add leading dot before extension, false for plain extension
	 * @return	string Extension
	 */
	 public function extension($dot = false) {
	 	$extension = pathinfo($this->path, PATHINFO_EXTENSION);
	 	return iif($dot, '.').strtolower($extension);
	 }

	/**
	 * Returns the size of the file in bytes, or FALSE in case of an error.
	 *
	 * Note: Because PHP's integer type is signed and many platforms use 32bit integers,
	 * size() may return unexpected results for files which are larger than 2GB.
	 *
	 * @return	int Size of file in bytes
	 */
	 public function size() {
		if ($this->exists() == false) {
			return false;
		}
	 	return @filesize($this->path);
	 }

	/**
	 * File will be created.
	 *
	 * If the file exists, the command will be ignored and true will be returned.
	 *
	 * @returns	boolean Returns TRUE on success or FALSE on failure.
	 */
	public function create() {
		if ($this->exists() == false) {
			return $this->truncate();
		}
		else {
			return true;
		}
	}

	/**
	 * File will be cleared.
	 *
	 * After calling this command the file is empty.
	 * If the file does not exist, the file will be created.
	 *
	 * @returns	boolean Returns TRUE on success or FALSE on failure.
	 */
	public function truncate() {
		return $this->write('');
	}

	/**
	 * Tells whether the file is readable.
	 *
	 * @return	boolean	Returns TRUE if the file exists and is readable.
	 */
	public function readable() {
		return is_readable($this->path);
	}

	/**
	 * Tells whether the file is executable.
	 *
	 * @return	boolean	Returns TRUE if the file exists and is executable.
	 */
	public function executable() {
		return is_executable($this->path);
	}

	/**
	 * Tells whether the file is writable.
	 *
	 * @return	boolean	Returns TRUE if the file exists and is writable.
	 */
	public function writable() {
		return is_writable($this->path);
	}

	/**
	 * Attempts to change the mode of the file to the given mode in the CHMOD-Object.
	 *
	 * Does nothing when the files is not existant.
	 *
	 * Example:
	 * <code>
	 * $f = new File('test.txt');
	 * $mode = new CHMOD('666');
	 * $f->setCHMOD($mode);
	 * </code>
	 *
	 * @param	CHMOD	Sets CHMOD with object of type CHMOD.
	 */
	public function setChmod(CHMOD $chmod) {
		if ($this->exists() == true) {
			@chmod($this->path, $chmod->getDecimal());
		}
	}

	/**
	 * Returns the current CHMOD of this file as Object of type CHMOD.
	 *
	 * @return	CHMOD	Object of type CHMOD or null if file does not exist.
	 */
	public function getChmod() {
		if ($this->exists() == false) {
			return null;
		}
		$mode = new CHMOD();
		$mode->read($this->path);
		return $mode;
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
	 */
	public function read($type = FILE_COMPLETE) {
		if ($this->readable() == false) {
			return null;
		}
		if ($type == FILE_COMPLETE) {
			$contents = file_get_contents($this->path);
			if ($contents != false) {
				return $contents;
			}
			else {
				return null;
			}
		}
		elseif ($type == FILE_LINES || $type == FILE_LINES_TRIM) {
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
			Core::throwError('The specified type ('.$type.') to read the file "'.$this->path.'" is not supported.');
			return null;
		}
	}

	/**
	 * Checks whether an file exists or not.
	 *
	 * @return	boolean	Returns TRUE if the file exists; FALSE otherwise.
	 */
	public function exists() {
		return (file_exists($this->path) && !is_dir($this->path));
	}

	/**
	 * Makes a copy of this file to the specified destination.
	 *
	 * Returns a new object of type File when the file was copied successfully.
	 * Returns null if an error occured.
	 *
	 * @param mixed Returns a object if successfull or null if not.
	 */
	public function copy($to) {
		if($this->exists() == true && @copy($this->path, $to) == true) {
			return new File($to);
		}
		else {
			return null;
		}
	}

	/**
	 * Attempts to move the file to the specified path.
	 *
	 * After executing this method successfully, this object will point to the new file and open file pointers are closed!
	 * To just rename a file please consider using the rename() function.
	 *
	 * @see File::rename()
	 * @param string Path to the new location.
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function move($path) {
		if ($this->exists() == false) {
			return false;
		}
		if (is_uploaded_file($this->path) == true) {
			if (file_exists($path) == true) {
				$bool = false;
			}
			else {
				$bool = @move_uploaded_file($this->path, $path);
			}
		}
		else {
			$bool = @rename($this->path, $path);
		}
		if ($bool == true) {
			if (is_resource($this->handle) == true) {
				fclose($this->handle);
			}
			$this->path = $path;
		}
		return $bool;
	}

	/**
	 * Attempts to rename the file to newname.
	 *
	 * The parameter newname should only consists of the pure filename without a path!
	 * To move and rename a file use method move().
	 * After executing this method successfully, this object will point to the new file and open file pointers are closed!
	 *
	 * @see File::move()
	 * @param string New name for the file
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function rename($newname) {
		if ($this->exists() == false) {
			return false;
		}
		$newname = dirname($this->path).DIRECTORY_SEPARATOR.basename($newname);
		$bool = @rename($this->path, $newname);
		if ($bool == true) {
			if (is_resource($this->handle) == true) {
				fclose($this->handle);
			}
			$this->path = $newname;
		}
		return $bool;
	}

	/**
	 * Sets access and modification time of file
	 *
	 * Attempts to set the access and modification times of the file to the value given in time.
	 * If time is not supplied, the current system time is used.
	 * If the second parameter, is present, the access time of the given filename is set to the value of atime.
	 * Note that the access time is always modified, regardless of the number of parameters.
	 *
	 * If the file does not exist, it will not be created and false will be returned.
	 *
	 * @param 	int	Modification time to set.
	 * @param 	int	Access time to set.
	 * @return	boolean	Returns TRUE on success or FALSE on failure.
	 */
	public function touch($time = null, $atime = null) {
		if ($this->exists() == true) {
			if (is_int($time) == false) {
				$time = time();
			}
			if (is_int($atime) == false) {
				$atime = time();
			}
			return touch($this->path, $time, $atime);
		}
		else {
			return false;
		}
	}

	/**
	 * Deletes a file.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function delete() {
		$bool = @unlink($this->path);
		clearstatcache();
		return $bool;
	}

	/**
	 * Writes a string to a file.
	 *
	 * Before writing to the file, it will be created if needed and the chmod will be set to 666.
	 *
	 * @param	string	String to write to the file.
	 * @param 	boolean Set this to true to append data to the file.
	 * @return	boolean Returns TRUE on success or FALSE on failure.
	 */
	public function write($content, $append = false) {
		if ($append == true) {
			$append = FILE_APPEND;
		}
		else {
			$append = null;
		}
		$this->setChmod(new CHMOD('666'));
		if (@file_put_contents($this->path, $content, $append) === false) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Writes an array to a file.
	 *
	 * Set the second parameter to true to serialize the array before saving it to the file.
	 * If it it set to false (standard) each array entry will be written to a new line separated by CR LF.
	 *
	 * @see 	http://www.php.net/serialize
	 * @param	array	Array or string to write to the file.
	 * @param 	boolean Set this to true to serialize the array.
	 * @return	boolean Returns TRUE on success or FALSE on failure.
	 */
	public function writeArray($content, $serialize = false) {
		if($serialize == true) {
			$content = serialize($content);
		}
		else {
			$content = implode("\r\n", $content);
		}
		$this->write($content);
	}

	public function readArray() {
		return unserialize($this->read());
	}

	/**
	 * Gets the time of the modification or the last access of the file as UNIX Timestamp.
	 *
	 * If the parameter is 1 (Folder::FILE_EDIT) or if no parameter is given, the time the file was last modified wil be returned.
	 * If the parameter is 2 (Folder::FILE_ACCESS) the time the file was last accessed will be returned.
	 *
	 * If the file does not exist null will be returned.
	 *
	 * @param int $type
	 */
	public function time($type = self::FILE_EDIT) {
		if ($this->exists() == false) {
			return null;
		}
		if ($type == self::FILE_ACCESS) {
			return fileatime($this->path);
		}
		else {
			return filemtime($this->path);
		}
	}

	/**
	 * Returns a new resource handler to use for php-file-functions and closes the old one (if existant).
	 *
	 * The mode-parameter uses the same values you can use for the php function fopen().
	 * Standard parameter is "a+".
	 *
	 * On failure null will be returned.
	 *
	 * When you are using the methods File::write(), File::move() or File::rename() the internal file handler will be destroyed!
	 *
	 * @see fopen()
	 * @param string Mode for reading/writing.
	 * @return resource File resource
	 */
	public function getResource($mode = 'a+') {
		if (is_resource($this->handle) == true) {
			fclose($this->handle);
		}
		$handle = @fopen($this->path, $mode);
		if (is_resource($handle) == true) {
			$this->handle = $handle;
			return $this->handle;
		}
		else {
			return null;
		}
	}

}
?>

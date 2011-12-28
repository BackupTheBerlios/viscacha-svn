<?php
Core::loadClass('Core.FileSystem.CHMOD');
Core::loadClass('Core.FileSystem.FileSystem');

/**
 * Folder functions.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 */
class Folder {

	/**
	 * Given Path to folder.
	 * @var string
	 */
	private $path;

	/**
	 * Creates a new object of type Folder.
	 *
	 * The folder given as parameter must not exist.
	 * The folder can be a relative or a absolute path.
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
	 * Returns the canonicalized absolute pathname to the folder.
	 *
	 * @return	string
	 * @see	Folder::absPath()
	 */
	public function __toString() {
		return $this->absPath();
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
	 * Returns the name of the folder.
	 *
	 * @return	string	Foldername
	 */
	public function name() {
 		return basename($this->absPath());
	}

	/**
	 * Folder will be created and CHMOD will be set to 777.
	 *
	 * If the folder exists, the command will be ignored and true will be returned.
	 *
	 * @returns	boolean Returns TRUE on success or FALSE on failure.
	 */
	public function create() {
		if ($this->exists() == false) {
			return @mkdir($this->path, 0777, true);
		}
		else {
			return true;
		}
	}

	/**
	 * Folder will be cleared.
	 *
	 * After calling this command the folder is completely empty.
	 *
	 * @returns	boolean Returns TRUE on success or FALSE on failure.
	 */
	public function clear() {
		foreach ($this->getContents() as $content) {
			if ($content instanceof File || $content instanceof Folder) {
				$content->delete();
			}
		}
		return $this->isEmpty();
	}

	/**
	 * This returns an array with the folders and files of the directory.
	 *
	 * The returned array will contain objects of the type File and Folder.
	 * This is the merged result of the functions Folder::getFolders and Folder::getFiles.
	 *
	 * @return array
	 */
	public function getContents() {
		return array_merge($this->getFolders(), $this->getFiles());
	}

	/**
	 * Returns an array with all folders directly in this folder.
	 *
	 * All array elements are Folder objects.
	 * The keys of the array are the folder names.
	 * The array is sorted by the keys.
	 *
	 * @return array
	 */
	public function getFolders() {
		$path = $this->absPath().DIRECTORY_SEPARATOR;
		$d = dir($path);
		$folders = array();
		while (false !== ($entry = $d->read())) {
			if (is_dir($path.$entry) && $entry != '.' && $entry != '..') {
				$folders[$entry] = new Folder($path.$entry);
			}
		}
		ksort($folders);
		$d->close();
		return $folders;
	}

	/**
	 * Returns an array with all files directly in this folder.
	 *
	 * You can limit the files by giving an extension as paramter.
	 *
	 * All array elements are File objects.
	 * The keys of the array are the filenames.
	 * The array is sorted by the keys.
	 *
	 * @return array
	 */
	public function getFiles($ext = null) {
		$path = $this->absPath().DIRECTORY_SEPARATOR;
		$d = dir($path);
		if ($ext != null) {
			$ext = strtolower($ext);
		}
		$files = array();
		while (false !== ($entry = $d->read())) {
			$file = new File($path.$entry);
			if ($ext != null && $file->extension() != $ext) {
				continue;
			}
			if (is_file($path.$entry)) {
				$files[$entry] = $file;
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
	 * Tells whether the folder is readable.
	 *
	 * @return	boolean	Returns TRUE if the folder exists and is readable.
	 */
	public function readable() {
		return is_readable($this->path);
	}

	/**
	 * Tells whether the folder is executable.
	 *
	 * @return	boolean	Returns TRUE if the folder exists and is executable.
	 */
	public function executable() {
		return is_executable($this->path);
	}


	/**
	 * Tells whether the folder is writable.
	 *
	 * @return	boolean	Returns TRUE if the folder exists and is writable.
	 */
	public function writable() {
		return is_writable($this->path);
	}

	/**
	 * Attempts to change the mode of the folder to the given mode in the CHMOD-Object.
	 *
	 * Does nothing when the folder is not existant.
	 *
	 * Example:
	 * <code>
	 * $f = new Folder('./folder/');
	 * $mode = new CHMOD('777');
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
	 * Returns the current CHMOD of this folder as Object of type CHMOD.
	 *
	 * @return	CHMOD	Object of type CHMOD or null if folder does not exist.
	 */
	public function getChmod() {
		if ($this->exists() == false) {
			return null;
		}
		$mode = new CHMOD();
		$mode->read($this->path);
		return $mode;
	}

	public function read($type = FILE_COMPLETE) {

	}

	/**
	 * Checks whether the folder exists or not.
	 *
	 * @return	boolean	Returns TRUE if the folder exists, FALSE otherwise.
	 */
	public function exists() {
		return (file_exists($this->path) && is_dir($this->path));
	}

	/**
	 * Makes a copy of the whole folder to the specified destination.
	 *
	 * Returns a new object of type Folder when the folder was copied successfully.
	 * Returns null if an error occured.
	 *
	 * @param mixed Returns an object if successfull or null if not.
	 */
	public function copy($to) {
		if($this->exists() == true && $this->copyRecursive($this->path, $to) == true) {
			return new Folder($to);
		}
		else {
			return null;
		}
	}

	/**
	 * Copy a file, or recursively copy a folder and its contents
	 *
	 * @param       string   $source    Source path
	 * @param       string   $dest      Destination path
	 * @return      bool     Returns TRUE on success, FALSE on failure
	 */
	private function copyRecursive($source, $dest) {
	    if (is_file($source)) {
	    	$file = new File($source);
	        return $file->copy($dest);
	    }
	    elseif (is_dir($source)) {
		    if (!is_dir($dest)) {
		    	$folder = new Folder($dest);
		        if (!$folder->create()) {
		        	return false;
		        }
		    }
		    $folder = new Folder($source);
		    $ret = true;
		    if ($folder->exists() && !$folder->isEmpty()) {
		    	foreach ($folder->getContents() as $content) {
		    		if ($content instanceof File || $content instanceof Folder) {
		    			$name = $content->name();
		    			$ret2 = $content->copy("{$dest}/{$name}");
			            if ($ret2 == false) {
			            	$ret = false;
			            }
		    		}
		    	}
		    }
		    return $ret;
	    }
	    else {
	    	return false;
	    }
	}

	/**
	 * Attempts to move the folder to the specified path.
	 *
	 * After executing this method successfully, this object will point to the new folder!
	 * To just rename a file please consider using the rename() function.
	 *
	 * @see Folder::rename()
	 * @param string Path to the new location.
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function move($dest) {
		if ($this->exists() == false) {
			return false;
		}
		else {
			if (rename($this->path, $dest)) {
				$this->path = $dest;
				return true;
			}
			else {
				return false;
			}
		}
	}

	/**
	 * Attempts to rename the folder to newname.
	 *
	 * The parameter newname should only consists of the pure folder name without a path!
	 * To move and rename a folder use method move().
	 * After executing this method successfully, this object will point to the new folder!
	 *
	 * @see Folder::move()
	 * @param string New name for the folder
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function rename($newname) {
		if ($this->exists() == false) {
			return false;
		}
		$parentDir = dirname($this->absPath());
		$to = $parentDir.DIRECTORY_SEPARATOR.$newname;
		return $this->move($to);
	}

	/**
	 * Deletes the folder completely.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function delete() {
		$this->clear();
		$bool = rmdir($this->path);
		clearstatcache();
		return $bool;
	}

}
?>

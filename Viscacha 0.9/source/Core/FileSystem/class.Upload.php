<?php
/**
 * FileUpload - File upload helper for PHP 5 or higher
 *
 * Copyright (c) 1999, David Fox, Angryrobot Productions;
 * Copyright (c) 2000-2005, Dave Tufts, iMarc LLC;
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.<br />
 * 2. Redistributions in binary form must reproduce the above
 *    copyright notice, this list of conditions and the following
 *    disclaimer in the documentation and/or other materials provided
 *    with the distribution.<br />
 * 3. Neither the name of author nor the names of its contributors
 *    may be used to endorse or promote products derived from this
 *    software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
 * TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
 * PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHOR OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @author		Dave Tufts [dt] <dave@imarc.net>
 * @author		David Fox [df]
 * @author		Fred LeBlanc [fl] <fred@imarc.net>
 * @author		William Bond [wb] <will@imarc.net>
 * @copyright	Copyright 1999 David Fox
 * @copyright	Copyright 1999, 2005, iMarc <info@imarc.net>
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * FileUpload - File upload helper for PHP 5 or higher
 *
 * Information: This class is named Folder because PHP has a built-in class named 'Directory'.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @author		Dave Tufts [dt] <dave@imarc.net>
 * @author		David Fox [df]
 * @author		Fred LeBlanc [fl] <fred@imarc.net>
 * @author		William Bond [wb] <will@imarc.net>
 * @since 		1.0
 * @version		5.3.0
 */
class Upload {

	// Mode how to handle upload if file with same name exists
	const OVERWRITE = 1;
	const RENAME = 2;
	const REJECT = 3;
	// Error codes
	const ERROR_NONE = 0;
	const ERROR_NO_FILE_UPLOADED = 1;
	const ERROR_FILESIZE_EXCEEDED = 2;
	const ERROR_IMAGESIZE_EXCEEDED = 3;
	const ERROR_INVALID_MIMETYPE = 4;
	const ERROR_FILE_EXISTS = 5;
	const ERROR_MOVE_FAILED = 6;
	const ERROR_INVALID_EXTENSION = 7;
	const ERROR_INPUT_MALFORMED = 8;

	/**
	 * Properties of a single file
	 *
	 * @var array
	 */
	private $file;

	/**
	 * Properties of all uploaded files (if one uploaded file, identical to $this->file)
	 *
	 * @var array
	 */
	private $file_array;

	/**
	 * Error code, accessable via Upload::getError();
	 *
	 * @var string
	 */
	private $error;

	/**
	 * Upload directory path
	 *
	 * @var string
	 */
	private $destination_dir;

	/**
	 * Mode to manage identically named files
	 * 1 or Upload::OVERWRITE, 2 or Upload::RENAME, 3 or Upload::REJECT
	 *
	 * @var int
	 */
	private $overwrite_mode;

	/**
	 * Case insensitive comma-speparated list of acceptable MIME types
	 *
	 * @var string
	 */
	private $acceptable_mime_types = array();

	/**
	 * Comma-speparated list of acceptable file extensions without leading dot
	 *
	 * @var array
	 */
	private $accept_extensions = array();

	/**
	 * Maximum byte size
	 *
	 * @var int
	 */
	private $max_filesize;

	/**
	 * Max pixel width, if image
	 *
	 * @var int
	 */
	private $max_image_width;

	/**
	 * Max pixel height, if image
	 *
	 * @var int
	 */
	private $max_image_height;

	/**
	 * Default extension for upload(s) without leading zero
	 *
	 * @var string
	 */
	private $default_extension;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this->setErrorCode(self::ERROR_NONE); // clears errors
		$this->setDefaultExtension(''); // clear
		$this->setOverwriteMode(self::RENAME); // default mode, rename new files
	}

	/**
	 * Set mode to manage overwriting files.
	 *
 	 * Upload::OVERWRITE (or 1): overwrites file with the same name;
	 * Upload::RENAME (or 2): if file exists, rename upload file (default);
	 * Upload::REJECT (or 3): if file exists, do nothing / flag error
	 *
	 * @param  int One of the constants (or integers) explained in the description.
	 */
	public function setOverwriteMode($mode) {
		if ($mode == self::OVERWRITE || $mode == self::REJECT) {
			$this->overwrite_mode = $mode;
		}
		else {
			$this->overwrite_mode = self::RENAME;
		}
	}

	/**
	 * Set array of file endings to accept (without leading dot).
	 *
	 * Specifying NULL accepts all file extensions.
	 *
	 * @param mixed Array or comma separated string of acceptable file endings
	 */
	public function setAcceptExtensions($accept = null) {
		if ($accept === null) {
			$this->accept_extensions = array();
		}
		else {
			if (!is_array($accept)) {
				$accept = strtolower($accept);
				$accept = str_replace(' ', '', $accept);
				$accept = explode(",", $accept);
			}
			else {
				$accept = array_map('strtolower', $accept);
			}
			$this->accept_extensions = array_unique($accept);
		}
	}

	/**
	 * Set array MIME types to accept.
	 *
	 * Specifying NULL accepts all MIME types/files.
	 *
	 * @param mixed array or comma separated string of acceptable MIME types
	 */
	public function setAcceptableTypes($mime = null) {
		if ($mime === null) {
			$this->acceptable_mime_types = array();
		}
		else {
			if (!is_array($mime)) {
				$mime = explode(",", str_replace(' ', '', $mime));
			}
			$this->acceptable_mime_types = array_unique($mime);
		}
	}

	/**
	 * Set default filename extenstion without leading dot.
	 * 
	 * Last resort if uploaded without extension and PHP can't deduce extension based on MIME.
	 *
	 * @param string Default extension
	 */
	public function setDefaultExtension($default_extension) {
		$this->default_extension = trim($default_extension);
	}

	/**
	 * Set maximum upload filesize in bytes.
	 *
	 * PHP's configuration also controls maximum upload size; usually defaults to 2Mb or 4Mb.
	 * To upload larger files, change php.ini first.
	 * Set the max. file size to 0 to disable the file size check.
	 *
	 * @param int Filesize in bytes
	 */
	public function setMaxFilesize($size) {
		$this->max_filesize = (int) $size;
	}

	/**
	 * Set maximum pixel dimensions, ignored by non-image uploads.
	 * 
	 * Setting an entry to 0 means no restriction on the either width or height.
	 *
	 * @param int Maximum pixel width of image uploads
	 * @param int Maximum pixel height of image uploads
	 */
	public function setMaxImageSize($width, $height) {
		$this->max_image_width  = (int) $width;
		$this->max_image_height = (int) $height;
	}

	/**
	 * Initiate upload.
	 * 
	 * After setXXX() methods are called to set preferences, this is the only public method called
	 * by user.
	 *
	 * Returns (bool) false if error; (string) filename if single file uploaded; (array) filenames
	 * if multiple uploads.
	 *
	 * @param string HTML form field name of uploaded file
	 * @param string Upload directory
	 * @return mixed bool false on error, filename on single, filename array on multiple uploads.
	 */
	public function doUpload($filename, $destination = '') {
		$this->setErrorCode(self::ERROR_NONE);
		if (!$this->isUpload($filename)) {
			return false;
		}
		$this->destination_dir = $this->makePath($destination);
		$this->file_array = array();

		/*
		 * $_FILES acts differently if HTML form field, type="file",
		 * is single upload (name='foo') or array (name='foo[]'):
		 *
		 * 1. Single:
		 *   $_FILES = array([$formfield] => array([name] => ?, [type] => ?, [tmp_name] => ?, ...));
		 * 2. Array:
		 *   $_FILES = array(
		 *     [$formfield] => array(
		 *       [name] => array([0] => ?, [1] => ?), [type] => array([0] => ?, [1] => ?), ...
		 *   ));
		 */
		if (is_array($_FILES[$filename]['name'])) {

            $num_uploads = $this->countUploads($filename);

			// MULTIPLE - loop through each, copy to internal var
			for ($i=0; $i<$num_uploads; $i++) {
				$this->file = $this->newFileArray();
				if (isset($_FILES[$filename]['name'][$i])) {
					$this->file['name'] = $_FILES[$filename]['name'][$i];
				}
				if (isset($_FILES[$filename]['type'][$i])) {
					$this->file['type'] = $_FILES[$filename]['type'][$i];
				}
				if (isset($_FILES[$filename]['tmp_name'][$i])) {
					$this->file['tmp_name'] = $_FILES[$filename]['tmp_name'][$i];
				}
				if (isset($_FILES[$filename]['error'][$i])) {
					$this->file['error'] = $_FILES[$filename]['error'][$i];
				}
				if (isset($_FILES[$filename]['size'][$i])) {
					$this->file['size'] = $_FILES[$filename]['size'][$i];
				}

				if ($this->processUpload()) {
					$this->file_array[] = $this->file;
				}
			}
			if (!count($this->file_array)) { // no successful uploads
				if (!$this->error) {
					$this->setErrorCode(self::ERROR_NO_FILE_UPLOADED);
				}
				return false;
			}

			// success; return array of filenames
			$return = array();
			foreach ($this->file_array as $file_array) {
				$return[] = $file_array['name'];
			}
			return $return;
		}
		else {
			// SINGLE - copy $_FILES array to internal var
			$this->file = $this->newFileArray();
			if (isset($_FILES[$filename]['name'])) {
				$this->file['name'] = $_FILES[$filename]['name'];
			}
			if (isset($_FILES[$filename]['type'])) {
				$this->file['type'] = $_FILES[$filename]['type'];
			}
			if (isset($_FILES[$filename]['tmp_name'])) {
				$this->file['tmp_name'] = $_FILES[$filename]['tmp_name'];
			}
			if (isset($_FILES[$filename]['error'])) {
				$this->file['error'] = $_FILES[$filename]['error'];
			}
			if (isset($_FILES[$filename]['size'])) {
				$this->file['size'] = $_FILES[$filename]['size'];
			}

			if ($this->processUpload()) {
				$this->file_array = $this->file;
				return $this->file['name']; // success; return single filename
			}
			else {
				if (!$this->error) {
					$this->setErrorCode(self::ERROR_NO_FILE_UPLOADED);
				}
				return false;
			}
		}
	}

	/**
	 * Returns file array of file(s)
	 *  [name] => final filename,
	 *  [type] => MIME type,
	 *  [tmp_name] => PHP's temp name,
	 *  [error] => PHP's error code,
	 *  [size] => filesize in bytes,
	 *  [extension] => extension, without leading dot,
	 *  [width] => pixel width (if image),
	 *  [height] => pixel height (if image),
	 *  [basename] => basename without extension
	 *
	 * @return array File attributes
	 */
	public function getFileInfo() {
		return $this->file_array;
	}

	/**
	 * Returns error code.
	 *
	 * Error code is one of the Upload::ERROR_? class constants.
	 *
	 * @return int Error code
	 */
	public function getError() {
		return $this->error;
	}

    /**
	 * Resets the error message
	 */
	private function resetError() {
		$this->error = '';
	}

	/**
	 * Count uploads
	 *
	 * @param string
	 * @return int
	 */
	private function countUploads($filename) {
		if (isset($_FILES[$filename]['name']) && is_array($_FILES[$filename]['name']) ) {
			return count($_FILES[$filename]['name']);
		}
		else {
			return 1;
		}
	}

	/**
	 * Validate upload
	 *
	 * @param  string HTML form file field
	 * @return boolean TRUE, if HTML form is setup, and user uploaded a file
	 */
	private function isUpload($filename) {
		if (!isset($_SERVER['CONTENT_TYPE']) || !stristr($_SERVER['CONTENT_TYPE'], 'multipart/form-data')) {
			$this->setErrorCode(self::ERROR_INPUT_MALFORMED);
			return false;
		}
		if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {
			$this->setErrorCode(self::ERROR_INPUT_MALFORMED);
			return false;
		}
		if (!isset($_FILES) || !isset($_FILES[$filename]) ) {
			$this->setErrorCode(self::ERROR_NO_FILE_UPLOADED);
			return false;
		}
		if (!is_array($_FILES[$filename]) || empty($_FILES[$filename]['name'])  || empty($_FILES[$filename]['tmp_name']) || empty($_FILES[$filename]['size'])) {
			$this->setErrorCode(self::ERROR_NO_FILE_UPLOADED);
			return false;
		}
		return true;
	}

	/**
	 * Upload process manager; passes file through other methods
	 *
	 * @return boolean true, if file is uploaded and moved
	 */
	private function processUpload() {
		if (!isset($this->file) || !is_array($this->file)) {
			return false;
		}
		if (isset($this->file['tmp_name']) && $this->file['tmp_name']) {
			$this->cleanFile();

			if (!$this->checkFile()) {
				unset($this->file['tmp_name']); // clean up temp file
				return false;
			}

			if (!$this->moveFile()) {
				unset($this->file['tmp_name']); // clean up temp file
				return false;
			}

			return true;
		}
	}

	/**
	 * File clean up process manager
	 *
	 * @return boolean true, if file passes all checks
	 */
	private function cleanFile() {
		$this->file['name'] = $this->cleanFilename($this->file['name']);
		$this->file['width'] = $this->getImageWidth($this->file['tmp_name']);
		$this->file['height'] = $this->getImageHeight($this->file['tmp_name']);
		$this->file['type'] = strtolower($this->file['type']);

		// find the best extension; PHP can figure out what images SHOULD use, or try MIME,
		$extension = '';
		$uploadFile = new File($this->file['name']);
		$supplied_extension = $uploadFile->extension();
		$basename = $uploadFile->name(false);

		if (stripos($this->file['type'], "image/") === 0) {
			$image_extension = $this->getImageExtension($this->file['tmp_name']);

			if ($supplied_extension == $image_extension) {
				// filename's extension matched what PHP thought it should be
				$extension = $image_extension;
			}
			else {
				// mismatch: user uploaded 'foo.gif'; PHP thinks it's a jpg; call it 'foo.gif.jpg'
				$basename  = $basename.'.'.$supplied_extension;
				$extension = $image_extension;
			}
		}
		elseif ($supplied_extension) {
			$extension = $supplied_extension;
		}
		elseif ($this->file['type'] != 'application/octet-stream') {
			// suggest ext, based on MIME, but not for generel type application/octet-stream
			$extension = MimeType::getExtensions($this->file['type'], true);
		}

		// last resort, use default_extenstion by setDefaultExtension()
		if (!$extension) {
			$extension = $this->default_extension;
		}

		// correct file array
		$this->file['extension'] = $extension;
		$this->file['basename'] = $basename;
		$this->file['name'] = $this->file['basename'].'.'.$this->file['extension'];

		return true;
	}

	/**
	 * Clean up filename.
	 *
	 * @param string Initial filename
	 * @return string Normalized filename
	 */
	private function cleanFilename($name) {
		$name = Strings::replaceSpecialChars($name);
		// first char is dot or if all chars were stripped out by replaceSpecialChars()
		if ($name[0] == '.' || strlen($name) == 0) {
			$name = "upload".$name;
		}
		return $name;
	}

	/**
	 * Get image's pixel width.
	 *
	 * @param string Path and name to file
	 * @return int Number of pixels, if image; -1, if not
	 */
	private function getImageWidth($upload_file) {
		$image_properties = @getimagesize($upload_file);
		return ($image_properties !== false) ? $image_properties[0] : -1;
	}

	/**
	 * Get image's pixel height,
	 *
	 * @param string Path and name to file
	 * @return int Number of pixels, if image; -1, if not
	 */
	private function getImageHeight($upload_file) {
		$image_properties = @getimagesize($upload_file);
		return ($image_properties !== false) ? $image_properties[1] : -1;
	}

	/**
	 * Get image's proper extension without leading dot.
	 *
	 * @param string Path and name to file
	 * @return mixed Extension
	 */
	private function getImageExtension($upload_file) {
		$image_properties = @getimagesize($upload_file);
		if ($image_properties !== false) {
			return image_type_to_extension($image_properties[2]);
		}
		else {
			return '';
		}
	}

	/**
	 * Acceptance mangager; perorms checks to see if the file is acceptable.
	 *
	 * @return boolean
	 */
	private function checkFile() {
		// validate filesize
		if (!$this->checkFilesize($this->file['size'])) {
			return false;
		}
		// validate pixel dimensions
		if (!$this->checkImageSize($this->file['width'], $this->file['height'], $this->file['type'])) {
			return false;
		}
		// validate MIME
		if (!$this->checkMime($this->file['type'])) {
			return false;
		}
		// validate extension
		if (!$this->checkExtension($this->file['name'])) {
			return false;
		}
		return true; // passed all checks
	}

	/**
	 * Checks upload filesize against Upload::$max_filesize.
	 *
	 * @param int Upload filesize in byes
	 * @return boolean
	 */
	private function checkFilesize($size) {
		if($this->max_filesize > 0 && $size > $this->max_filesize) {
			$this->setErrorCode(self::ERROR_FILESIZE_EXCEEDED);
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Checks pixel dimension against class vars max_image_width/height.
	 *
	 * @param int Number of pixels in upload width
	 * @param int Number of pixels in upload height
	 * @param string MIME type of upload; only checks if MIME starts with "image/"
	 * @return boolean
	 */
	private function checkImageSize($width, $height, $type) {
		if (stripos($type, "image/") === 0) {
			if ($this->max_image_width > 0 && $width > $this->max_image_width) {
				$this->setErrorCode(self::ERROR_IMAGESIZE_EXCEEDED);
				return false;
			}
			if ($this->max_image_height > 0 && $height > $this->max_image_height) {
				$this->setErrorCode(self::ERROR_IMAGESIZE_EXCEEDED);
				return false;
			}
		}
		return true;
	}

	/**
	 * Checks MIME type against class var for acceptable MIME types.
	 *
	 * @param string MIME type of uploaded file
	 * @return boolean
	 * @todo incorporate PECL/fileinfo or PEAR/Mime_type; compare supplied MIME type from browser,
	 *       with what PHP returns; for now, 'trust' the MIME from user's browser. [dt, 2005-12-27]
	 * @todo Check implementation
	 */
	private function checkMime($mime) {
		if (!empty($this->acceptable_mime_types)) {
			if (!trim($mime['type'])) { // browser didn't send mime type; reject file
				$this->setErrorCode(self::ERROR_INVALID_MIMETYPE);
				return false;
			}
			$accept = false; // set to true is a MIME type matches
			foreach ($this->acceptable_mime_types as $acceptable_mime) {
				if (preg_match("|".preg_quote($acceptable_mime)."|i", $mime)) {
					$accept = true;
				}
			}
			if (!$accept) {
				$this->setErrorCode(self::ERROR_INVALID_MIMETYPE);
				return false;
			}
		}
		return true;
	}

	/**
	 * Checks upload filename type against class var for acceptable extension(s).
	 *
	 * @param string Filename of uploaded file
	 * @return boolean
	 */
	private function checkExtension($filename) {
		if (count($this->accept_extensions) == 0) {
			return true;
		}
		$file = new File($filename);
		if (in_array($file->extension(), $this->accept_extensions)) {
			return true;
		}
		else {
			$this->setErrorCode(self::ERROR_INVALID_EXTENSION);
			return false;
		}
	}

	/**
	 * Moves $this->file to $this->destination_dir, renames file if necessary
	 *
	 * @return boolean
	 */
	private function moveFile() {
		// error set somewhere else, exit
		if ($this->error) {
			return false;
		}

		switch($this->overwrite_mode) {
			case self::OVERWRITE:
				// overwrite file with same name
				// Nothing to do in this case
			break;
			case self::REJECT:
				// if filename exists, do nothing / flag error
				$path = $this->destination_dir.Folder::SEPARATOR.$this->file['name'];
				if (file_exists($path)) {
					$this->setErrorCode(self::ERROR_FILE_EXISTS);
					return false;
				}
			break;
			default:
				// if file exists, rename upload file
				$copy = '';
				$n = 1;
				$prefix = $this->destination_dir.Folder::SEPARATOR.$this->file['basename'];
				while(file_exists($prefix.$copy.'.'.$this->file['extension'])) {
					$copy = "_{$n}";
					$n++;
				}
				$this->file['basename'] = $this->file['basename'].$copy;
				$this->file['name'] = $this->file['basename'].'.'.$this->file['extension'];
			break;
		}
		$destination = $this->destination_dir.Folder::SEPARATOR.$this->file['name'];
		$source = new File($this->file['tmp_name']);
		if ($source->moveUploaded($destination) == false) {
			$this->setErrorCode(self::ERROR_MOVE_FAILED);
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Returns nicely formated directory path.
	 *
	 * @param string Initial path
	 * @return string Path, without trailing slash
	 */
	private function makePath($path) {
		$path = FileSystem::adjustTrailingSlash$path);
		if (strlen($path) == 0) {
			$path = ".";
		}
		if (!preg_match("/(^\.|\/)/", $path)) {
			$path = "./".$path;
		}
		return $path;
	}

	/**
	 * Creates empty file array for single upload.
	 *
	 * @return array empty file array
	 */
	private function newFileArray() {
		return array(
			'name' => '', // file name
			'type' => '', // MIME type
			'tmp_name' => '', // upload/tmp name; from PHP's $_FILES array
			'error' => '', // PHP error code; from PHP's $_FILES array
			'size' => '', // filesize in bytes
			'extension' => '', // file extension without leading dot
			'width' => '', // if image, pixel width
			'height' => '', // if image, pixel height
			'basename' => '' // file name preceding extension
		);
	}

	/**
	 * Returns correct error message based on language and code
	 *
	 * @param  int Error Code (one of the ERROR_? class constants)
	 * @todo Think about the error message handling when implementing the Locale classes
	 */
	private function setErrorCode($error_code) {
		$this->error = $error_code;
		/*
		$error_message    = array();
		$error_message[0] = ''; // no error
		$error_message[1] = "No file was uploaded";
		$error_message[2] = "Maximum file size exceeded. File may be no larger than " . $this->max_filesize/1000 . " KB (" . $this->max_filesize . " bytes).";
		$error_message[3] = "Maximum image size exceeded. Image may be no more than " . $this->max_image_width . " x " . $this->max_image_height . " pixels.";
		$error_message[4] = "Only " . implode(" or ", $this->acceptable_mime_types) . " files may be uploaded.";
		$error_message[5] = "File '" . $this->destination_dir . "/" . $this->file['name'] . "' already exists.";
		$error_message[6] = "Permission denied. Unable to copy file to '" . $this->destination_dir . "/" . "'";
		$error_message[7] = "Only Filenames ending with " . implode(" or ", $this->getAcceptExtensions()) . " may be uploaded.";
		$error_message[8] = "Setup error. Form must contain: method=\"POST\" enctype=\"multipart/form-data\"";
		 */
	}
}
?>
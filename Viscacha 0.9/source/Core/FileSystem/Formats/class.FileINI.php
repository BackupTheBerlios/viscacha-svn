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
 * @author		Justin Frim <phpcoder@cyberpimp.pimpdomain.com>
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @copyright	Copyright (c) 2005, Justin Frim
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

Core::loadClass('Core.FileSystem.File');

/**
 * Implementation of the INI file format for read ans write access.
 *
 * How the INI file format is implemented:<br />
 * Sections can use any character excluding ASCII control characters and ASCII DEL. You may even
 * use [ and ] characters as literals! Keys can use any character excluding ASCII control
 * characters, ASCII DEL, ASCII equals sign (=), and not start with the user-defined comment
 * character. If file mode is set to Binary, the values are saved binary safe (encoded with C-style
 * backslash escape codes). Values may be enclosed by double-quotes (to retain leading & trailing
 * spaces). User-defined comment character can be any non-white-space ASCII character excluding
 * ASCII opening bracket ([).
 *
 * This class reads sections automatically.
 * If there is a comment at the beginning of the file, it can be read and it will be automatically
 * written back to the file (or it will be overwritten by a spcified comment). All other comments
 * are ignored in the parsing process and won't be written back.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @author		Justin Frim <phpcoder@cyberpimp.pimpdomain.com>
 * @since 		0.8
 * @see			http://www.php.net/manual/function.parse-ini-file.php
 * @todo		What do we do when we only need to parse a string (no file)
 */
class FileINI extends File {

	const CRLF = "\r\n";

	/**
	 * File read and write mode (Unicode or Binary)
	 * @var int
	 */
	private $mode;
	/**
	 * Char that comments start with
	 * @var string
	 */
	private $commentChar;
	/**
	 * Comment the files starts with (if any).
	 * @var array
	 */
	private $comment;

	/**
	 * Construct a new INI object for the specified file.
	 *
	 * The specified path needn't exist and can be a relative or an absolute path.
	 * The default comment char is set to ';'. Default mode for read and write access is Unicode.
	 *
	 * FTP fallback is activated automatically for this class.
	 *
	 * @param string Path to an INI file or null
	 */
	public function __construct($file) {
		parent::__construct($file, true);
		$this->comment = array();
		$this->setCommentChar();
		$this->setFileMode();
	}

	/**
	 * Sets the char a comment starts with.
	 *
	 * User-defined comment character can be any non-white-space ASCII character excluding ASCII
	 * opening bracket ([). If you specify more than one char in the string only the first char will
	 * be used.
	 *
	 * @param string Character to start comments with
	 */
	public function setCommentChar($char = ';') {
		if (isset($char[0]) == true && preg_match('/[\0-\37]|\[|\177/', $char[0]) == 0) {
			$this->commentChar = $char[0];
		}
	}

	/**
	 * Sets the read and write mode.
	 *
	 * Use either File::UNICODE (default) or File::BINARY as parameter.
	 */
	public function setFileMode($mode = self::UNICODE) {
		$this->mode = $mode;
	}

	/**
	 * Returns the initial comment from the file.
	 *
	 * If there is a comment at the beginning of the file, it can be read and this function will
	 * return it, but you have to call FileINI::parse() or FileINNI::parseString() before!
	 * If you specify a comment for the FileINI::writeArray() or FileINI::transformArray() process,
	 * that comment will be returned here.
	 * The comment is the last comment found/specified in one of the four mentions methods.
	 *
	 * If a multiline comment/an aray is specified the comment will be joined together with white
	 * spaces.
	 *
	 * The comment below will be transformed to "This is a multiline comment with three lines!".
	 * <code>
	 * ; This is a
	 * ; multiline comment
	 * ; with three lines!
	 * </code>
	 *
	 * @return string Comment
	 */
	public function getComment() {
		return $this->comment;
	}

	/**
	 * Reads and parses an INI file.
	 *
	 * Returns a multidimensional array with the parsed content of the file or null on failure.
	 * This function is case-sensitive when reading sections and keys.
	 *
	 * @return array Array with the data or null on failure
	 */
	public function parse() {
		$content = $this->read(self::READ_STRING, $this->mode);
		if ($content !== false) {
			$data = $this->parseString($content);
			return $data;
		}
		return null;
	}

	/**
	 * Parses a string containing INI formatted data.
	 *
	 * Returns an array with the parsed content of the string or null on failure.
	 * This function is case-sensitive when reading sections and keys.
	 *
	 * @param string String containing INI data.
	 * @return array Array with the data or null on failure
	 * @author Justin Frim <phpcoder@cyberpimp.pimpdomain.com>
	 */
	public function parseString($str) {
		$array1 = Strings::toTrimmedArray($str);
		$array2 = array();
		// Reset comment etc.
		$this->comment = array();
		$section = '';
		$firstElementFound = false;

		foreach ($array1 as $line) {
			if (empty($line) == true) {
				// If line is empty jump to next line
				continue;
			}

			// Read the first char of the line to analyse type (section, comment, element)
			$firstchar = substr($line, 0, 1);
			if ($firstchar == $this->commentChar) {
				// It's a comment
				if ($firstElementFound == false) {
					// The comment belongs to the heading comment
					$this->comment[] = trim(substr($line, 1));
				}
			}
			else { // It's not a comment.
				// We have found the first element (section or key/value).
				// No more comments can be read, stop it...
				$firstElementFound = true;
				//It's an entry (not a comment and not a blank line)
				if ($firstchar == '[' && substr($line, -1, 1) == ']') {
					//It's a section
					$section = substr($line, 1, -1);
				}
				else {
					//It's a key...
					$delimiter = strpos($line, '=');
					if ($delimiter > 0) {
						//...with a value
						list($key, $value) = explode('=', $line, 2);
						$key = trim($key);
						$value = trim($value);
						if (substr($value, 0, 1) == '"' && substr($value, -1, 1) == '"') {
							$value = substr($value, 1, -1);
							$value = str_replace('\\r', "\r", $value);
							$value = str_replace('\\n', "\n", $value);
						}
						if ($this->mode == self::BINARY) {
							$value = stripcslashes($value);
						}
					}
					else {
						//...without a value
						$key = $line;
						$value = '';
					}
					if (empty($section)) {
						$array2[$key] = $value;
					}
					else {
						$array2[$section][$key] = $value;
					}
				}
			}
		}
		return $array2;
	}

	/**
	 * Transforms a multidimensional array (like arrays returned by FileINI::parse()) to a string.
	 *
	 * This function writes sections and keys case sensitive. Invalid characters are converted to
	 * ASCII dash/hyphen (-). Values are always enclosed by double-quotes. All line breaks are
	 * translated to CRLF.
	 *
	 * You can specify a comment to prepend before the file. This would overwrite a comment that
	 * was read before from the file. For a single line comment you can just specify a string that
	 * will be used, for a multiline comment you have to put each line into an array element. The
	 * comment must not contain line breaks, but if there are any, they will be replaced with a
	 * white space. If you specify an empty array no comment will be written, if you specify null
	 * (default) the comment read from the file will be used (if any). The comment char specified
	 * before ( FileINI::setCommentChar() ) will be used.
	 *
	 * @param array The data to transform
	 * @param mixed Comment to prepend before the file
	 * @return string The transformed data as CSV
	 * @author Justin Frim <phpcoder@cyberpimp.pimpdomain.com>
	 */
	public function transformArray(array $array, $comment = null) {
		// Handle comment parameter
		if (is_array($comment)) {
			$this->comment = $comment;
		}
		elseif ($comment !== null && !is_array($comment)) {
			$this->comment = array($comment);
		}

		$data = '';
		// Transform comment and add to ini
		if (count($this->comment) > 0) {
			foreach ($this->comment as $comtext) {
				$data .= $this->commentChar . Strings::replaceLineBreaks($comtext, ' ') . self::CRLF;
			}
		}

		// Transform data and add to ini
		foreach ($array as $sections => $items) {
			//Write the section
			if (isset($section)) {
				$data .= self::CRLF;
			}
			if (is_array($items)) {
				// Remove invalid chars from section name (\0-\37 is octal notation for ascii chars)
				$section = preg_replace('/[\0-\37]|\177/', "-", $sections);
				$data .= "[{$section}]" . self::CRLF;
				foreach ($items as $keys => $values) {
					// Remove invalid chars from key name
					$key = preg_replace('/[\0-\37]|=|\177/', "-", $keys);
					// Replace comment char at the beginning
		  			if (substr($key, 0, 1) == $this->commentChar) {
		  				$key = '-'.substr($key, 1);
		  			}
			  		$values = str_replace("\r", '\r', $values);
			  		$values = str_replace("\n", '\n', $values);
					if ($this->mode == self::BINARY) {
						$value = addcslashes($values, '');
					}
					// Will be ' keyname = "value"\r\n' (without ')
		  			$data .= ' ' . $key . ' = "' . $value . '"' . self::CRLF;
				}
			}
			else {
				$key = preg_replace('/[\0-\37]|=|\177/', "-", $sections);
		  		if (substr($key, 0, 1) == $this->commentChar) {
		  			$key = '-'.substr($key, 1);
		  		}
		  		$items = str_replace("\r", '\r', $items);
		  		$items = str_replace("\n", '\n', $items);
				if ($this->mode == self::BINARY) {
					$value = addcslashes($items, '');
				}
				// Will be 'keyname = "value"\r\n' (without ')
		  		$data .= $key . ' = "' . $value . '"' . self::CRLF;
			}
	  	}
		return $data;
	}

	/**
	 * Writes a multidimensional array (like arrays returned by FileINI::parse()) to a file.
	 *
	 * @param array The data to transform
	 * @return boolean true on success, false on failure.
	 */
	public function writeArray(array $data) {
		return $this->write($this->transformArray($data), $this->mode);
	}
}
?>
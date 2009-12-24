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

Core::loadClass('Core.FileSystem.File');

/**
 * Implementation of the CSV specifications.
 *
 * This class can read and write CSV files according to the RFC 4180.
 *
 * @todo		What about unicode/binary for reading/writing etc.?
 * @package		Core
 * @subpackage	FileSystem
 * @author		Stefan Tenhaeff
 * @author		Matthias Mohr
 * @since 		1.0
 * @see			http://tools.ietf.org/html/rfc4180
 */
class FileCSV extends File {

	/**
	 * Shall we read/write header data? true = yes, false = no
	 * @var boolean
	 */
	private $headers;
	/**
	 * Column separator
	 * @var string
	 */
	private $sep;
	/**
	 * Char the values are enclosed with (e.q. quotes).
	 * @var string
	 */
	private $quote;
	/**
	 * Use Excel compatibility mode (for reading)? true = yes, false = no
	 */
	private $compat;

	/**
	 * Construct a new CSV object for the specified file.
	 *
	 * The specified path needn't exist and can be a relative or an absolute path.
	 * The field separator is set to ',', the char the values are enclosed with is set to '"'.
	 * Excel compatibility mode is turned off by default.
	 *
	 * FTP fallback is activated automatically for this class.
	 *
	 * @param string Path to a CSV file
	 * @param boolean Shall we read/write header data? true = yes, false = no
	 */
	public function __construct($file, $headers = false) {
		parent::__construct($file, true);
		$this->headers = (boolean) $headers;
		$this->sep = ',';
		$this->quote = '"';
		$this->compat = false;
	}

	/**
	 * Specifies whether the script contains (or should contain) header data.
	 *
	 * Set the parameter to true to use headers, set to false to disallow headers.
	 *
	 * @param boolean Change the usage of headers (default: false)
	 */
	public function setHeaders($hasHeaders = false) {
		$this->headers = (boolean) $hasHeaders;
	}

	/**
	 * Returns whether the script reads/writes header data for the csv file.
	 *
	 * @return booelan Returns state of header handling
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * Sets the char the values are enclosed with (e.q. double quotes).
	 *
	 * @param string Char the values are enclosed with (default is double quotes).
	 */
	public function setQuote($quote = '"') {
		$this->quote = $quote;
	}

	/**
	 * Column separator for the csv file.
	 *
	 * @param string Column separator (default is comma).
	 */
	public function setSeparator($sep = ',') {
		$this->sep = $sep;
	}

	/**
	 * Enable/Disable Excel compatibility mode for reading.
	 *
	 * The compatinility mode only supports reading files created with excel, we do not write the
	 * data in the Excel way. We only write standard compliant csv data.
	 *
	 * @param boolean Use Excel compatibility mode (true = enable, false = disable)
	 */
	public function setCompat($compat) {
		$this->compat = (boolean) $compat;
	}

	/**
	 * Reads and parses a csv file.
	 *
	 * Returns a multidimensional array with the parsed content of the file or null on failure.
	 * If the usage of headers are enabled the header values are used as keys for the array.
	 *
	 * @return array Array with the data or null on failure
	 */
	public function parse() {
		$content = $this->read();
		if ($content !== false) {
			$data = $this->parseString($content);
			if ($data !== null) {
				return $data;
			}
		}
		return null;
	}

	/**
	 * Reads and parses a string containing csv formatted data.
	 *
	 * Returns a multidimensional array with the parsed content of the string or null on failure.
	 * If the usage of headers are enabled the header values are used as keys for the array.
	 *
	 * @param string String containing csv data.
	 * @return array Array with the data or null on failure
	 */
	public function parseString($str) {
		if (!is_string($str)) {
			return null;
		}
		$lines = Strings::toArray($str);
		$data = array();
		$header = array();
		$numLine = ($this->headers == true) ? -1 : 0;

		foreach($lines as $line) {
			$cols = $this->parseLine($line);
			if ($numLine >= 0) {
				$data[$numLine] = array();
			}
			$numCol = 0;
			foreach($cols as $col) {
				if($numLine == -1 && $this->headers == true) {
					$header[$numCol] = $col;
				}
				elseif($numLine >= 0 && $this->headers == true) {
					$data[$numLine][$header[$numCol]] = $this->unescape($col);
				}
				else {
					$data[$numLine][$numCol] = $this->unescape($col);
				}
				$numCol++;
			}
			$numLine++;
		}
		return $data;
	}

	/**
	 * Transforms a multidimensional array (like arrays returned by FileCSV::parse()) to a string.
	 *
	 * @param array The data to transform
	 * @return string The transformed data as CSV
	 */
	public function transformArray(array $data) {
		$content = array();
		foreach($data as $counter) {
			$content[] = implode($this->sep, $this->escape($counter));
		}
		return $content;
	}

	/**
	 * Writes a multidimensional array (like arrays returned by FileCSV::parse()) to a file.
	 *
	 * @param array The data to transform
	 * @return boolean true on success, false on failure.
	 */
	public function writeArray(array $data) {
		return $this->write($this->transformArray($data));
	}

	/**
	 * Splits a CSV line into an array.
	 *
	 * @param string String to parse
	 * @return array Parsed string as array
	 */
	private function parseLine($str){
		$sep = preg_quote($this->sep, '/');
		$quote = preg_quote($this->quote, '/');
		$expr = "/{$sep}(?=(?:[^{$quote}]*{$quote}[^{$quote}]*{$quote})*(?![^{$quote}]*{$quote}))/";
		return preg_split($expr, $str);
	}

	/**
	 * Escape csv data.
	 *
	 * @param mixed The data to escape
	 * @return string The escaped data
	 */
	private function escape($field) {
		if (is_array($field) == true) {
			$field = array_map(array($this, 'escape'), $field);
		}
		else {
			$field = str_replace(array("\r", "\n"), array("%x0D", "%x0A"), $field);
			if (strpos($field, $this->sep) !== false) {
				$field = str_replace($this->quote, $this->quote.$this->quote, $field);
				$field = $this->quote.$field.$this->quote;
			}
		}
		return $field;
	}


  	/**
	 * Unescape csv data.
	 *
	 * @param mixed The data to unescape
	 * @return string The unescaped data
	 */
	private function unescape($field) {
		if (is_array($field) == true) {
			$field = array_map(array($this, 'unescape'), $field);
		}
		else {
			if (!strlen($field)) {
				return $field;
			}

			$field = str_replace(array("%x0D", "%x0A"), array("\r", "\n"), $field);

			// Excel compatibility
			if ($this->compat == true && $field[0] == '=' && $field[1] == '"') {
				$field = str_replace('="', '"', $field);
			}

			// Get rid of escaping quotes
			$field_len = strlen($field);
			if ($this->quote && $field[0] == $this->quote && $field[$field_len - 1] == $this->quote) {
				$new = '';
				$prev = '';
				$c = '';
				for ($i = 0; $i < $field_len; ++$i) {
					$prev = $c;
					$c = $field[$i];
					// Deal with escaping quotes
					if ($c == $this->quote && $prev == $this->quote) {
						$c = '';
					}

					$new .= $c;
				}
				$field = substr($new, 1, -1);
			}

		}
		return $field;
	}
}

?>
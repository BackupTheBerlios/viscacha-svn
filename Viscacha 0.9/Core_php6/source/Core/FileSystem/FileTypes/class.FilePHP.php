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
 * This class implements the format of native php code for arrays.
 *
 * You can store and read native php code that defines an arrays.
 * Class works with optional ftp fallback if configured.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 */
class FilePHP extends File {

	private $varname;

	/**
	 * @param string Path to a PHP file
	 * @param string Variable name to read
	 */
	public function __construct($path, $varname) {
		parent::__construct($path);
		$this->varname = $varname;
	}

	/**
	 * Includes a php file and returns a specific variable file.
	 *
	 * The file has to be utf8 encoded or data can't be read correctly!
	 *
	 * @return array Array with the data or null on failure
	 */
	public function parse() {
		if ($this->exists()) {
			ob_start();
			include($this->path);
			ob_end_clean();

			if (isset(${$this->varname}) == true && is_array(${$this->varname}) == true) {
				return ${$this->varname};
			}
		}
		return null;
	}

	/**
	 * Writes an array as native php code to a file.
	 *
	 * The file will be saved in utf8 encoding.
	 *
	 * @param array The data to transform
	 * @return boolean true on success, false on failure.
	 */
	public function writeArray(array $data) {
		$content = '<?php'."\n";
		$content .= '$' . $this->varname . ' = ' . var_export($data, true) . ';' . "\n";
		$content .= '?>';

		return $this->file->write($content);
	}

}
?>
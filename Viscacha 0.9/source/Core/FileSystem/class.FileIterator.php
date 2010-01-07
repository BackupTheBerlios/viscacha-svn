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
 * File Iterator to iterate through files with filters.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 */
class FileIterator extends FileFolderIterator {

	protected $extension;

	/**
	 * Constructs a new FileIterator for a specified path (folder).
	 *
	 * The default behaviour is set. The iterator will return objects and no filter (neither RegExp
	 * nor extension) will be applied.
	 *
	 * @param string Path to a folder
	 */
	public function  __construct($path) {
		$this->extension = null;
		parent::__construct($path);
	}

	/**
	 * Sets an extension to check for.
	 *
	 * The extension check is made before the regexp filter.
	 */
	public function setExtensionFilter($extension = null) {
		if ($extension !== null) {
			$this->extension = strtolower($extension);
		}
		else {
			$this->extension = null;
		}
	}

	/**
	 * Checks whether a file can be used in this Iterator.
	 *
	 * Checks the name against the extension (first) and the RegExp (second) if specified.
	 *
	 * @param string File name to check
	 * @return boolean Returns true for a valid file, false for an invalid file.
	 */
	protected function matchesFilter($name) {
		if (is_file($this->path.$name) == false) {
			return false; // Reject: Not a file (inlcudes folders '.' and '..') or name is not given
		}
		if ($this->extension !== null && strtolower(pathinfo($name, PATHINFO_EXTENSION)) != $this->extension) {
			return false; // Reject files with wrong extension
		}
		if($this->filter !== null && preg_match($this->filter, $name) == 0) {
			return false; // Reject files not matching the filter
		}
		return true; // Accept all files that reach this return statement
	}

	/**
	 * Constructs a new File object for the specified path.
	 *
	 * @param string Path for the new object
	 * @return File
	 */
	protected function constructObject($path) {
		return new File($path);
	}

}
?>

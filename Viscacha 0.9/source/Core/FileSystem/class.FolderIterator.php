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
 * FolderIterator to iterate through folders.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 */
class FolderIterator extends FileFolderIterator {

	/**
	 * Checks whether a folder can be used in this Iterator.
	 *
	 * Checks the name against the filter etc.
	 *
	 * @param string Folder name to check
	 * @return boolean Returns true for a valid, false for an invalid folder.
	 */
	protected function matchesFilter($name) {
		if ($name == '.' || $name == '..' || is_dir($this->path.$name) == false) {
			return false; // Reject: This and the parent directory or no more entries or not a dir
		}
		elseif ($this->filter === null) {
			return true; // No filter, allow folder
		}
		elseif($this->filter !== null && preg_match($this->filter, $name) > 0) {
			return true; // Filter set and name matches the filter, allow folder
		}
		else {
			return false; // Filter set and name doesn't match the filter, reject folder
		}
	}

	/**
	 * Constructs a new Folder object for the specified path.
	 *
	 * @param string Path for the new object
	 * @return Folder
	 */
	protected function constructObject($path) {
		return new Folder($path);
	}

}
?>
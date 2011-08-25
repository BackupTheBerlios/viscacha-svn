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
 * @subpackage	Util
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License
 */

/**
 * Implementation for native php config files.
 *
 * This config is valid only during the page request and all data will be lost afterwards.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */
class PHPConfig implements ConfigHandler {

	// All private, they have to be declared again in subclasses or they would be overwritten
	private $data;
	private $hasChanged;
	private $file;

	public function __construct($file, $varname = 'config') {
		$this->file = new FilePHP($file, $varname);
		$this->data = array();
		$this->hasChanged = false;
		if ($this->file->exists() == true) {
			$this->load();
		}
		else {
			$this->create();
		}
	}

	public function __destruct() {
		FileSystem::resetWorkingDir();
		$this->save();
	}

	public function get($name) {
		list($group, $entry) = $this->parseName($name);
		if (empty($entry) == true && isset($this->data[$group]) == true) {
			return $this->data[$group];
		}
		elseif (empty($entry) == false && isset($this->data[$group][$entry]) == true) {
			return $this->data[$group][$entry];
		}
		else {
			return null;
		}
	}

	public function set($name, $value) {
		list($group, $entry) = $this->parseName($name);
		if (empty($entry) == true && is_array($value) == true) {
			foreach ($value as $entry => $entryValue) {
				$this->data[$group][$entry] = $entryValue;
			}
			if (count($value) > 0) {
				$this->hasChanged = true;
			}
			return true;
		}
		elseif (empty($entry) == false && is_scalar($value) == true) {
			$this->data[$group][$entry] = $value;
			$this->hasChanged = true;
			return true;
		}
		else {
			return false;
		}
	}

	public function rename($oldName, $newName) {
		$old = explode('.', $oldName);
		$new = explode('.', $newName);
		// It is only possible to move a group to a group and an entry
		if (count($old) == count($new)) {
			$data = $this->get($oldName);
			if ($data !== null && $this->set($newName, $data) == true) {
				if ($this->delete($oldName) == true) {
					return true;
				}
				$this->hasChanged = true;
			}
		}
		return false;
	}

	public function delete($name) {
		list($group, $entry) = $this->parseName($name);
		$this->hasChanged = true;
		if (empty($entry) == true) { // Group
			if (isset($this->data[$group])) {
				unset($this->data[$group]);
			}
			return (isset($this->data[$group]) == false);
		}
		else { // Entry
			if (isset($this->data[$group][$entry])) {
				unset($this->data[$group][$entry]);
			}
			return (isset($this->data[$group][$entry]) == false);
		}
	}

	public function create() {
		return $this->writeFile(array());
	}

	public function load() {
		if($this->file->exists() == true) {
			$this->data = $this->file->parse();
		}
		return false;
	}

	public function save() {
		if ($this->hasChanged == true) {
			return $this->writeFile($this->data);
		}
		else {
			return true;
		}
	}

	private function writeFile($data = null) {
		if ($data === null) {
			$data = $this->data;
		}

		return $this->file->writeArray($data);
	}

	/**
	 * Parses a name into two parts.
	 *
	 * @param string Name
	 * @return array
	 */
	protected function parseName($name) {
		return explode('.', $name, 2) + array(null, null);
	}

}
?>
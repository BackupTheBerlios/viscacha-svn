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
 * @subpackage	Util
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * Implementation of a temporary config.
 *
 * This config is valid only during the page request and all data will be lost afterwards.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */
class TempConfig implements ConfigHandler {

	private $data;

	public function __construct() {
		$this->load();
	}

	public function __destruct() {
		$this->save();
	}

	public function get($name) {
		$path = str_replace('.', '/', $name);
		if (Arrays::xPath($this->data, $path, $value) == true && is_scalar($value) == true) {
			return $value;
		}
		else {
			return null;
		}
	}

	public function getGroup($name) {
		$path = str_replace('.', '/', $name);
		if (Arrays::xPath($this->data, $path, $value) == true && is_array($value) == true) {
			return $value;
		}
		else {
			return null;
		}
	}

	public function set($name, $value) {
		if (is_scalar($value) == false || $this->get($name) === null) {
			return false;
		}
		else {
			$path = str_replace('.', '/', $name);
			return Arrays::xPath($this->data, $path, $value);
		}
	}

	public function setGroup($name, array $data) {
		if ($this->getGroup($name) === null) {
			return false;
		}
		else {
			$path = str_replace('.', '/', $name);
			return Arrays::xPath($this->data, $path, $data);
		}
	}

	public function rename($oldName, $newName) {
		$oldPath = str_replace('.', '/', $oldName);
		$newPath = str_replace('.', '/', $newName);
		if (Arrays::xPath($this->data, $oldPath, $data) == false) { // get data
			return false;
		}
		if (Arrays::xPath($this->data, $newPath, $data) == false) { // set data
			return false;
		}
		// Remove old data
		$this->delete($oldName);
		return true;
	}

	public function delete($name) {
		$this->deletePath($this->data, $name);
	}

	protected function load() {
		// Only create fresh array for temporary data
		$this->data = array();
	}

	public function save() {
		// Nothing to do for temporary data
	}

	private function deletePath(array &$data, $path) {
		if (strpos($path, '.') !== false) {
			list($key, $path) = explode('.', $path, 2);
			if (isset($data[$key]) == true && is_array($data[$key]) == true) {
				 $this->deletePath($data[$key], $path);
			}
		}
		else {
			if (isset($data[$path]) == true) {
				unset($data[$path]);
			}
		}
	}

}
?>
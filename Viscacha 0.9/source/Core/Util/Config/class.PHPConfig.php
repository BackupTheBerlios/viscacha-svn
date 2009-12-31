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

Core::loadInterface('Core.Util.Config.ConfigHandler');

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

	protected $data;
	protected $hasChanged;
	private $file;
	private $varname;

	public function __construct($file, $varname = 'config') {
		$this->file = new File($file);
		$this->varname = $varname;
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
		list($group, $entry) = explode('.', $name, 2);
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
		list($group, $entry) = explode('.', $name, 2);
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
		list($group, $entry) = explode('.', $name, 2);
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
			ob_start();
			include($this->file->relPath());
			ob_end_clean();

			if (isset(${$this->varname}) == true && is_array(${$this->varname}) == true) {
				$this->data = ${$this->varname};
				return true;
			}
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

		$content = '<?php'."\n";
		$content .= '$' . $this->varname . ' = ' . var_export($data, true) . ';' . "\n";
		$content .= '?>';

		$this->file->write($content);

	}

}
?>
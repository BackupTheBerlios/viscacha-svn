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
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * CacheObject to store general data.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		1.0
 */
class CacheObject {

	protected $name;
	protected $data;
	protected $maxAge;
	protected $driver;

	/**
	 * Constructs a new CacheObject
	 *
	 * @param string Cache name
	 * @param string Cache driver name (e.q. Serialize)
	 */
	public function __construct($name, CacheDriver $driver = null) {
		$this->name = $name;
		if ($driver === null) {
			$this->driver = CacheServer::getDriver();
		}
		else {
			$this->driver = $driver;
		}
		$this->data = null;
		$this->maxAge = null;
	}

	/**
	 * Call this function to get the cached data.
	 *
	 * If needed the data will be loaded from the cache file once.
	 * Function returns null if no data was specified before or no cache file exists.
	 *
	 * @return mixed Cached data or null
	 */
	public function get() {
		if ($this->data === null || $this->exists() == false) {
			$this->read();
		}
		else {
			$this->data = null; // No data specified before
		}
		return $this->data;
	}

	/**
	 * Sets the data that should be cached.
	 *
	 * For more information on the allowed data types please refer to the documentation of the
	 * CacheObject::save() method. No further checks will be made!
	 *
	 * @param	mixed	Data to be cached
	 * @see		CacheObject::save()
	 */
	public function set($data) {
		$this->data = $data;
	}

	/**
	 * Sets the maximum age in seconds.
	 *
	 * Takes any positive integer including zero. Specify null to disable the expiry of the cache.
	 *
	 * It is recommended to call this function directly after object creation. Using this function
	 * after working with data might have no effect on the data.
	 *
	 * @param int Expiry time in seconds or null
	 */
	public function setExpiryTime($maxAge) {
		if ($maxAge >= 0) {
			$this->maxAge = $maxAge;
		}
		else {
			$this->maxAge = null;
		}
	}

	/**
	 * Deletes the cache file.
	 *
	 * Returns false if the file could not be deleted or does not exist.
	 *
	 * @return boolean true on success, false on failure
	 */
	public function delete() {
		$this->data = null;
		return $this->driver->delete($this->name);
	}

	/**
	 * Returns whether the file can be rebuilt or not.
	 *
	 * A cache file can't be rebuilt if the data has been set only via CoreObject::set() method
	 * as the context of the usage is missing and there is no built-in data retrieval method. If
	 * there is data retrival logic implemented (this is normally the case if CacheItem is extended
	 * and the load method implemented) you can normally rebuild a class.
	 *
	 * @return	boolean	true if the file can be rebuilt without context
	 */
	public function rebuildable() {
		return false;
	}

	/**
	 * Saves the data to the cache file as serialized data.
	 *
	 * @return boolean true on success, false on failure.
	 */
	protected function save() {
		return $this->driver->save($this->name, $this->data, $this->maxAge);
	}

	/**
	 * Reads a cache file and unserializes the data.
	 * 
	 * Returns false if the cache file can't be read otherwise true.
	 *
	 * @return true on success, false on failure.
	 */
	protected function read() {
		return $this->driver->read($this->name);
	}
}
?>
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

	const DEFAULT_DIR = 'data/cache/';

	protected $name;
	protected $file;
	protected $data;
	protected $maxAge;

	public function __construct($name, $path = CacheObject::DEFAULT_DIR) {
		$this->name = $name;
		$this->path = FileSystem::adjustTrailingSlash($path, true);
		$this->file = new File($this->path.$this->name.'.ser');
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
	 * Takes any positive integer including zero.
	 * Specify an negative integer or null (not zero!) to disable the expiry of the cache.
	 *
	 * It is recommended to call this function directly after object creation.
	 * If you use this function after you have retrieved data, this setting won't have any
	 * effect on the loaded data.
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
		if ($this->file->exists() == true) {
			return $this->file->delete();
		}
		else {
			return false;
		}
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
		return $this->file->write(serialize($this->data));
	}

	/**
	 * Reads a cache file and unserializes the data.
	 * 
	 * Returns false if the cache file can't be read otherwise true.
	 *
	 * @return true on success, false on failure.
	 */
	protected function read() {
		if ($this->file->exists() == true) {
			$data = $this->file->read();
			if ($data !== false) {
				$this->data = unserialize($data);
				return true;
			}
			else {
				$this->data = null;
				return false;
			}
		}
		else {
			return false;
		}
	}

	/**
	 * Returns the age of the cache in seconds.
	 *
	 * This is simply the current time minus the last file modification time.
	 *
	 * @return int Age in seconds or -1 if the file does not exist.
	 */
	protected function age() {
		if ($this->file->exists() == true) {
			return (time() - $this->file->modTime());
		}
		else {
			return -1;
		}
	}

	/**
	 * Returns whether the cache file is expired or not.
	 *
	 * Returns boolean true if file is expired or false if file is up-to-date.
	 * If the file is expired the cache file will be automatically deleted!
	 *
	 * @return boolean true if expired, false if not expired.
	 */
	protected function isExpired() {
		if ($this->maxAge !== null && $this->age() > $this->maxAge) {
			$this->delete();
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks whether the cache exists or not.
	 *
	 * This function returns only true if the cache file exists in the filesystem and the file size
	 * is greater than 0. In addition the cache has to be up to date, if the file is expired the
	 * cache file will be deleted and false will be returned.
	 *
	 * @return boolean Returns boolean true if the cache file is not expired, not empty and exists
	 * @see CacheObject::isExpired()
	 */
	protected function exists() {
		if ($this->file->exists() == true && $this->file->size() > 0 && $this->isExpired() == false) {
			return true;
		}
		else {
			return false;
		}
	}
}
?>
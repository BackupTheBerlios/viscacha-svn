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
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License
 */

/**
 * Abstract class for Cache classes that implement data retrieval (e.q. from database) instead of
 * using the CacheObject::set() method directly.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */
abstract class CacheItem extends CacheObject {

	/**
	 * Implement the data retrieval for the cache.
	 *
	 * You just need to set the data with the CoreCobject::set() method afterwards.
	 *
	 * @see CoreObject::set()
	 */
	protected abstract function load();

	/**
	 * Call this function to get the cached data.
	 *
	 * If needed the data will be loaded from the cache file once. If the cache file doesn't exist
	 * the data is loaded with the load-method and saved with the save-method.
	 * Function returns null on failure.
	 *
	 * @return mixed Cached data or null
	 */
	public function get() {
		if ($this->data === null) {
			if($this->exists() == false) {
				$this->load();
				$this->save();
			}
			else {
				$this->read();
			}
		}
		return $this->data;
	}

	/**
	 * Returns whether the file can be rebuilt or not.
	 *
	 * {@inheritDoc}
	 *
	 * @return	boolean	true if the file can be rebuilt without context
	 */
	public function rebuildable() {
		return true;
	}

}
?>
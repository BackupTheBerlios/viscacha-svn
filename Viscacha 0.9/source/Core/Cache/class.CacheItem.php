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

Core::loadClass('Core.Cache.CacheObject');

/**
 * Abstract class for Cache classes that implement data retrieval instead of using the CacheObject::set() method.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */
abstract class CacheItem extends CacheObject {

	public function __construct($filename, $path = CACHE_DEFAULT_DIR) {
		parent::__construct($filename, $path);
	}

	/**
	 * Implement the data retrieval for the cache.
	 *
	 * You just need to set the data with the CoreCobject::set() method afterwards.
	 *
	 * @see CoreObject::set()
	 */
	public abstract function load();

	/**
	 * @todo Add Documentation
	 */
	public function get() {
		if ($this->data === null || $this->exists() == false) {
			$this->load();
			$this->save();
		}
		else {
			$this->read();
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
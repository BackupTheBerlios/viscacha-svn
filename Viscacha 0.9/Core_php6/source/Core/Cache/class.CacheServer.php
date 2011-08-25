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
* @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
*/

/**
* The CacheServer manages the CacheObjects and CacheDrivers.
*
* @package		Core
* @subpackage	Cache
* @author		Matthias Mohr
* @since		0.8
*/
class CacheServer {

	const DEFAULT_DRIVER = 'Serialize';

	private static $data = array();
	private static $drivers = array();

	public function addDriver(CacheDriver $driver, $name = null) {
		if ($name === null) {
			$name = get_class($driver);
		}
		self::$drivers[$className] = $driver;
	}

	public function getDriver($driver = null) {
		if ($driver === null) {
			$driver = self::DEFAULT_DRIVER;
		}
		return self::$drivers[$driver];
	}

	/**
	 * This method loads a cache item and adds it to the cache manager.
	 *
	 * If the cache class file is not found a new CacheObject will be constructed.
	 *
	 * @param string Name of the cache
	 * @param string Cache driver to use
	 * @param array Arguments to use for object creation
	 * @return object
	 **/
	public function load($className, $driver = null, array $args = array()) {
		if (isset($this->data[$className]) == false) {
			if ($driver == null) {
				$driver = self::$defaultDriver;
			}
			if (class_exists($className) == true) {
				$this->data[$className] = Utility::createClassInstance($className, $args);
			}
			else {
				$this->data[$className] = new CacheObject($className, $driver);
			}
		}
		return $this->data[$className];
	}

	/**
	 * This method removes a cache item from the cache manager.
	 *
	 * If the cache manager has no cache item with the specified name nothing will be done.
	 *
	 * @param string Name of the cache file
	 **/
	public function unload($className) {
		if (isset(self::$data[$className]) == true) {
			unset(self::$data[$className]);
		}
	}

}
?>
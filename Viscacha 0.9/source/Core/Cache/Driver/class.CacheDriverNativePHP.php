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
 * Cache driver that stores the cached data as native php code in the filesystem.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		1.0
 */
class CacheDriverPHP implements CacheDriver {

	private $path;

	public function __construct($path = null) {
		if ($path == null) {
			$path = Config::get('base.file_cache.default_dir');
		}
		$this->path = FileSystem::adjustTrailingSlash($path, true);
	}

	/**
	 * Deletes data from the cache.
	 *
	 * @param string Cache name
	 * @return boolean true on success, false on failure.
	 */
	public function delete($name) {
		$file = new FilePHP($this->path.$name.'.php');
		return $file->delete();
	}

	/**
	 * Saves the data to the cache file as serialized data with an optional expiry time.
	 *
	 * @param string Cache name
	 * @param mixed Data to cache
	 * @param int Expiration time in seconds, if it's 0, the item (theoretically) never expires.
	 * @return boolean true on success, false on failure.
	 */
	public function save($name, $data, $expiry = 0) {
		$file = new FilePHP($this->path.$name.'.php');
		$data = array(
			'expires' => ($expiry > 0 ? (\time() + $expiry) : 0),
			'data' => $data
		);
		return $file->writeArray($data);
	}

	/**
	 * Reads a cache file and unserializes the data.
	 *
	 * @param string Cache name
	 * @return Cached data or null on failure
	 */
	public function load($name) {
		$file = new FilePHP($this->path.$name.'.php');
		if ($file->exists() == true && $file->size() > 0) {
			$data = $file->parse();
			if ($data !== null) {
				if (isset($data['expires']) && ($data['expires'] == 0 || $data['expires'] > time())) {
					return $data['data'];
				}
				else {
					$this->delete();
				}
			}
		}
		return null;
	}

}
?>
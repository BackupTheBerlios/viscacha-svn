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
 * Operating system specific functions.
 *
 * Example:
 * <code>
 *  // Store temporary entries (Registry like) - Namespace: temp
 * Config::setConfigHandler(new TempConfig(), 'temp');
 * // Load/Write entries from a native php array in file data/config.php - Namespace: base
 * Config::setConfigHandler(new PHPConfig('data/config.php'), 'base');
 * // Load/Write entries from a database table named config - Namespace: core
 * Config::setConfigHandler(new DBConfig('config'), 'core');
 * // ...
 *
 * // Set a temporary variable (named 'script_start' using namespace 'temp'!) with the starting time
 * Config::set('temp.benchmark.script_start', microtime(true));
 * // After a hard working script get the variable back
 * $startTime = Config::get('temp.benchmark.script_start');
 * </code>
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 * @todo Check implementation
 */
class Config {

	private static $configHandler = array();

	public static function setConfigHandler(ConfigHandler $config, $namespace) {
		self::$configHandler[$namespace] = $config;
	}

	public static function getConfigHandler($namespace) {
		if (isset(self::$configHandler[$namespace]) == true) {
			return self::$configHandler[$namespace];
		}
		else {
			return null;
		}
	}

	public static function get($name) {
		list($namespace, $path) = explode('.', $name, 2);
		if (isset(self::$configHandler[$namespace]) == true) {
			return self::$configHandler[$namespace]->get($path);
		}
		else {
			return null;
		}
	}

	public static function set($name, $value) {
		list($namespace, $path) = explode('.', $name, 2);
		if (isset(self::$configHandler[$namespace]) == true) {
			return self::$configHandler[$namespace]->set($path, $value);
		}
		else {
			return null;
		}
	}

	public static function save() {
		if (isset(self::$configHandler[$namespace]) == true) {
			return self::$configHandler[$namespace]->save();
		}
		else {
			return null;
		}
	}

}
?>
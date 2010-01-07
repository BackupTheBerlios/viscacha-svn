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
 * Global Configuration Manager supporting namespaces.
 *
 * Example:
 * <code>
 * // Store temporary entries (Registry like) - Namespace: temp
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
 */
class Config {

	/**
	 * Array containing the ConfigHandler
	 * @var array
	 */
	private static $configHandler = array();

	/**
	 * Add a ConfigHandler to the Config Manager using a specified namespace.
	 *
	 * You can add mutliple ConfigHandler from the same type, but the namespace is unique and will
	 * be overwritten if soecified twice per runtime.
	 *
	 * @param ConfigHandler Object that implements the ConfigHandler interface
	 * @param string Namespace to use for the config data of this handler
	 */
	public static function setConfigHandler(ConfigHandler $config, $namespace) {
		self::$configHandler[$namespace] = $config;
	}

	/**
	 * Get a ConfigHandler by namespace from the Config Manager for advanced use of the handler.
	 *
	 * @param string Namespace
	 * @return ConfigHandler ConfigHandler for the namespace or null
	 */
	public static function getConfigHandler($namespace) {
		if (isset(self::$configHandler[$namespace]) == true) {
			return self::$configHandler[$namespace];
		}
		else {
			return null;
		}
	}

	/**
	 * Returns a variable or group of variables.
	 *
	 * This function can return a specific config entry as scalar or the whole group as associative
	 * array. On failure null will be returned.
	 *
	 * Use the following notation to get a group of variables: 'Namespace.Group' and this notation
	 * to get one specific variable: 'Namespace.Group.VariableName'.
	 *
	 * @param string Namespace.Group[.VariableName]
	 * @return mixed Array for a variable group, scalar for a single variable or null on failure
	 * @see ConfigHandler::set()
	 */
	public static function get($name) {
		list($namespace, $path) = self::parseName($name);
		if (isset(self::$configHandler[$namespace]) == true) {
			return self::$configHandler[$namespace]->get($path);
		}
		else {
			return null;
		}
	}

	/**
	 * Adds or edits a variable or a group of variables.
	 *
	 * This function can add or edit a specific config entry (allowed type is scalar) or the whole
	 * group (allowed type is an associative array with the keys as entry names and scalar values).
	 * If you specify a whole group of arrays you can only specify a subset of the group as the
	 * add/edit works for each element separately. If you miss one entry it won't be deleted or
	 * changed. Function returns true on success and false on failure.
	 *
	 * @param string Name for the variable (group), see Config::get() method for syntax.
	 * @param mixed Array for variable groups, scalar for specific variables
	 * @return boolean true on success, false on failure
	 * @see Config::get()
	 */
	public static function set($name, $value) {
		list($namespace, $path) = self::parseName($name);
		if (isset(self::$configHandler[$namespace]) == true) {
			return self::$configHandler[$namespace]->set($path, $value);
		}
		else {
			return false;
		}
	}

	/**
	 * Saves configuration data.
	 */
	public static function save() {
		foreach (self::$configHandler as $handler) {
			$handler->save();
		}
	}

	/**
	 * Parses a name into two parts.
	 *
	 * @param string Name
	 * @return array
	 */
	private static function parseName($name) {
		return explode('.', $name, 2) + array(null, null);
	}

}
?>
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
 * Abstract config implementation.
 *
 * All the classes that implement the several config formats have to implement this interface as
 * the Config Manager can only handle classes that have this specifications.
 *
 * The name of a config entry is in the format 'group1.group2.groupX.varname'.
 * Group and entry names can contain the following chars: A-Z, a-Z, 0-9, _, - and can have a
 * maximum length of 64 chars.
 * Values can only be scalar (int, float, string, boolean), to store objects see class Core with the
 * methods storeObject() and getObject().
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */
interface ConfigHandler {

	/**
	 * This function has to save the data if needed.
	 * 
	 * @see ConfigHandler::save()
	 */
	public function __destruct();

	/**
	 * Returns one specific config entry.
	 *
	 * @param string Config entry name
	 * @return scalar Scalar value for the config entry or null on failure
	 */
	public function get($name);

	/**
	 * Returns a group of config entries.
	 *
	 * @param string Config group name
	 * @return array Array containing the config group or null on failure
	 */
	public function getGroup($name);

	/**
	 * Sets (add or edit) a config entry.
	 *
	 * @param string Config entry name
	 * @param scalar Scalar value for the config entry
	 * @return boolean true on success, false on failure
	 */
	public function set($name, $value);

	/**
	 * Sets (add or edit) a group of config entries.
	 *
	 * @param string Config group name
	 * @param array Array containing the config group data (only scalars are allowed!)
	 * @return boolean true on success, false on failure
	 */
	public function setGroup($name, array $data);

	/**
	 * Renames a config group or a single entry.
	 *
	 * If newname is already available it will be overwritten.
	 *
	 * @param string Old group or entry name
	 * @param string New group or entry name
	 * @return boolean true on success, false on failure
	 */
	public function rename($oldName, $newName);

	/**
	 * Remmoves a config group or a single entry.
	 *
	 * @param string Config group or entry name
	 */
	public function delete($name);

	/**
	 * Loads the config data.
	 *
	 * This coule be done on object construction or maybe on first usage (lazy loading).
	 *
	 * @return boolean true on success, false on failure.
	 */
	protected function load();

	/**
	 * Saves the config data.
	 *
	 * This function must be called in the destructor!
	 * 
	 * @return boolean true on success, false on failure.
	 */
	public function save();

}
?>
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
 * The name of a config entry is in the format 'groupname.varname'. If no group name is specified 
 * the group 'default' is automatically used. Group names can contain the following chars:
 * A-Z, a-Z, 0-9, _, - and variable/entry names can contain the same chars plus the dot (.).
 * Both values can each have a maximum length of 32 chars. Values can only be scalar (int, float,
 * string, boolean), to store objects see class Core with the methods storeObject() and getObject().
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */
interface ConfigHandler {

	// The constructor params are for what needs to be set (file paths, database tables, ...)

	/**
	 * This function must save the data if needed.
	 * 
	 * @see ConfigHandler::save()
	 */
	public function __destruct();

	/**
	 * Return config data.
	 *
	 * This function can return a specific config entry as scalar or the whole group as associative
	 * array. On failure null will be returned.
	 *
	 * @param string Config name
	 * @return mixed Config data
	 */
	public function get($name);

	/**
	 * Add or edit config data.
	 *
	 * This function can add or edit a specific config entry (allowed type is scalar) or the whole
	 * group (allowed type is an associative array with the keys as entry names and scalar values).
	 * Function returns true on success and false on failure.
	 *
	 * @param string Config name
	 * @param scalar Config data
	 * @return boolean true on success, false on failure
	 */
	public function set($name, $value);

	/**
	 * Rename config data.
	 *
	 * This can rename a whole group or just a single entry. You can't move a group to a single
	 * entry or vice versa. If newname is already available it will be overwritten. Function
	 * returns true on success and false on failure.
	 *
	 * @param string Old group or entry name
	 * @param string New group or entry name
	 * @return boolean true on success, false on failure
	 */
	public function rename($oldName, $newName);

	/**
	 * Remove a config group or a single entry.
	 *
	 * Function returns true on success (when specified name does not exist at end of the function
	 * runtime) and false on failure.
	 *
	 * @param string Config name
	 */
	public function delete($name);

	/**
	 * Creates a new container to store the data.
	 *
	 * This function creates the "container" where the data is stored, for example a database table
	 * or a new file. Container will only be created if not already existent.
	 *
	 * @return boolean true on success, false on failure
	 */
	public function create();

	/**
	 * Load the config data.
	 *
	 * Replaces existent data.
	 * This could be done on object creation or on first usage (lazy loading).
	 *
	 * @return boolean true on success, false on failure.
	 */
	public function load();

	/**
	 * Save the config data.
	 *
	 * This function must be called in the destructor!
	 * 
	 * @return boolean true on success, false on failure.
	 * @see ConfigHandler::__destruct()
	 */
	public function save();

}
?>
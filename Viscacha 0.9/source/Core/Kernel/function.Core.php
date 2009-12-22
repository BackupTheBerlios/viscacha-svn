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
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

Core::loadPackage('Core.Kernel');
Core::loadPackage('Core.Util.DataTypes');
Core::loadPackage('Core.Cache');
Core::loadClass('Core.Util.System');
Core::loadClass('Core.FileSystem.FileSystem');
Core::loadClass('Core.FileSystem.File');
Core::loadClass('Core.FileSystem.Folder');

/**
 * Short form for Core::getObject().
 *
 * During implementation we tried several short forms and decided to take the last one:
 * <code>
 * Core::_(DB)->query("SQL");	// This is ugly
 * Core::DB()->query("SQL");	// Ok, but slow (see php manual)
 * Core::DB('query', "SQL");	// Alternative for the above
 * Core::$DB->query("SQL");		// Not possible in PHP 5.3 (__getStatic, __setStatic), maybe
 *								// introduced in PHP 6, then the best option!
 * Core(DB)->query("SQL");		// Short, fast, but not really oop. Seems to be the best one...
 * </code>
 *
 * @param	string|int	Stored name of object as string or constant (internally that's an int)
 * @return	Object		Stored object of a class
 * @see Core::getObject()
 */
function Core($objectConst) {
	return Core::getObject($objectConst);
}

/**
 * Loads the required classes automatically from ClassManager (only indexed classes).
 *
 * @param string Name of the requested class
 */
function __autoload($className) {
	$classManager = ClassManager::getInstance();
	$classManager->loadFile($className);
}
?>
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

checkCore();
Core::loadClass('Core.Kernel.ClassManager');

/**
 * Short form for Core::getObject().
 *
 * During implementation we tried several short forms and decided to take the last one:
 * <code>
 * Core::_(DB)->query("SQL"); // This is ugly
 * Core::DB()->query("SQL"); // Ok, but slow (see php manual)
 * Core::DB('query', "SQL"); // Alternative for the above
 * Core::$DB->query("SQL"); // Not possible in PHP 5.3 (__getStatic, __setStatic), maybe introduced in PHP 6
 * Core(DB)->query("SQL"); // Short, fast, but not really oop. It's just a good short wrapper
 * </code>
 *
 * @param int Constant for the object to be loaded
 * @return Object Stored object of a class
 * @see Core::getObject()
 */
function Core($objectConst) {
	return Core::getObject($objectConst);
}

/**
 * This function should be used at the top of every source file. This is not neede for classes.
 *
 * This checks whether the VISCACHA_CORE constant is set to 2 or not.
 * In case VISCACHA_CORE is not "2" the script dies immediately with the following error message:
 * <code>Error: Internal protection against hacking attempts</code>
 */
function checkCore() {
	if (constant('VISCACHA_CORE') !== '2') {
		die('Error: Internal protection against hacking attempts');
	}
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
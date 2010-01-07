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

/**
 * Implements the Singleton pattern as an abstract class.
 *
 * Example:
 * <code>
 * class ConcreteSingleton extends Singleton {
 *   public function __construct($param) {
 *     // Some code using $param...
 *   }
 * }
 * $test = ConcreteSingleton::getInstance('Hello World');
 * </code>
 *
 * @package		Core
 * @subpackage	Kernel
 * @copyright	Copyright (c) 2009, phpbar.de
 * @since 		1.0
 * @link		http://www.phpbar.de/w/Abstract_Singleton
 */
abstract class Singleton {

	private static $objects = array();

	// getInstance is better than getObject as this name is already used in Core class (confusing)
	public static final function getInstance() {
		$class = get_called_class();
		if (empty(self::$objects[$class])) {
			Core::loadClass('Core.Util.Utility');
			self::$objects[$class] = Utility::createClassInstance($class, func_get_args());
		}
		return self::$objects[$class];
	}

	public final function __clone() {
		throw new CoreException('Singleton must not be cloned.');
	}
}

?>
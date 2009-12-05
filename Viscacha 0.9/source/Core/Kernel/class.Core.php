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

Core::loadClass('Core.Kernel.CoreException');

/**
 * Useful Core functionality for centralized object handling and more.
 *
 * This class is abstract as there are only static methods and properties.
 *
 * @package		Core
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @abstract
 */
abstract class Core {

	/**
	 * Saves the stored objects
	 *
	 * @var array
	 * @static
	 */
	private static $namedObjects = array();

	/**
	 * Gets an object of a class with the stored name passed as parameter.
	 *
	 * The method loads a stored object, which has to be stored with storeObject() before.
	 * The parameter is case sensitive. On failure a CoreException will be thrown (error code 1).
	 *
	 * Alternative: You can use the short wrapper function to get the stored objects. Just use Core(DB) for the DB class.
	 *
	 * @param	string	Stored name of object
	 * @return	Object	Returns the object
	 * @see Core()
	 * @throws CoreException
	 */
	public static function getObject($objectId) {
		if (isset(self::$namedObjects[$objectId]) && is_object(self::$namedObjects[$objectId])) {
			return self::$namedObjects[$objectId];
		}
		else {
			$e = new CoreException("Object with name '{$objectId}' not found.", 1);
			$e->setArrayData(self::$namedObjects);
			throw $e;
		}

	}

	/**
	 * Stores an object with a specified name.
	 *
	 * If no name or no string as second argument is specified the class name is used instead.
	 * If a stored object with the specified name is existant it will be replaced.
	 * The second parameter is case sensitive.
	 *
	 * The object name will be declared as a constant, but you can declare a name multiple times (unlike constants).
	 * The constant will get an internal id and this is used also for stored objects with the same name.
	 *
	 * @param	Object	Object to store
	 * @param	string	Name for the object
	 */
	public static function storeObject($object, $name = null) {
		if (is_string($name) == false) {
			$name = get_class($object);
		}

		if (defined($name) == false) {
			if (count(self::$namedObjects) > 0) {
				$objectId = max(array_keys(self::$namedObjects)) + 1;
			}
			else {
				$objectId = 0;
			}
			define($name, $objectId);
		}
		else {
			$objectId = constant($name);
		}

		self::$namedObjects[$objectId] = $object;
	}

	/**
	 * Deletes an object by internal id or object name.
	 *
	 * If the specified name is not existant nothing will happen.
	 * The parameter is case sensitive if you pass the name as string.
	 * You can also pass the internal id with the defined constant.
	 *
	 * @param	string|int	Stored name of an object or internal object id
	 */
	public static function removeObject($objectId) {
		if (is_string($objectId) == true) {
			$objectId = constant($objectId);
		}
		if (isset(self::$namedObjects[$objectId]) == true) {
			self::$namedObjects[$objectId] = null;

		}
	}

	/**
	 * Checks case sensitive if a class for the specified full class name (package + class) exists on the server.
	 *
	 * If the class exists the path will be returned in the other case null will be returned.
	 *
	 * Attention: This method only checks whether the corresponding file exists, but the content of the file won't be checked!
	 *
	 * Example full class names for classes:
	 * <ul>
	 * <li>Core.Core is this class
	 * <li>Core.System.ClassManager is the ClassManager class at aource/Core/System/class.ClassManager.php
	 * <lI>Board.Controller.Forums would be a class located at source/Board/Controller/class.Forums.php
	 * </ul>
	 *
	 * @param	string	Full class name
	 * @return	string	Path to class or null
	 * @see Core::sourceFileExists()
	 **/
	public static function classExists($class){
		return self::sourceFileExists($class, 'class');
	}

	/**
	 * Checks case sensitive if an interface for the specified full interface name (pakcage + interface) exists on the server.
	 *
	 * If the interface exists the path will be returned in the other case null will be returned.
	 *
	 * Attention: This method only checks whether the corresponding file exists, but the content of the file won't be checked!
	 *
	 * Example full interface names:
	 * <ul>
	 * <li>Core.DB.DBAL is the interface DBAL in the subpackage DB in the package Core.
	 * <li>Core.Cache.CacheObject is the interface CacheObject in the subpackage Cache in the pckage Core.
	 * </ul>
	 *
	 * @param	string	Full interface class
	 * @return	string	Path to interface or null.
	 * @see Core::sourceFileExists()
	 **/
	public static function interfaceExists($interface){
		return self::sourceFileExists($interface, 'interface');
	}

	/**
	 * Checks case sensitive if a source code file for the specified full name exists.
	 *
	 * If the file exists the path will be returned in the other case null will be returned.
	 *
	 * @param	string	Full name
	 * @param	string	Prefix of the file name (e.q. interface or class)
	 * @return	string	Path to the file or null on failure
	 * @see Core::classExists()
	 * @see Core:interfaceExists()
	 */
	protected static function sourceFileExists($package, $prefix) {
		$packageData = explode('.', $package);
		array_unshift($packageData, 'source');
		$packageName = array_pop($packageData);
		$folders = implode('/', $packageData);
		$file = "{$folders}/{$prefix}.{$packageName}.php";
		if (file_exists($file) == true) {
			return $file;
		}
		else {
			return null;
		}
	}

	/**
	 * Loads every class and interface from the specified package.
	 *
	 * This method loads every file (and the containing classes/interfaces) in the specified package.
	 * This does not include possible subpackages (subdirectories), these packages have to be included separately.
	 *
	 * @param	string	Name of package (e.q. Core.Util.DataTypes for classes and interfaces in source/Core/Util/Datatypes)
	 * @todo	Check Implementation
	 */
	public static function loadPackage($package){
	    $path = 'source/'.str_replace('.', '/', $package).'/';
		$files = glob($path.'(class|interface)\.*\.php');
		foreach ($files as $file) {
		    include_once($path.$file);
		}
	}

	/**
	 * Loads a class from the filesystem that can be used afterwards.
	 *
	 * On failure the class won't be available (but maybe the file) and a CoreException will be thrown.
	 * The parameter is case sensitive. For the format of a full package name see Core::classExists().
	 *
	 * @see 	Core::classExists()
	 * @param	string	Full name of the class
	 * @throws	CoreException
	 */
	public static function loadClass($class){
		$className = self::getNameFromPackage($class);
		if (class_exists($className, false) == false) {
			$file = self::classExists($class);
			if ($file === null) {
				throw new CoreException("Can't find class source file for '{$class}'", 2);
			}
			else {
				include_once($file);
				if (class_exists($className, false) == false) {
					throw new CoreException("Included source file does not contain a class with the name '{$className}'", 3);
				}
			}
		}
	}

	/**
	 * Loads an interface from the filesystem that can be used afterwards.
	 *
	 * On failure the interface is not available (but maybe the file) and the script will throw a CoreException.
	 *
	 * @see		Core::interfaceExists()
	 * @param	string	Full name of the interface
	 * @throws	CoreException
	 **/
	public static function loadInterface($interface){
		$interfaceName = self::getNameFromPackage($interface);
		if (interface_exists($interfaceName, false) == false) {
			$file = self::interfaceExists($interface);
			if ($file === null) {
				throw new CoreException("Can't find interface source file for '{$interface}'", 2);
			}
			else {
				include_once($file);
				if (interface_exists($interfaceName, false) == false) {
					throw new CoreException("Included source file does not contain an interface with the name '{$interfaceName}'", 5);
				}
			}
		}
	}

	/**
	 * Extracts the last part of a name (the class name respectively the interface name)
	 *
	 * @param	string	Package name
	 */
	private function getNameFromPackage($name) {
		return array_pop(explode('.', $name));
	}

}
?>
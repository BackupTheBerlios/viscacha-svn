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
 * Abstract validator that has to be extended by all classes that implement methods used in the
 * Validator and ValidatorElement classes.
 *
 * To write your own classes with validation rules the class names need a special suffix: Validator.
 * The namespace to use in the Validator classes will be the part before the suffix.
 * The methods implementing custom validators are static, protected and have to return a boolean
 * value (true on success, false on failure). The method names need the prefix '_', but in the
 * validator they are called without the prefix. The first parameter has to be the value to check,
 * the second parameter is true when the check is optional.
 * The error codes or an empty array on success are returned by the static method getErrors().
 * The error array is cleared on every function call to a validation method.
 *
 * Example method signature:
 * <code>protected static function _funcName($value, $optional, $arg1, $arg2 = true, ...)</code>
 *
 * More information on the validation framework and how to implement Validator and Filter classes
 * you can find in the documentation of the Validator class.
 *
 * @package		Core
 * @subpackage	Security
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @see			Validator
 */
class AbstractValidator {

	protected static $errors = array();

	/**
	 * This function routes all requests to the validation methods.
	 *
	 * If validation method does not exist false will be returned..
	 * 
	 * @param string Function name
	 * @param array Arguments for the function
	 */
	public static function __callStatic($name, $arguments) {
		self::$errors = array();

		// Use Late static binding because __CLASS__ would contain AbstractValidator
		if (method_exists(get_called_class(), "_{$name}") == true) {
			return call_user_func_array("static::_{$name}", $arguments);
		}
		else {
			return false;
		}
	}

	/**
	 * Returns the error messages as array or an empty array if no errors occured.
	 *
	 * @return array
	 */
	public static function getErrors() {
		return self::$errors;
	}

	/**
	 * Sets an error code to the errors array.
	 *
	 * @param mixed Error Code
	 */
	protected static function setError($errorCode) {
		self::$errors[] = $errorCode;
	}

}
?>
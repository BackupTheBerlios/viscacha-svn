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
 * Several useful static methods to manipulate/work with numbers (integers, floats etc).
 *
 * This package does NOT represent a number and you can't define any content to this class.
 * This class is abstract as there are only static methods.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @abstract
 */
abstract class Numbers {

	/**
	 * Checks whether a number is a natural number (without zero).
	 *
	 * The natural number can be given as integer or as string.
	 * For strings it is not allowed to use and other chars than 0-9 and the first char can't be 0.
	 *
	 * @param	int|string	Number to check (Number system N)
	 * @return	boolean		true if the specified data is a natural number, false if not.
	 */
	public static function isNatural($x) {
		if (is_int($x) == true && $x > 0) {
			return true;
		}
		elseif (is_string($x) == true && ctype_digit($x) == true && $x[0] != 0) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks whether a number is an integer (no type check!).
	 *
	 * This method checks whether the specified number is an representative of an integer, this does
	 * not check whether the given parameter is a variable of the type int! The integer can be given
	 * as integer or as string. The string is a valid int if it contains only the chars 0-9 with an
	 * additional sign (+/-) as suffix.
	 *
	 * @param	int|string	Number to check (Number system Z)
	 * @return	boolean		true if the specified data is an integer, false if not.
	 */
	public static function isInteger($x) {
		if (is_int($x) == true) {
			return true;
		}
		// Is there a faster way than preg_match that can deal with numbers > PHP_INT_MAX?
		elseif (is_string($x) == true && preg_match('~^[\-\+]?\d+$~', $x) == 1) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks whether a number is a decimal (no type check!).
	 *
	 * This method checks whether the specified number is an representative of a decimal, this does
	 * not check whether the given parameter is a variable of the type float or int! The decimal can
	 * be given as integer, float or as string. The string is a valid decimal if it contains:
	 * Adittional +/-, one or more chars with a value between 0 and 9, another addtional part
	 * starting with a dot (not a comma!) and followed by one or more chars with a value
	 * between 0 and 9.
	 *
	 * @param mixed Number to check (Number system Q)
	 */
	public static function isDecimal($x) {
		if (is_int($x) == true || is_float($x) == true) {
			return true;
		}
		// Is there a better way than preg_match?
		elseif (is_string($x) == true && preg_match('~^[\-\+]?\d+(\.\d+)?$~', $x) == 1) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Add leading zeros to an integer until the specified length is reached.
	 *
	 * You can specify an integer or a string where leading zeros should be added.
	 * If the specified string is invalid it will be returned without any change.
	 * The sign of a number does not count to the length.
	 *
	 * If the length is negative the absolute value of the length will be used.
	 * The length 0 will do nothing.
	 *
	 * @param	int|string	Number to
	 */
	public static function leadingZero($x, $length = 2) {
		// strval() is used because without this call the $x on the right would be converted to an
		// integer as the left side is already an integer and that is always true.
		if ($length == 0 || intval($length) != $length || strval(intval($x)) != $x) {
			return $x;
		}
		else {
			$length = abs($length);
			return sprintf("%0{$length}d", $x);
		}
	}

}
?>

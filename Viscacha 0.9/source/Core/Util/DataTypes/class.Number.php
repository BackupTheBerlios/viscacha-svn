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
abstract class Number {

	/**
	 * Checks whether a number is a natural number (without zero).
	 *
	 * The natural number can be given as integer, float or as string.
	 * For strings it is not allowed to use and other chars than 0-9, so its not allowed to use
	 * floats in strings (1.0 or 1,0).
	 *
	 * @param	mixed	Number
	 * @return	boolean	true if the specified data is a natural number, false if not.
	 */
	public static function isNatural($x) {
		if (is_int($x) && $x > 0) {
			return true;
		}
		elseif(is_float($x) && $x > 0 && intval($x) == $x) {
			return true;
		}
		elseif (is_string($x) && ctype_digit($x) && intval($x) > 0) {
			return true;
		}
		else {
			return false;
		}
		// return (is_numeric($x) && $x > 0) ? (intval($x) == $x) : false;
	}

	public static function isInteger($x) {
	}

	public static function isDecimal($x) {
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

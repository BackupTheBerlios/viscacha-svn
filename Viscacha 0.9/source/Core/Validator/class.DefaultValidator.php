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
 * @subpackage	Validator
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * Provides different basic validation rules.
 *
 * @package		Core
 * @subpackage	Validator
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class DefaultValidator extends AbstractValidator {

	const ERROR_BETWEEN_EXCLUSIVE = 'validator_default_between_exclusive';
	const ERROR_BETWEEN_INCLUSIVE = 'validator_default_between_inclusive';
	const ERROR_NATURALNUMBER = 'validator_default_naturalnumber';
	const ERROR_GREATERTHAN = 'validator_default_greaterthan';
	const ERROR_LESSTHAN = 'validator_default_lessthan';
	const ERROR_ALPHANUMERIC = 'validator_default_alphanumeric';
	const ERROR_ALPHA = 'validator_default_alpha';
	const ERROR_NUMERIC = 'validator_default_numeric';
	const ERROR_HEXADECIMAL = 'validator_default_hexadecimal';
	const ERROR_DIGIT = 'validator_default_digit';
	const ERROR_EQUAL = 'validator_default_equal';
	const ERROR_EMAIL_GENERAL = 'validator_default_email_general';
	const ERROR_EMAIL_BLACKLIST = 'validator_default_email_blacklist';
	const ERROR_IPADDRESS_GENERAL = 'validator_default_ipaddress_general';
	const ERROR_IPADDRESS_LOCAL = 'validator_default_ipaddress_local';
	const ERROR_URL = 'validator_default_url';
	const ERROR_INARRAY = 'validator_default_inarray';
	const ERROR_LENGTH_MIN = 'validator_default_length_min';
	const ERROR_LENGTH_MAX = 'validator_default_length_max';
	const ERROR_REGEXP = 'validator_default_regexp';

	/**
	 * Checks if a value is between a minimum and maximum.
	 *
	 * Internal name: [default.]between
	 *
	 * Returns true if and only if the value is between the minimum and maximum boundary values.
	 *
	 * The comparison is inclusive by default, though this may be overridden by setting the fifth
	 * parameter to false. This will do a strict comparison, where the value must be strictly
	 * greater than the minimum and strictly less than the maximum.
	 *
	 * @see empty()
	 * @param mixed Value to check
	 * @param int|float Minimum boundary value
	 * @param int|float Maximum boundary value
	 * @param boolean Inclusive comparison (true, default) or exclusive comparison (false)
	 * @return boolean
	 */
	public static function between($value, $min, $max, $inclusive = true) {
		if ($inclusive == true) {
			if (Numbers::isDecimal($value) == true && $min >= $value && $value <= $max) {
				return true;
			}
			else {
				self::setError(self::ERROR_BETWEEN_INCLUSIVE);
				return false;
			}
		}
		else {
			if (Numbers::isDecimal($value) == true && $min > $value && $value < $max) {
				return true;
			}
			else {
				self::setError(self::ERROR_BETWEEN_EXCLUSIVE);
				return false;
			}
		}
	}

	/**
	 * Checks if a value is a natural number excluding zero (e.q. an ID).
	 *
	 * Returns true if and only if the value is a valid natural number excluding the zero.
	 *
	 * @see Numbers::isNatural()
	 * @param mixed Value to check
	 * @return boolean
	 */
	public static function naturalNumber($value) {
		if (Numbers::isNatural($value) == true) {
			return true;
		}
		else {
			self::setError(self::ERROR_NATURALNUMBER);
			return false;
		}
	}

	/**
	 * Checks if a value is greater than the minimum.
	 *
	 * Returns true if and only if the value (should be numeric) is greater than the minimum
	 * boundary value.
	 *
	 * @param mixed Value to check
	 * @param int|float Minimum boundary value
	 * @return boolean
	 */
	public static function greaterThan($value, $min) {
		if ($value > $min) {
			return true;
		}
		else {
			self::setError(self::ERROR_GREATERTHAN);
			return false;
		}
	}

	/**
	 * Checks if a value is less than the maximum.
	 *
	 * Returns true if and only if the value (should be numeric) is less than the maximum boundary
	 * value.
	 *
	 * @param mixed Value to check
	 * @param int|float Maximum boundary value
	 * @return boolean
	 */
	public static function lessThan($value, $max) {
		if ($value < $max) {
			return true;
		}
		else {
			self::setError(self::ERROR_LESSTHAN);
			return false;
		}
	}

	/**
	 * Checks if a value contains alphanumeric chars only.
	 *
	 * Returns true if and only if the value contains only alphabetic and digit chars.
	 *
	 * @see ctype_alnum()
	 * @param mixed Value to check
	 * @return boolean
	 */
	public static function alphanumeric($value, $optional) {
		if (ctype_alnum($value) == true) {
			return true;
		}
		else {
			self::setError(self::ERROR_ALPHANUMERIC);
			return false;
		}
	}

	/**
	 * Checks if a value contains alphabetic chars only.
	 *
	 * Returns true if and only if the value contains only alphabetic chars.
	 *
	 * @see ctype_alpha()
	 * @param mixed Value to check
	 * @return boolean
	 */
	public static function alpha($value, $optional) {
		if (ctype_alpha($value) == true) {
			return true;
		}
		else {
			self::setError(self::ERROR_ALPHA);
			return false;
		}
	}

	/**
	 * Checks if a value contains a number.
	 *
	 * This method checks whether the specified number is an representative of a decimal
	 * A string is a valid decimal if it contains: Adittional +/-, one or more chars with a value
	 * between 0 and 9, another addtional part starting with a dot (not a comma!) and followed by
	 * one or more chars with a value between 0 and 9.
	 *
	 * Note: To check if a value contains numeric chars/digits only, see DefaultValidator::digit().
	 *
	 * Note: As this only accepts a dot as decimal separator you should use the filter "float"
	 * (Defaultfilter::float()) before.
	 *
	 * @see Number::isDecimal()
	 * @param mixed Value to check
	 * @return boolean
	 */
	public static function numeric($value, $optional) {
		if (Numbers::isDecimal($value) == true) {
			return true;
		}
		else {
			self::setError(self::ERROR_NUMERIC);
			return false;
		}
	}

	/**
	 * Checks if a value contains alphanumeric chars only.
	 *
	 * Returns true if and only if the value contains only numeric chars/digits.
	 *
	 * @see ctype_alnum()
	 * @param mixed Value to check
	 * @return boolean
	 */
	public static function digit($value, $optional) {
		if (ctype_digit($value) == true) {
			return true;
		}
		else {
			self::setError(self::ERROR_DIGIT);
			return false;
		}
	}

	/**
	 * Checks if a value contains hexadecimal chars only.
	 *
	 * Returns true if and only if the value contains only hexadecimal 'digits' (0-9, A-F, a-f).
	 *
	 * @see ctype_xdigit()
	 * @param mixed Value to check
	 * @return boolean
	 */
	public static function hexadecimal($value, $optional) {
		if (ctype_xdigit($value) == true) {
			return true;
		}
		else {
			self::setError(self::ERROR_HEXADECIMAL);
			return false;
		}
	}

	/**
	 * Checks if a value is equal/identical to another value.
	 *
	 * Returns true if and only if the value is equal to another value. The third parameter enables
	 * or disables strict comparison, default is loose comparison (true).
	 *
	 * This check uses == for loose comparison (a is equal to b) and === for strict comparision
	 * (a is identical to b, this includes a type check).
	 *
	 * @param mixed Value to check
	 * @param mixed Value to check against
	 * @param boolean Loose (true, default) or strict (false) comparison.
	 * @return boolean
	 */
	public static function equal($value, $anotherValue, $strict = false) {
		if ($value == $anotherValue && $strict == false) {
			return true;
		}
		elseif ($value === $anotherValue && $strict == true) {
			return true;
		}
		else {
			self::setError(self::ERROR_EQUAL);
			return false;
		}
	}

	/**
	 * Checks if a value is ...
	 *
	 * Returns true if and only if the value is ...
	 *
	 * @param mixed Value to check
	 * @return boolean
	 * @todo Implement validator for eMail
	 */
	public static function eMail($value, $checkBlackList = true, $checkMX = true) {
		if (true) {
			return true;
		}
		else {
			self::setError(self::ERROR_EMAIL_GENERAL, self::ERROR_EMAIL_BLACKLIST);
			return false;
		}
	}

	/**
	 * Checks if a value is ...
	 *
	 * Returns true if and only if the value is ...
	 *
	 * @param mixed Value to check
	 * @return boolean
	 * @todo Implement validator for IPAddress
	 */
	public static function IPAddress($value, $allowLocal = false) {
		if (true) {
			return true;
		}
		else {
			self::setError(self::ERROR_IPADDRESS_GENERAL, self::ERROR_IPADDRESS_LOCAL);
			return false;
		}
	}

	/**
	 * Checks if a value is ...
	 *
	 * Returns true if and only if the value is ...
	 *
	 * @param mixed Value to check
	 * @return boolean
	 * @todo Implement validator for URL
	 */
	public static function URL($value) {
		if (true) {
			return true;
		}
		else {
			self::setError(self::ERROR_URL);
			return false;
		}
	}

	/**
	 * Checks if a value is ...
	 *
	 * Returns true if and only if the value is ...
	 *
	 * @param mixed Value to check
	 * @return boolean
	 * @todo Implement validator for inArray
	 */
	public static function inArray($value, array $array) {
		if (true) {
			return true;
		}
		else {
			self::setError(self::ERROR_INARRAY);
			return false;
		}
	}

	/**
	 * Checks if a value matches a RegExp pattern.
	 *
	 * Returns true if and only if the value matches against a regular expression pattern.
	 *
	 * @param mixed Value to check
	 * @param string Valid regular expression pattern
	 * @return boolean
	 * @see preg_match()
	 */
	public static function regExp($value, $pattern) {
		if (preg_match($pattern, $value) > 0) {
			return true;
		}
		else {
			self::setError(self::ERROR_REGEXP);
			return false;
		}
	}

	/**
	 * Checks if a value is between a specified length (or just above).
	 *
	 * Returns true if and only if the string length of the variable is at least a minimum and no
	 * greater than a maximum (when the max option is not -1).
	 *
	 * @param mixed Value to check
	 * @param int Minimum length
	 * @param int Maximum length or -1 to make this optional (default)
	 * @return boolean
	 * @see strlen()
	 */
	public static function length($value, $min = 0, $max = -1) {
		if (strlen($value) < $min) {
			self::setError(self::ERROR_LENGTH_MIN);
			return false;
		}
		elseif ($max > -1 && strlen($value) > $max) {
			self::setError(self::ERROR_LENGTH_MAX);
			return false;
		}
		else {
			return true;
		}
	}
}
?>
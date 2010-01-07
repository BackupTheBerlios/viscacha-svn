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
 * Provides different basic validation rules.
 *
 * @package		Core
 * @subpackage	Security
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class DefaultValidator extends AbstractValidator {

	const ERROR_EXCLUSIVE = 'validator_default_between_exclusive';
	const ERROR_INCLUSIVE = 'validator_default_between_inclusive';

	protected static function _between($value, $optional, $min, $max, $inclusive = true) {
		if ($inclusive == true) {
			if (Numbers::isDecimal($value) == true && $min >= $value && $value <= $max) {
				return true;
			}
			else {
				self::setError(self::ERROR_INCLUSIVE);
				return false;
			}
		}
		else {
			if (Numbers::isDecimal($value) == true && $min > $value && $value < $max) {
				return true;
			}
			else {
				self:setError(self::ERROR_EXCLUSIVE);
				return false;
			}
		}
	}

}
?>
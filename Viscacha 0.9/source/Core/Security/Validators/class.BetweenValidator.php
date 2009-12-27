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
 * Validates that a number is between two numbers.
 *
 * @package		Core
 * @subpackage	Security
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @see			http://www.php.de/software-design/50128-formular-validierung.html
 */
class BetweenValidator extends AbstractValidator {

	const NOT_BETWEEN_EXCLUSIVE = 'NOT_BETWEEN_EXCLUSIVE';
	const NOT_BETWEEN_INCLUSIVE = 'NOT_BETWEEN_INCLUSIVE';

	protected $min;
	protected $max;
	protected $inclusive;

	public function  __construct($min, $max, $inclusive = true) {
		parent::__construct();
		$this->min = $min;
		$this->max = $max;
		$this->inclusive = $inclusive;
	}

	public function isValid($value) {
		$this->reset();
		if ($this->optional == true && empty($value) == true) {
			return true;
		}
		if ($this->inclusive == true) {
			if (Numbers::isDecimal($value) == true && $this->min >= $value && $value <= $this->max) {
				return true;
			}
			else {
				$this->setError(self::NOT_BETWEEN_INCLUSIVE);
				return false;
			}
		}
		else {
			if (Numbers::isDecimal($value) == true && $this->min > $value && $value < $this->max) {
				return true;
			}
			else {
				$this->setError(self::NOT_BETWEEN_EXCLUSIVE);
				return false;
			}
		}
	}

}
?>
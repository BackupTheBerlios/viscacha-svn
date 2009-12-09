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
 * Operating system specific functions.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */
class System {

	const WINDOWS = 1;
	const MAC = 2;
	const UNIX = 3; // Linux, BSD, Unix etc.

	/**
	 * Returns the basic type/family of operating system.
	 *
	 * The default OS is UNIX (used for everything not Windows and not MAC). 
	 *
	 * @return int Constant: System::WINDOWS, System::MAC or System::UNIX (default)
	 */
	public static function getOS() {
		$os = strtoupper(substr(PHP_OS, 0, 3));
		if($os == 'MAC' || $os == 'DAR') {
			return System::MAC;
		}
		elseif ($os == 'WIN') {
			return System::WINDOWS;
		}
		elseif (isset($_SERVER['OS']) && stripos($_SERVER['OS'], 'Windows') !== false) {
			return System::WINDOWS;
		}
		elseif (function_exists('php_uname') && stripos(@php_uname(), 'Windows') !== false) {
			return System::WINDOWS;
		}
		else {
			return System::UNIX;
		}
	}

}
?>
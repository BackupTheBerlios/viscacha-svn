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
 * Several useful static methods to manipulate/work with strings.
 *
 * This package does NOT represent a string and you can't define any content to this class.
 * This class is abstract as there are only static methods.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @abstract
 */
abstract class String {

	public static function replaceLineBreak($string, $replace) {
		return str_replace(array("\r\n", "\n", "\r"), $replace, $string);
	}

	public static function isHash($string) {
		return (bool) preg_match("/^[a-f\d]{32}$/i", $string);
	}

	public static function splitWords($text) {
		$word_seperator = "\\.\\,;:\\+!\\?\\_\\|\s\"'\\#\\[\\]\\%\\{\\}\\(\\)\\/\\\\";
		return preg_split('/['.$word_seperator.']+?/', $text, -1, PREG_SPLIT_NO_EMPTY);
	}

}
?>

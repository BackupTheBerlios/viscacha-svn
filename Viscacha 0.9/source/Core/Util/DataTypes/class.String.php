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

	const WORD_SEPARATOR = '\.\,;:\+!\?\_\|\s"\'\#\[\]\%\{\}\(\)\/\\';

	public static function replaceLineBreak($string, $replace) {
		return str_replace(array("\r\n", "\n", "\r"), $replace, $string);
	}

	/**
	 * Splits a string into an array.
	 *
	 * The string is splitted at every line break and additionally all whitespaces are trimmed (this
	 * is the same as applying trim() to every element of the array). Empty lines/array elements are
	 * removed from the array. If the parameter is not a string or an error occurs null will be
	 * returned.
	 *
	 * @param string String to split
	 * @return array Splitted string as array or null on failure
	 * @see trim()
	 */
	public static function toTrimmedArray($text) {
		if (!is_string($text)) {
			return null;
		}
		else {
			return preg_split("~[ \t\0\x0B]*[\r\n]+[ \t\0\x0B]*~", $text, -1, PREG_SPLIT_NO_EMPTY);
		}
	}

	public static function isHash($string) {
		return (bool) preg_match("/^[a-f\d]{32}$/i", $string);
	}

	/**
	 * Splits a text into an array containing the words.
	 *
	 * @param string Text to split
	 * @return array Array wirh words or null on failure
	 * @see String::WORD_SEPARATOR
	 */
	public static function splitWords($text) {
		if (!is_string($text)) {
			return null;
		}
		else {
			return preg_split('~['.String::WORD_SEPARATOR.']+?~', $text, -1, PREG_SPLIT_NO_EMPTY);
		}
	}

}
?>

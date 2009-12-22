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
abstract class Strings {

	const WORD_SEPARATOR = ".,;:+!?_| '\"#[]%{}()/\r\n\t\\";

	public static function trimLineBreaks($string, $rightOnly = true) {
		$function = $rightOnly ? 'rtrim' : 'trim';
		return $function($string, "\r\n");
	}

	public static function replaceLineBreaks($string, $replace) {
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
	 * @see Strings::WORD_SEPARATOR
	 */
	public static function splitWords($text) {
		if (!is_string($text)) {
			return null;
		}
		else {
			return preg_split(
				'~['.preg_quote(self::WORD_SEPARATOR, '~').']+~',
				$text,
				-1,
				PREG_SPLIT_NO_EMPTY
			);
		}
	}

	/**
	 * Normalizes a string (replace/remove special chars).
	 *
	 * This function tries to replace as many chars as it can with standard ascii chars. Every char
	 * from String::WORD_SEPARATOR will be replaced with the char specified as second parameter,
	 * but if there are multiple of these chars in a row it is replaced with one separator only. If
	 * you specify a string with more than one char as separator only the first char will be used.
	 * Removes every char that couldn't be replaced before. The result will be a string with only
	 * alphanumerical chars plus the chars dot (.), underscore (_), dash (-) and if applicable the
	 * separator specified.
	 *
	 * @param string Initial string
	 * @param string Separator
	 * @return string Normalized string
	 */
	public static function replaceSpecialChars($text, $separator = '_') {
		if (strlen($separator) > 1) {
			$separator = $separator[0];
		}
		$text = strtr($text, self::getSpecialCharsMap());
		$text = preg_replace(
			'~['.preg_quote(self::WORD_SEPARATOR, '~').']+~',
			$separator,
			$text
		);
		$text = preg_replace("~[^\w\d\.-_".preg_quote($separator, '~')."]+~i", '', $text);
		return $text;
	}

	protected static function getSpecialCharsMap() {
		return array(
			'Š' => 'S',
			'š' => 's',
			'Đ' => 'Dj',
			'đ' => 'dj',
			'Ž' => 'Z',
			'ž' => 'z',
			'Č' => 'C',
			'č' => 'c',
			'Ć' => 'C',
			'ć' => 'c',
			'Ç' => 'C',
			'À' => 'A',
			'Á' => 'A',
			'Â' => 'A',
			'Ã' => 'A',
			'Ä' => 'Ae',
			'Å' => 'A',
			'Æ' => 'Ae',
			'È' => 'E',
			'É' => 'E',
			'Ê' => 'E',
			'Ë' => 'E',
			'Ì' => 'I',
			'Í' => 'I',
			'Î' => 'I',
			'Ï' => 'I',
			'Ñ' => 'N',
			'Ò' => 'O',
			'Ó' => 'O',
			'Ô' => 'O',
			'Õ' => 'O',
			'Ö' => 'Oe',
			'Ø' => 'O',
			'Ù' => 'U',
			'Ú' => 'U',
			'Û' => 'U',
			'Ü' => 'Ue',
			'Ý' => 'Y',
			'Þ' => 'B',
			'ß' => 'ss',
			'à' => 'a',
			'á' => 'a',
			'â' => 'a',
			'ã' => 'a',
			'ä' => 'ae',
			'å' => 'a',
			'æ' => 'a',
			'ç' => 'c',
			'è' => 'e',
			'é' => 'e',
			'ê' => 'e',
			'ë' => 'e',
			'ì' => 'i',
			'í' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'ð' => 'o',
			'ñ' => 'n',
			'ò' => 'o',
			'ó' => 'o',
			'ô' => 'o',
			'õ' => 'o',
			'ö' => 'oe',
			'ø' => 'o',
			'ù' => 'u',
			'ú' => 'u',
			'û' => 'u',
			'ý' => 'y',
			'ý' => 'y',
			'þ' => 'b',
			'ÿ' => 'y',
			'Ŕ' => 'R',
			'ŕ' => 'r',
			'ü' => 'ue'
		);
	}

}
?>

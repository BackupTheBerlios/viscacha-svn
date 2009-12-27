<?php
/**
 * Script zur einfachen Erstellung und Prüfung von Passwörtern.
 *
 * Copyright (c) 2006, Mathias Bank
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
 * @subpackage	Security
 * @author		Matthias Mohr
 * @author		Mathias Bank
 * @copyright	Copyright (c) 2006, Mathias Bank
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * Script zur einfachen Erstellung und Prüfung von Passwörtern.
 *
 * @package		Core
 * @subpackage	Security
 * @author		Matthias Mohr
 * @author		Mathias Bank
 * @copyright	Copyright (c) 2006, Mathias Bank
 * @since 		1.0
 * @see			http://www.mathias-bank.de/2006/08/18/passwort-sicherheit-ermitteln/
 */
class Password {

	const TOO_SMALL = 1;
	const NO_UPPERCASE_CHAR = 2;
	const NO_LOWERCASE_CHAR = 4;
	const NO_NUMBERIC_CHAR = 8;
	const NO_SPECIAL_CHAR = 16;
	const SAME_CHAR_SEQUENCE = 32;
	const ALPHABETICAL_SEQUENCE = 64;
	const KEYBOARD_SEQUENCE = 128;
	const NUMERICAL_SEQUENCE = 256;
	const LEXICON_WORD = 512;

	private static $vowels  = "aeiou";
	private static $consonants = "bcdfghjklmnprstvwxz";
	private static $specialchars = '!#$%&*+-/<=>?@^_~';
	private static $alphabet = "abcdefghijklmnopqrstuvqxyz0123456789";
	private static $keyboardSequences = array(
		"123", "456", "789", "369", "258", "147", "987", "654", "321", "012", "210",
		"asd", "qwe", "jkl", "fgh" // ...
	);

	/**
	 * Creates a mnemonic password which should be easy to remember, but also secure.
	 *
	 * @param int The minimum number of vowel/consonant pairs to use
	 * @param int The maximum number of vowel/consonant pairs to use
	 * @param int The minimum number of digits to use
	 * @param int The maximum number of digits to use
	 * @return string Generated password
	 */
	public static function generate($minpairs = 2, $maxpairs = 5, $minnumbers = 1, $maxnumbers = 3) {
		$password = "";
		$pairs = mt_rand($minpairs, $maxpairs);
		$lenv = strlen(self::$vowels) - 1;
		$lenc = strlen(self::$consonants) - 1;
		$usedBig = false; // Speichert, ob schon mal ein Großbuchstabe eingefügt wurde

		for($i = 1; $i <= $pairs; $i++) {
			//Per Zufall ein Großbuchstaben als Konsonant einfügen
			$password .= self::$vowels[mt_rand(0, $lenv)];

			if (mt_rand(0, 1) == 0 && $usedBig == false) {
				$password .= strtoupper(self::$consonants[mt_rand(0, $lenc)]);
				if (mt_rand(0, 1) == 0) {
					$usedBig = true;
				}
			}
			else {
				$password .= self::$consonants[mt_rand(0, $lenc)];
			}
		}

		//zufälliges Sonderzeichen einfügen
		$password .= self::$specialchars[mt_rand(0, strlen(self::$specialchars)-1)];

		//Zufällige Anzahl an Zahlen einfügen
		$sizeNumbers = mt_rand($minnumbers, $maxnumbers);
		for($i = 1; $i <= $sizeNumbers; $i++) {
			$password .= mt_rand(0, 9);
		}
		return $password;
	}

	/**
	 * Checks the security of a password.
	 *
	 * The following aspects will be concidered. The password...
	 * <ul>
	 * <li>... is not a word in a dictionary (uses Spellcheck class if possible).
	 * <li>... reaches the minimum length (4 chars).
	 * <li>... consists of uppercase and lowercase chars, digits and special chars.
	 * <li>... does not contain identical char sequences. (min. 3 chars)
	 * <li>... does not contain general keyboard sequences. (min. 3 chars)
	 * <li>... does not contain increasing or decreasing numbers in a row. (min. 3 chars)
	 * <li>... does not contain char sequences that match sequences in the alphabet. (min. 3 chars)
	 * </ul>
	 *
	 * @param string Password to check
	 * @param array Array contains the values why the password is not secure (see class constants)
	 * @param int Optimal length of a password
	 * @param string Sprachkürtzel eines Wörterbuchs zum Prüfen
	 * @return int 0 = Very low security, 100 = Very high Security
	 * @see Spellcheck::check()
	 * @todo Concider special chars like umlauts (see below)
	 */
	public static function check($password, &$failureArray = array(), $language = null, $optimalPasswordLength = 10) {
		//Rating initialisieren
		$rating = 100;
		$passwordLength = strlen($password);
		$smallPassword = strtolower($password); //Zum Vergleich mit Reihen

		// Check that pw is not shorter than 4 chars.
		if ($passwordLength < 4) {
			// Pw is too short, completely unsecure, return 0 now
			return 0;
		}

		// Check that pw is not a word in a dictionary.
		try {
			$spellcheck = new Spellcheck($language);
			if ($spellcheck->check($password) == false) {
				$failureArray[] = self::LEXICON_WORD;
				$rating -= 50;
			}
		}
		catch (CoreException $e) {
			// No other solution at the moment
		}

		// Remove 5 points for each char missing to the optimal pw length
		$diff = $optimalPasswordLength - $passwordLength;
		if ($diff > 0) {
			$failureArray[] = self::TOO_SMALL;
			$rating -= $diff * 5;
		}

		// Password consists of uppercase and lowercase chars, digits and special chars.
		// Todo: This does not tests for special chars like umlauts
		$smallChar = false;
		$bigChar = false;
		$numericChar = false;
		$specialChar = false;
		for($i = 0; $i < $passwordLength; $i++) {
			$ascii = ord($password[$i]);
			if ($ascii >= 48 && $ascii <= 57) {
				$numericChar = true;
			}
			elseif ($ascii >= 65 && $ascii <= 90) {
				$bigChar = true;
			}
			elseif ($ascii >= 97 && $ascii <= 122) {
				$smallChar = true;
			}
			elseif ($ascii >= 32 && $ascii <= 126) {
				$specialChar = true;
			}
		}
		// Remove points and add reason to failrue array
		if($smallChar == false) {
			$failureArray[] = self::NO_LOWERCASE_CHAR;
			$rating -= 15;
		}
		if($bigChar == false) {
			$failureArray[] = self::NO_UPPERCASE_CHAR;
			$rating -= 15;
		}
		if($numericChar == false) {
			$failureArray[] = self::NO_NUMBERIC_CHAR;
			$rating -= 20;
		}
		if($specialChar == false) {
			$failureArray[] = self::NO_SPECIAL_CHAR;
			$rating -= 10;
		}

		// Check for identical char sequences (min 3 chars)
		for ($i = 0; $i <= $passwordLength-3; $i++) {
			$excerpt = substr($smallPassword, $i, 3);
			if ($excerpt[0] == $excerpt[1] && $excerpt[1] == $excerpt[2]) {
				$failureArray[] = self::SAME_CHAR_SEQUENCE;
				$rating -= 20;
				break;
			}
		}

		// Check for increasing or decreasing numbers in a row. (min. 3 chars)
		foreach(self::$keyboardSequences as &$sequence) {
			if (strpos($smallPassword, $sequence) !== false) {
				$failureArray[] = self::KEYBOARD_SEQUENCE;
				$rating -= 15;
				break;
			}
		}

		//ABC oder Zahlenreihen (ab 3 Buchstaben) => 20 Punkte abziehen
		for ($i = 0; $i <= $passwordLength-3; $i++) {
			$excerpt = substr($smallPassword, $i, 3);
			if(strpos(self::$alphabet, $excerpt) !== false) {
				$failureArray[] = self::ALPHABETICAL_SEQUENCE;
				$rating -= 15;
				break;
			}
		}

		return ($rating > 0) ? $rating: 0;
	}

}
?>
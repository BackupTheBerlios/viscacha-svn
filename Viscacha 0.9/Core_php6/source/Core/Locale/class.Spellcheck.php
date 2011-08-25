<?php
/**
 * Viscacha - Flexible Website Management Solution
 *
 * Copyright (C) 2004 - 2010 by Viscacha.org
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package		Core
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License
 */

/**
 * Simple spell check class using the php ectension 'pspell'.
 *
 * @package		Core
 * @subpackage	Security
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class Spellcheck extends Singleton {

	private $handle;
	private $word;

	/**
	 * Constructs a new Spellcheck object.
	 *
	 * The language parameter is the language code which consists of the two letter ISO 639 language
	 * code and an optional two letter ISO 3166 country code after an underscore.
	 * Throws a core exception if pspell extension is not available or initialization failed.
	 *
	 * @param string Langauge code and additional country code separated by an underscore.
	 * @throws CoreException
	 */
	public function __construct($language) {
		$this->word = '';
		if (extension_loaded('pspell') == true && function_exists('pspell_new') == true) {
			$this->handle = pspell_new($language, "", "", "", PSPELL_NORMAL);
			if ($this->handle === false) {
				throw new CoreException('Could not load spellcheck instance');
			}
		}
		else {
			throw new CoreException('Extension "pspell" is not available');
		}
	}

	/**
	 * Checks a word against the dictionary.
	 *
	 * @param boolean Returns true if word is in dictionary, false if not.
	 */
	public function check($word) {
		$this->word = $word;
		if ($this->handle !== false && pspell_check($this->handle, $word) == true) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Returns suggestions for the last or the specified word.
	 *
	 * @param string Word to suggest alternatives for or null to use the word from the last check.
	 * @
	 */
	public function suggest($word = null) {
		if ($word == null) {
			$word = $this->word;
		}
		if ($this->handle !== false) {
			return pspell_suggest($this->handle, $word);
		}
		else {
			return array();
		}
	}

}
?>
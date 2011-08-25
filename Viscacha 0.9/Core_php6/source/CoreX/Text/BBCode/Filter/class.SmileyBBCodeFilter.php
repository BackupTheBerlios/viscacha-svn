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
 * @subpackage	Text
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * Smiley filter for the BBCodeCompiler.
 *
 * @package		Core
 * @subpackage	Text
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class SmileyBBCodeFilter implements BBCodeFilter {

	private $smileys;

	public function  __construct() {
		$this->smileys = array(
			':)' => 'client/images/smile.gif',
			':(' => 'client/images/frown.gif',
			':p' => 'client/images/tongue.gif'
		);
	}

	public function getType() {
		return self::POST;
	}

	public function compile($text) {
		foreach ($this->smileys as $search => $replace) {
			if (strpos($text, $search) !== false) {
				$text = preg_replace(
					'~(\r|\n|\t|\s|\>|\<|^)'.preg_quote($search, '~').'(\r|\n|\t|\s|\>|\<|$)~',
					'\1<img src="'.$replace.'" alt="'.$search.'" />\2',
					$text
				);
			}
		}
		return $text;
	}

}
?>
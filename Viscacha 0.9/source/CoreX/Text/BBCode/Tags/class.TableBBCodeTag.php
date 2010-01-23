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
 * Table BB-Code for the BBCodeCompiler.
 *
 * @package		Core
 * @subpackage	Text
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class TableBBCodeTag extends AbstractBlockBBCodeTag {

	public function getTagNames() {
		return array('table', 'tr', 'td', 'th');
	}

	public function getDisallowedChilds($tagName) {
		if ($tagName == 'table') {
			return array(self::DT_ALL);
		}
		elseif ($tagName == 'tr') {
			return array(self::DT_ALL);
		}
		else { // td, th
			return array('tr', 'td', 'th');
		}
	}

	public function getDisallowedChildsExceptions($tagName) {
		if ($tagName == 'table') {
			return array('tr');
		}
		elseif ($tagName == 'tr') {
			return array('td', 'th');
		}
		else { // td, th
			return array();
		}
	}

	public function compile(BBCodeToken $token) {
		if ($token->getTagName() == 'table') {
			$table = '';
			foreach ($token->getChilds() as $child) {
				if ($child instanceof BBCodeToken && $child->getTagName() == 'tr') {
					$table .= '<tr>';
					foreach ($child->getChilds() as $subchild) {
						if ($subchild instanceof BBCodeToken) {
							$cellContent = $this->compiler->compile($subchild, self::DT_ALL);
							if ($subchild->getTagName() == 'th') {
								$table .= "<th>{$cellContent}</th>";
							}
							elseif ($subchild->getTagName() == 'td') {
								$table .= "<td>{$cellContent}</td>";
							}
						}
					}
					$table .= '</tr>';
				}
			}
			return "<table border='1' cellspacing='0'>{$table}</table>";
		}
	}

}
?>
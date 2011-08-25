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
 * Interface for BB-Code tags.
 *
 * @package		Core
 * @subpackage	Text
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
interface BBCodeTag {

	const DT_ALL = 0;
	const DT_TEXT = 1;
	const DT_INLINE = 2;
	const DT_BLOCK = 3;

	public function getTagNames();

	public function isStandalone($tagName);

	public function compile(BBCodeToken $token);

	public function getDisplayType($tagName);

	public function getDisallowedChilds($tagName);

	public function getDisallowedChildsExceptions($tagName);

	public function setCompiler(BBCodeCompiler $compiler);

}
?>

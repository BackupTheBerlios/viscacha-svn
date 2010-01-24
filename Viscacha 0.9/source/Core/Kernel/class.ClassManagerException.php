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

Core::loadClass('Core.Kernel.InfoException');

/**
 * Exception for class ClassManager.
 *
 * @package		Core
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class ClassManagerException extends InfoException {

	private $index;

	/**
	 * Returns an array with additional information about the excpetion.
	 *
	 * The keys of the array are the class names, the values are the paths to the class files.
	 *
	 * @return	array	Data with keys as labels and values as data.
	 */
	public function getData() {
		return $this->index;
	}

	/**
	 * Sets the index data from the class manager for the exception.
	 *
	 * @param	array	Index from ClassManager
	 */
	public function setIndex($index) {
		if (is_array($index) == false) {
			$index = array();
		}
		$this->index = $index;
	}

}
?>
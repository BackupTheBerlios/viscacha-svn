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
 * Abstract class for Exceptions that have additional information.
 *
 * @package		Core
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @abstract
 */
abstract class InfoException extends Exception {

	protected static $objInExec;

	/**
	 * Constructs the Exception.
	 *
	 * @param	string	Error message
	 * @param	int		Error code (default: 0)
	 */
	public function __construct($message, $code = 0) {
		if (self::$objInExec instanceof self) {
			self::$objInExec->hardExit();
		}
		self::$objInExec = $this;
		parent::__construct($message, $code);
	}

	/**
	 * Returns an array with additional information about the excpetion.
	 *
	 * The array can contain as many details as you want.
	 * The keys of the elements are the labels/titles and the values is the variable data.
	 *
	 * @return	array	Data with keys as labels and values as data.
	 * @abstract
	 */
	public abstract function getData();

	/**
	 * Quit the program immediately with an error message.
	 */
	protected function hardExit() {
		exit(
			'Error: Recursive exception during the following exception - '.$this->getMessage().
				' (File: '.$this->getFile().', Line: '.$this->getLine().')'
		);
	}

}
?>
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
 * Exception for Core class.
 *
 * @package		Core
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class CoreException extends InfoException {

	private $data;

	/**
	 * Constructs the CoreException.
	 *
	 * @param	string	Core error message
	 * @param	int		Core error code (default: 0)
	 */
	public function __construct($message, $code = 0) {
		$this->data = array();
		parent::__construct($message, $code);
	}

	/**
	 * Returns an array with additional information about the excpetion.
	 *
	 * {@inheritdoc}
	 *
	 * @return	array	Data with keys as labels and values as data.
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * Sets additional information (single information).
	 *
	 * @param	mixed	Key for the data
	 * @param	mixed	Value for the data
	 */
	public function setData($key, $value) {
		$this->data[$key] = $value;
	}

	/**
	 * Sets additional information (whole array).
	 *
	 * Previously added data will be merged together with the data specified as parameter.
	 * This function accepts an array that is compatible with InfoEception::getData().
	 *
	 * @param	array	Array with the data
	 * @see InfoException::getData()
	 */
	public function setArrayData(array $data) {
		$this->data = array_merge($this->data, $data);
	}

}
?>
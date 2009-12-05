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
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
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
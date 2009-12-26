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

/**
 * ValidatorElement class that bundles the validation rules and filters for this element.
 *
 * @package		Core
 * @subpackage	Security
 * @author		Andreas Wilhelm
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @see			http://www.php.de/software-design/50128-formular-validierung.html
 */
class ValidatorElement {

	protected $name;
	protected $value;
	protected $validators = array();
	protected $errors = array();

	/**
	 * @param string Element name
	 */
	public function __construct($name, $value = null) {
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * Adds a validator to an element.
	 *
	 * @param AbstractValidator
	 * @param boolean Make this Validator optional (true) or not (false, default).
	 * @todo Find a better method for the in_array check (maybe a uniqueId method for validators)
	 */
	public function addValidator(AbstractValidator $validator, $optional = false) {
		// Don't add a validator with the same data multiple times
		if(in_array($validator, $this->validators) == false) {
			$this->validators[] = $validator;
		}
	}

	/**
	 * Returns the name of an element.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the value of an element.
	 *
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Assigns a value to an element.
	 *
	 * @param mixed
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	/**
	* Checks if any errrors accured in this element.
	*
	* @return Boolean
	*/
	public function isValid() {
		$isValid = true;
		foreach($this->validators as $validator) {
			if(!$validator->isValid($this->value) == false) {
				$isValid = false;
				$this->errors = array_merge($this->errors, $validator->getErrors());
			}
		}
		return $isValid;
	}

	/**
	* Returns the errors occured in an element.
	*
	* @access public
	* @return Array
	*/
	public function getErrors() {
		return $this->errors;
	}
}
?>
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
 * Validator class that bundles the ValidatorElement objects.
 *
 * Validation classes base on a concept discussed at php.de, see link.
 *
 * @package		Core
 * @subpackage	Security
 * @author		Matthias Mohr
 * @author		Andreas Wilhelm
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @see			http://www.php.de/software-design/50128-formular-validierung.html
 */
class Validator {

	protected $data = array();
	protected $elements = array();
	protected $errors = array();

	/**
	 * @param array Array with data to validate
	 */
	public function __construct($data) {
		$this->data = $data;
	}

	/**
	 * Adds an element to the Validator.
	 *
	 * If you specify an Element with a name that was used by another element before, this element
	 * won't overwrite the element specified before.
	 *
	 * @param ValidatorElement
	 */
	public function addElement(ValidatorElement $element) {
		$name = $element->getName();
		if (isset($this->data[$name]) == true) {
			$element->setValue($this->data[$name]);
		}

		// check if entry exists
		if(!isset($this->elements[$name])) {
			$this->elements[$name] = $element;
		}
		else {
			// Write a message to the log file as this can lead to unapplied Validators (risky)!
			ErrorHandling::getDebug()->addText(
				"Validation: You specified an element multiple times, this can be a security hole!"
			);
		}
	}

	/**
	 * Checks if any errors accured in this form.
	 *
	 * @return boolean
	 */
	public function isValid() {
		$isValid = true;
		foreach($this->elements as $element) {
			if($element->isValid() == false) {
				$isValid = false;
				$this->errors[$element->getName()] = $element->getErrors();
			}
		}
		return $isValid;
	}

	/**
	 * Returns the errors accured in a validator.
	 *
	 * @return array
	 */
	public function getErrors() {
		return $this->errors;
	}
}
?>
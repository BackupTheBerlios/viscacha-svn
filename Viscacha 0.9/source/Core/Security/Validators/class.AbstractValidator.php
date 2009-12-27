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
 * Abstract validator that has to be extended by all Validator rule classes.
 *
 * @package		Core
 * @subpackage	Security
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @see			http://www.php.de/software-design/50128-formular-validierung.html
 * @abstract
 */
abstract class AbstractValidator {

	private $errors;
	private $messages;
	protected $optional;

	/**
	 * Constructs a new validator rule.
	 */
	public function  __construct() {
		$this->reset();
		$this->messages = array();
	}

	/**
	 * Checks the specified parameter against the rules.
	 *
	 * @param mixed
	 * @return boolean returns true if valid, false if invalid.
	 */
	public abstract function isValid($value);

	/**
	 * Sets whether this validator is optional or not.
	 *
	 * @param boolean true = optional, false = required
	 */
	public function setOptional($optional) {
		$this->optional = $optional;
	}

	/**
	 * Returns the error codes as array.
	 *
	 * @return array
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * Returns the number of errors.
	 *
	 * @return int
	 */
	public function countErrors() {
		return count($this->errors);
	}

	/**
	 * Sets the human readable error messages.
	 *
	 * You have to specify an array with the error codes as keys and the messages itself as values.
	 *
	 * @param array
	 */
	public function setMessages(array $messages) {
		$this->messages = $messages;
	}

	/**
	 * Returns the error messages as array.
	 *
	 * The keys are the error codes and the values are the human readable messages.
	 * You have to specify the messages before with AbstractValidator::setMessages().
	 * If no error message is found for an error code the error code will be returned as value.
	 *
	 * @return array
	 */
	public function getMessages() {
		$messages = array();
		foreach ($this->errors as $code) {
			if (isset($this->messages[$code]) == true) {
				$messages[$code] = $this->messages[$code];
			}
			else {
				$messages[$code] = $code;
			}
		}
		return $messages;
	}

	/**
	 * Adds an error message to the error array.
	 *
	 * @param string Error code
	 */
	protected function setError($error) {
		$this->errors[] = $error;
	}

	/**
	 * Resets the validator to be able to validate another value.
	 */
	protected function reset() {
		$this->errors = array();
	}

}
?>
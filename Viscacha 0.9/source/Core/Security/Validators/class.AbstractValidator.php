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
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @see			http://www.php.de/software-design/50128-formular-validierung.html
 * @abstract
 */

abstract class AbstractValidator {

	private $errors;
	private $messages;

	public function  __construct() {
		$this->reset();
		$this->messages = array();
	}

	public abstract function isValid($value);

	public function getErrors() {
		return $this->errors;
	}

	public function countErrors() {
		return count($this->errors);
	}

	public function setMessages(array $messages) {
		$this->messages = $messages;
	}

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

	protected function setError($error) {
		$this->errors[] = $error;
	}

	protected function reset() {
		$this->errors = array();
	}

}
?>
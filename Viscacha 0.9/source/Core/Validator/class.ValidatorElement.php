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
 * ValidatorElement class that bundles the validation rules and filters for an element.
 *
 * You can add filters that are applied before or affter the rule based check.
 * These filters are applied to the values after calling the isValid()-method.
 *
 * @package		Core
 * @subpackage	Security
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class ValidatorElement {

	private $rules;
	private $prependFilter;
	private $appendFilter;
	private $label;
	private $originalValue;
	private $value;
	private $optional;
	private $errors;

	public function  __construct($name, $value, $optional = false) {
		$this->label = $name;
		$this->originalValue = $value;
		$this->value = null;
		$this->optional = $optional;
		$this->appendFilter = array();
		$this->prependFilter = array();
		$this->rules = array();
		$this->errors = array();
	}
	
	public function setLabel($label) {
		$this->label = $label;
	}

	public function setOptional($optional = true) {
		$this->optional = $optional;
	}

	public function addRule($rule, $args = array()) {
		if (!is_array($args)) {
			$args = array($args);
		}
		$this->rules[$rule] = $args;
		return $this;
	}

	public function prependFilter($filter, $args = array()) {
		if (!is_array($args)) {
			$args = array($args);
		}
		$this->prependFilter[$filter] = $args;
		return $this;
	}

	public function appendFilter($filter, $args = array()) {
		if (!is_array($args)) {
			$args = array($args);
		}
		$this->appendFilter[$filter] = $args;
		return $this;
	}

	public function getValue() {
		return $this->value;
	}

	public function getOriginalValue() {
		return $this->originalValue;
	}

	public function isValid() {
		$result = true;
		$this->value = $this->originalValue;
		foreach ($this->prependFilter as $name => $args) {
			$this->filter($name, $args);
		}
		foreach ($this->rules as $name => $args) {
			if ($this->validate($name, $args) === false) {
				$result = false;
			}
		}
		if ($result == false) {
			$this->value = null;
			// We use return after the loop to get all error messages
			return false;
		}
		foreach ($this->appendFilter as $name => $args) {
			$this->filter($name, $args);
		}
		return true;
	}

	public function getErrors() {
		return $this->errors;
	}

	private function validate($name, $args) {
		$context = explode('.', $name, 2);
		// Apply default validator if needed
		if (count($context) != 2) {
			$context = array('Default', $name);
		}

		if (strtolower($context[0]) == 'php') {
			// Using php functions we don't have the optional feature so we use empty() for that
			if ($this->optional == true && empty($context[1])) {
				$status = true;
			}
			else {
				array_unshift($args, $this->value);
				$status = call_user_func_array($context[1], $args);
			}
		}
		else {
			// Add the value and the optional state to the beginning of the argument array
			array_unshift($args, $this->value, $this->optional);
			$className = $context[0].'Validator';
			$status = call_user_func_array(array($className, $context[1]), $args);
			// Request the errors
			$errors = call_user_func(array($className, 'getErrors'));
			$this->errors = array_merge($this->errors, $errors);
		}
		return $status;
	}

	private function filter($name, $args) {
		$context = explode('.', $name, 2);
		// Apply default filter if needed
		if (count($context) != 2) {
			$context = array('Default', $name);
		}

		array_unshift($args, $this->value);

		if (strtolower($context[0]) == 'php') {
			$this->value = call_user_func_array($context[1], $args);
		}
		else {
			$this->value = call_user_func_array(array($context[0].'Filter', $context[1]), $args);
		}
	}

}
?>
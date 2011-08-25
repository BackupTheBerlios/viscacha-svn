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
 * @subpackage	Validator
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License
 */

/**
 * ValidatorElement class that bundles the validation rules and filters for an element.
 *
 * You can add filters that are applied before or affter the rule based check.
 * These filters are applied to the values after calling the isValid()-method.
 *
 * @package		Core
 * @subpackage	Validator
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @todo		Implement a method to set another lang key for a error code as this could be useful for general regexp checks for example.
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
			// Add the optional state and the value to the beginning of the argument array
			// 0 => value, 1 => optional, 2 => Argument 1, 3 => Argument 2, ...
			array_unshift($args, $this->optional, $this->value);
			$className = $context[0].'Validator';
			// Preprend a _ to the method name to use AbstractValidator::__callStatic()
			// This is needed because we have to do some work before calling the validation method
			$status = call_user_func_array(array($className, '_'.$context[1]), $args);
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
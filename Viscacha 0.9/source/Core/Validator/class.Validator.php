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
 * Validation rules and filter can be called with a internal name. The format is a namespace and a
 * function name. There is a default namespace (default) which you don't have to specify, just skip
 * that. To run php functions just use the namespace php.
 * Note: All names are case insensitive.
 *
 * To write your own classes with filters the class names need a special suffix: Filter.
 * The namespace will be the part before the suffix. The methods implementing custom filters are
 * static, and have ro return the filtered input. The restrictions on the return type for using
 * the namespace php and the internal php functions are the same.
 * 
 * Validation rules can be implemented using the abstract class AbstractValidator, see it for
 * more information. Using the namespace php and an internal php functions as validatior it needs to
 * return a boolean. The error code/message for php functions is always a general one.
 *
 * Note: You need to add all elements you wish to use later as calling isValid() will remove all
 * values without an appropriate element.
 *
 * Examples - Usage of namespaces and rule/filter names:
 * <ul>
 * <li>addRule('between', array(1,10)) will call DefaultValidator::_between($value, 1, 10)
 * <li>addRule('php.trim', '\r\n') will call trim($value, '\r\n')
 * <li>addRule('Forum.UserExists') will call ForumValidator::_userExists($value)
 * <li>appendFiler('forum.bbcode', true) will call ForumFilter::_bbcode($value, true)
 * <li>prependFiler('xss') will call DefaultFilter::_xss($value)
 * </ul>
 *
 * Example - Using the Validator class:
 * <code>
 * $validator = new Validator($_REQUEST);
 * $validator->setLanguage('validator.long');
 * // Add elements
 * $validator->addElement('test', $lang->phrase('label_test'))->addRule('between', array(1, 10));
 * $element2 = $validator->addElement('date', $lang->phrase('label_date'));
 * $element2->addRule('alnum');
 * $element2->prependFilter('php.trim'); // Calls phps internal function trim
 * $element2->appendFilter('normalizeDate', array('d.m.Y'));
 * // Validate and filter data and handle result appropriate
 * if ($validator->isValid() == false) {
 *   $output = $validator->getErrors();
 * }
 * else {
 *   $output = $validator->getValues();
 * }
 * </code>
 *
 * @package		Core
 * @subpackage	Security
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @see			AbstractValidator
 */
class Validator {

	private $elements;
	private $data;

	public function __construct($data) {
		$this->elements = array();
		$this->data = $data;
	}
	
	public function setLanguage($x) {
		// Do something with language...
	}

	public function addElement($name, $label = null) {
		$element = new ValidatorElement($name, $this->getData($name));
		if ($label !== null) {
			$element->setLabel($label);
		}
		$this->elements[$name] = $element;
		return $element;
	}

	public function addOptionalElement($name, $label = null) {
		$element = new ValidatorElement($name, $this->getData($name), true);
		if ($label !== null) {
			$element->setLabel($label);
		}
		$this->elements[$name] = $element;
		return $element;
	}

	public function isValid() {
		$status = true;
		$this->data = array();
		foreach ($this->elements as $varname => $element) {
			if ($element->isValid() == false) {
				$status = false;
			}
			$this->data[$varname] = $element->getValue($varname);
		}
		return $status;
	}
	
	public function getErrors($element = null) {
		$errors = array();
		foreach ($this->elements as $element) {
			$errors = array_merge($errors, $element->getErrors());

		}
		return $errors;
	}

	public function getValue($varname) {
		if (isset($this->data[$varname])) {
			return $this->data[$varname];
		}
		else {
			return null;
		}
	}

	public function getValues() {
		return $this->data;
	}

	private function getData($varname) {
		if (!isset($this->data[$varname])) {
			$this->data[$varname] = null;
		}
		return $this->data[$varname];
	}

}
?>
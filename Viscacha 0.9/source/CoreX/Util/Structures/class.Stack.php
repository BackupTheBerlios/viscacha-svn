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
 * @subpackage	Util
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * Implementation of the stack data structure.
 *
 * A stack is a LIFO (Last In First Out) data structure.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class Stack {

	/**
	 * The stack data
	 * @var array
	 */
	private $stack;

	/**
	 * Constructs an Stack object containing no items.
	 */
	public function __construct() {
		$this->stack = array();
	}

	/**
	 * Pushs the element onto the stack.
	 *
	 * @param mixed The element to be pushed onto the stack.
	 */
	public function push($element) {
		array_push($this->stack, $element);
	}

	/**
	 * Removes the element at the top of this stack and returns that element.
	 *
	 * Returns null if the stack is empty.
	 *
	 * @return mixed The element at the top of this stack or null.
	 */
	public function pop() {
		if ($this->isEmpty() == true) {
			return null;
		}
		else {
			return array_pop($this->stack);
		}
	}

	/**
	 * Returns the object at the top of this stack without removing it from the stack.
	 *
	 * Returns null if the stack is empty.
	 *
	 * @return mixed The element at the top of this stack or null.
	 */
	public function top() {
		if ($this->isEmpty() == true) {
			return null;
		}
		else {
			$count = count($this->stack);
			return $this->stack[$count-1];
		}
	}

	/**
	 * Tests if this stack is empty.
	 *
	 * @return boolean TRUE if this stack contains no items, FALSE otherwise.
	 */
	public function isEmpty() {
		return (count($this->stack) == 0);
	}

	/**
	 * Returns the lenght of the Stack.
	 *
	 * @return int The lenght of the stack.
	 */
	public function getLength() {
		return count($this->stack);
	}

	/**
	 * Returns the stack as an enumerated array.
	 *
	 * The element at the top has the highest key and the element first added to the stack has the
	 * key 0. If the parameter is set to TRUE the whole array is reversed before it is returned.
	 * The array pointer is pointing to the element with the key 0.
	 *
	 * @param boolean Set this to TRUE ro reverse the whole stack. Default is false.
	 * @return array Array representing the stack
	 */
	public function getArray($reverse = false) {
		if ($reverse == true) {
			$stack = array_reverse($this->stack);
		}
		else {
			$stack = $this->stack;
		}
		reset($stack);
		return $stack;
	}
}
?>
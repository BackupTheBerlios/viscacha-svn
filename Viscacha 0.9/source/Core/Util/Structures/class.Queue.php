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
 * Implementation of the queue data structure.
 *
 * A queue is a FIFO (First In First Out) data structure.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class Queue {
	/**
	 * The Stack data
	 * @var array
	 */
	private $queue;

	/**
	 * Constructs an Queue object containing no items.
	 */
	public function __construct() {
		$this->queue = array();
	}

	/**
	 * Adds the element to the end of the queue.
	 *
	 * @param mixed The element to be pushed at the end of the queue.
	 */
	public function enqueue($element) {
		array_push($this->queue, $element);
	}

	/**
	 * Removes the element at the beginning of this queue and returns that element.
	 *
	 * Returns null if the queue is empty.
	 *
	 * @return mixed The element at the beginning of this queue or null.
	 */
	public function dequeue() {
		if ($this->isEmpty() == true) {
			return null;
		}
		else {
			return array_shift($this->queue);
		}
	}

	/**
	 * Returns the object at the beginning of this queue without removing it.
	 *
	 * Returns null if the queue is empty.
	 *
	 * @return mixed The element at the top of this queue or null.
	 */
	public function front() {
		if ($this->isEmpty() == true) {
			return null;
		}
		else {
			return $this->queue[0];
		}
	}

	/**
	 * Tests if this queue is empty.
	 *
	 * @return boolean TRUE if this queue contains no items, FALSE otherwise.
	 */
	public function isEmpty() {
		return (count($this->queue) == 0);
	}

	/**
	 * Returns the lenght of the Queue.
	 *
	 * @return int The lenght of the queue.
	 */
	public function getLength() {
		return count($this->queue);
	}

	/**
	 * Returns the queue as an enumerated array.
	 *
	 * The element at the front/beginning has the lowest key (0) and the element added last has the
	 * highest key. If the reverse parameter is set to true the whole array is reversed before it
	 * is returned. The array pointer is pointing to the element with the key 0.
	 *
	 * @param boolean Set this to TRUE ro reverse the whole queue. Default is false.
	 * @return array Array representing the queue
	 */
	public function getArray($reverse = false) {
		if ($reverse == true) {
			$queue = array_reverse($this->queue);
		}
		else {
			$queue = $this->queue;
		}
		reset($queue);
		return $queue;
	}
}
?>
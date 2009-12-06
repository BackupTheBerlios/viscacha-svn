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
 * Several useful static methods to manipulate/work with arrays.
 *
 * This package does NOT represent an array and you can't define any content to this class.
 * This class is abstract as there are only static methods.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 * @abstract
 */
abstract class Arrays {

	/**
	 * Search a value in a (multidimensional) array - replacement for array_search().
	 *
	 * This function returns the key of the element that contains the specified keyword.
	 * The element can be an array, it will be recursively scanned for the keyword.
	 * The key of the parent element (not the child element) which contains the keyword
	 * will be returned. If there are more than one elements with the specified keyword
	 * the key of the first found element will be returned. If nothing is found boolean
	 * FALSE will be returned.
	 *
	 * Example:
	 * <code>
	 * $mimeTypes = array(
	 *	'image/gif' => array('gif'),
	 *	'image/jpeg' => array('jpe', 'jpeg', 'jpg'),
	 *	'image/png' => array('png', 'x-png'),
	 * );
	 * echo Arrays::find($mimeTypes, 'jpg');
	 * // Output: image/jpeg
	 * </code>
	 *
	 * @param array Array to search in
	 * @param mxied Text to find
	 * @return mixed Key of array or false on failure
	 */
	public static function find($array, $keyword) {
		foreach($array as $key => $value) {
			if($keyword === $value || (is_array($value) == true && Arrays::find($array, $value) !== false)) {
				return $key;
			}
		}
		return false;
	}

    /**
     * Sorts the specified array of objects internally into ascending order
	 * (the result can be different, that depends on the implementation of
	 * the compareTo method).
	 *
	 * All elements in the array must implement the Comparable interface.
	 * Furthermore, all elements in the array must be from the same type and
	 * must be comparable.
     *
     * This sort is guaranteed to be <i>stable</i>:  equal elements will
     * not be reordered as a result of the sort. The sorting algorithm is
	 * mergesort. This algorithm offers guaranteed n*log(n) performance.
	 *
	 * Note: This function assigns new keys to the elements in array .
	 * It will remove any existing keys that may have been assigned, rather
	 * than just reordering the keys.
     *
     * @param	array	The array to be sorted
     * @return	boolean	true on success, false on failure
     */
	public static function sort(&$array) {
		return self::mergeSort($array);
	}

	/**
	 * Mergesort for an array with objects all from the same type
	 * implementing the Comparable interface.
	 *
	 * This sort is guaranteed to be <i>stable</i>:  equal elements will
     * not be reordered as a result of the sort. The sorting algorithm is
	 * mergesort. This algorithm offers guaranteed n*log(n) performance.
	 *
	 * Note: This function assigns new keys to the elements in array .
	 * It will remove any existing keys that may have been assigned, rather
	 * than just reordering the keys.
	 *
	 * @param	array	Array to sort
	 * @return	boolean	true on success, false on failure
	 * @author	sreid at sea-to-sky dot net
	 * @see		http://www.php.net/manual/en/function.usort.php#38827
	 * @todo	Implement check with class_implements() and return value false.
	 */
	private static function mergesort(&$array) {
	    // Arrays of size < 2 require no action.
	    if (count($array) < 2) {
	    	return true;
    	}

	    // Split the array in half
	    $halfway = count($array) / 2;
	    $array1 = array_slice($array, 0, $halfway);
	    $array2 = array_slice($array, $halfway);

	    // Recurse to sort the two halves
	    self::mergesort($array1);
	    self::mergesort($array2);

	    // If all of $array1 is <= all of $array2, just append them.
		 // Original code: call_user_func($cmpFunction, end($array1), $array2[0])
	    if (end($array1).compareTo($array2[0]) < 1) {
	        $array = array_merge($array1, $array2);
	        return true;
	    }

	    // Merge the two sorted arrays into a single sorted array
	    $array = array();
	    $ptr1 = 0;
		$ptr2 = 0;
	    while ($ptr1 < count($array1) && $ptr2 < count($array2)) {
			// Original code: call_user_func($cmpFunction, $array1[$ptr1], $array2[$ptr2])
	        if ($array1[$ptr1].compareTo($array2[$ptr2]) < 1) {
	            $array[] = $array1[$ptr1++];
	        }
	        else {
	            $array[] = $array2[$ptr2++];
	        }
	    }

	    // Merge the remainder
	    while ($ptr1 < count($array1)) {
	    	$array[] = $array1[$ptr1++];
    	}
	    while ($ptr2 < count($array2)) {
	    	$array[] = $array2[$ptr2++];
    	}
    	return true;
	}

	/**
	 * Checks whether an array is empty.
	 *
	 * An array is empty if there is no value in it that passes the empty() check.
	 *
	 * @see empty()
	 * @todo Enhance documentation
	 */
	public static function isEmpty(array $array) {
		$array = array_unique($array);
		if (count($array) == 0) {
			return true;
		}
		elseif (count($array) == 1) {
			$current = current($array);
			if (empty($current)) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			foreach ($array as $val) {
				if (!empty($val)) {
					return false;
				}
			}
			return true;
		}
	}

}
?>
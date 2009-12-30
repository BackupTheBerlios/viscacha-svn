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
	public static function find(array $array, $keyword) {
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
	public static function sort(array &$array) {
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
	private static function mergesort(array &$array) {
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

	    // If all of $array 1 is <= all of $array2, just append them.
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
	 * An array is empty if all elements (if any) pass the empty() check. The second parameter
	 * specifies whether to search the array recursive or not.
	 *
	 * Example for the differences between recursive check and first level check only:
	 * array(array()) with recurisve check is empty, with first level check is NOT empty.
	 *
	 * @param array Array to check
	 * @param boolean Enables recursive check (true) or not (false).
	 * @return true if array is empty, false if not.
	 */
	public static function isEmpty(array $array, $recursive = false) {
		if (count($array) > 0) {
			// Some tests fail with: $array = array_unique($array); [Reason unknown]
			foreach ($array as $val) {
				if ($recursive == true && is_array($val)) {
					$return = self::isEmpty($val, $recursive);
					if ($return === false) {
						return false;
					}
				}
				elseif (!empty($val)) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Simple Emulation of xPath for arrays.
	 *
	 * With this function you can find (and change) values in an array by a path like
	 * "abc/def/ghi" or "abc/*=value/ghi. Each level of the array is separated by a slash ('/').
	 * The names specified between the slashes have to match the keys, but you can also use a
	 * wildcard (*) to search in all keys on that level. The wildcard can't be used on the last
	 * level. The syntax of the search query limits the array keys a bit, you cant use slashes in
	 * array keys and you cant search for keys named '*'.
	 *
	 * The function returns true on success and false if nothing was found.
	 *
	 * You can use this function in a Get Mode (1) and in a Set Mode (2):
	 * <ul>
	 * <li>Get Mode: If you specify a variable that is null (an unset variable which is null, too)
	 * as third parameter, the search result will be assigned to this variable after the function
	 * call. If the function returns false (no search result) the third parameter will be null.
	 * When using the wildcard * and results are found every result will be added to an array that
	 * has the keys from the level where the wilcard was used and the search results as values.
	 * <li>Set Mode: If you specify something unequal to null as third parameter the search result
	 * will be replace with the value given in the original array. When using the wildcard * and
	 * multiple results are found every result will be replaced.
	 * Note: As the third parameter takes a reference you have to specify a variable, you can't
	 * just specify an integer, a string, ... in the function call as PHP can't reference that,
	 * but you can assign a variable in the function call, of course.
	 * </ul>
	 *
	 * Original idea by {@link http://www.jonasjohn.de/snippets/php/array-get-path.htm Jonas John}.
	 *
	 * @param array Array/data to search in
	 * @param string Path to follow
	 * @param array Holds the result of the search or null on failure
	 * @return boolean true on success, false on failure
	 */
	public static function xPath(array &$data, $path, &$result) {
		// Is there a / in the path?
		if (strpos($path, '/') !== false) {
			// More than one key, separate key from path
			list($key, $path) = explode('/', $path, 2);

			if ($key != '*' && isset($data[$key]) == true && is_array($data[$key]) == true) {
				// If $data is an array and the next key is set, call this method again (recursion).
				return self::xPath($data[$key], $path, $result);
			}
			elseif ($key == '*' && is_array($data) == true) {
				// There is a wildcard on this level, loop through all elements
				$resultArray = array(); // For get mode
				$status = false; // For set mode
				foreach ($data as $array_key => &$array_value) {
					// Not an array, we can't search there... go to next element
					if (!is_array($array_value)) {
						continue;
					}

					if ($result === null) { // We are in get mode
						// $result2 is the array we save our result in, must be null on every iteration
						$result2 = null;
						$status = self::xPath($array_value, $path, $result2);
						if ($status == true) {
							// Oh we found something, add to our array
							$resultArray[$array_key] = $result2;
						}
					}
					else { // We are in set mode
						$temp = $result; // Security against overwriting $result in case of an error
						if(self::xPath($array_value, $path, $result) == true) {
							$status = true;
						}
						$result = $temp; // Write back
					}
				}

				if ($result === null) { // We are in get mode
					// Return true when something was found, false in the other case
					$result = $resultArray;
					return (count($result) > 0);
				}
				else { // we are in set mode
					return $status;
				}
			}
		}
		else {
			// Last key, lets bring this to an end
			if (isset($data[$path]) == true) {
				// Yes, there is also the last key in the array
				if ($result === null) { // We are in get mode
					$result = $data[$path];
				}
				else { // We are in set mode
					$data[$path] = $result;
				}
				return true;
			}
		}

		// An error occured (Key not found, data invalid, ...)
		$result = null;
		return false;
	}

}
?>
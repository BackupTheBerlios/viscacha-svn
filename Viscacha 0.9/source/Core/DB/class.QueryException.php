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
 * @subpackage	DB
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * Exception for errors during queriing the database.
 *
 * @package		Core
 * @subpackage	DB
 * @author		Matthias Mohr
 * @since 		1.0
 */
class QueryException extends DatabaseException {

	/**
	 * Containts the query.
	 * @var string
	 */
	protected $query;

	/**
	 * Returns a detailed error message.
	 *
	 * The error message contains:
	 * 1. Error Number (only if greater than zero)
	 * 2. Error Message
	 * 3. File Name
	 * 4. Line Number of the file
	 * 5. Executed SQL Query (optional)
	 *
	 * @return string All error details as string
	 */
	public function __toString() {
		$error = parent::__toString();
		if (empty($this->query) == false) {
			$query = Strings::replaceLineBreaks($this->query, "\t");
			$error .= "\r\nQuery: {$query}";
		}
		return $error;
	}

	/**
	 * Sets the query.
	 *
	 * @param string Query
	 */
	public function setQuery($query) {
		$this->query = $query;
	}

	/**
	 * Returns the query.
	 *
	 * @return string Query
	 */
	public function getQuery() {
		return $this->query();
	}

	/**
	 * Returns an array with additional information about the excpetion.
	 *
	 * The array contains one element.
	 * The key is "Query" and contains the query which caused the exception.
	 *
	 * @return array Data with keys as labels and values as data.
	 */
	public function getData() {
		return array('Query' => $this->query);
	}
}
?>
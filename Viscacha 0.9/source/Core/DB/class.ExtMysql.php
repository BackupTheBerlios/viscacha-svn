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

Core::loadClass('Core.DB.Database');
Core::loadClass('Core.DB.VendorMySQL');

/**
 * Database driver for MySQL.
 *
 * This driver is for older MySQL installations, mysql extension is used.
 *
 * @package		Core
 * @subpackage	DB
 * @author		Matthias Mohr
 * @since 		1.0
 */
class ExtMySQL extends VendorMySQL {

	protected $tempData;

	/**
	 * Initializes the class and the variables.
	 *
	 * No connection will be established. Use the methods connect() and selectDB() to open a connection.
	 */
	public function __construct() {
		parent::__construct();
		$this->system = 'MySQL';
	}

	/**
	 * Disconnect from the database and roll back open transactions.
	 **/
	public function __destruct() {
		parent::__destruct();
	}

	/**
	 * Returns the number of rows affected by the last INSERT, UPDATE, or DELETE query.
	 *
	 * If the last query was invalid, this function will return -1.
	 *
	 * @return int Affected rows by the last query
	 **/
	public function affectedRows() {
		$id = @mysql_affected_rows($this->connection);
		return is_numeric($id) ? $id : -1;
	}

	/**
	 * Attempts to open a connection to the sql server.
	 *
	 * The host can be either a host name or an IP address. Passing the null value or the
	 * string "localhost" to this parameter, the local host is assumed. If successful,
	 * the the function will return an object representing the connection to the database,
	 * or null on failure. The port and socket parameters are used in conjunction with the
	 * host parameter to further control how to connect to the database server. The port
	 * parameter specifies the port number to attempt to connect to the MySQL server on,
	 * while the socket parameter specifies the socket or named pipe that should be used.
	 * If you specify a port and a socket, only the port will be used.
	 * Throws a DatabaseException if opening the connection fails.
	 *
	 * If one of the parameters host, username, port are null, default data will be used:
	 * Host: localhost<br>
	 * Username: root<br>
	 * Password: <empty><br>
	 * Port: 3306<br>
	 *
	 * Note: The database will not be selected! You have to call selectDB() before you can work with the database.
	 *
	 * The charset is set to UTF-8 with <code>SET NAMES 'UTF8'</code>.
	 *
	 * @param string Host
	 * @param string Username
	 * @param string Passwort
	 * @param int Port
	 * @param string Socket
	 * @throws DatabaseException
	 */
	public function connect($username = null, $password = null, $host = null, $port = null, $socket = null) {
		$this->username = $username === null ? 'root' : $username;
		$this->password = $password === null ? ''  : $password;
		$this->host = $host === null ? 'localhost' : $host;
		$this->port = ($port === null && $this->socket == null) ? '3306' : $port;
		$this->socket = $port === null ? $socket : null;

		$host = $this->host;
		if (Number::isNatural($this->port)) {
			$this->host += ":{$this->port}";
		}
		elseif ($this->socket !== null) {
			$this->host += ":{$this->socket}";
		}
		$this->connection = @mysql_connect($host, $this->username, $this->password);

		if ($this->hasConnection() == false) {
			throw new DatabaseException('Could not connect to database! Pleasy try again later or check the database settings!');
		}
		else {
			$this->setUTF8();
		}
	}

	/**
	 * Closes a previously opened database connection.
	 *
	 * If no connection is open TRUE will be returned.
	 *
	 * @return boolean true on success, false on failure
	 **/
	public function disconnect() {
		if ($this->hasConnection() == false) {
			return true;
		}
		return mysql_close($this->connection);
	}

	/**
	 * Returns an internal error message for the last error.
	 *
	 * @return string Error message
	 **/
	public function error() {
		if ($this->hasConnection() == false) {
			return null;
		}
		return @mysql_error($this->connection);
	}

	/**
	 * Returns an internal error number for the last error.
	 *
	 * @return int Error number
	 **/
	public function errno() {
		if ($this->hasConnection() == false) {
			return 0;
		}
		return @mysql_errno($this->connection);
	}

	/**
	 * Escapes special characters in a string for use in a SQL statement.
	 *
	 * You can either specify an string to be escaped or an array.
	 * Each array element will be escaped recursively.
	 *
	 * @param mixed String or array to escape
	 * @return mixed Escaped string or array
	 **/
	public function escapeString($data) {
		if (is_array($data) == true) {
			foreach($data as $key => $value){
				$data[$key] = $this->escapeString($value);
			}
		}
		else {
			$data = mysql_real_escape_string($data, $this->connection);
		}
		return $data;
	}

	/**
	 * Fetch a result row as an associative array
	 *
	 * Returns an associative array that corresponds to the fetched row or null if there are no more rows.
	 * The function is used to return an associative array representing the next row in the result set for
	 * the result represented by the result parameter, where each key in the array represents the name of
	 * one of the result set's columns. If two or more columns of the result have the same field names,
	 * the last column will take precedence. To access the other column(s) of the same name, you either
	 * need to access the result with numeric indices by using fetchNum() or add alias names.
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 *
	 * @param mixed $result
	 * @return array Result in an associative array
	 **/
	public function fetchAssoc($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		return mysql_fetch_assoc($result);
	}

	/**
	 * Get a result row as an enumerated array.
	 *
	 * Fetches one row of data from the result set represented by result and returns it as an enumerated array,
	 * where each column is stored in an array offset starting from 0 (zero). Each subsequent call to the
	 * function will return the next row within the result set, or null if there are no more rows.
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 *
	 * @param resource Result set
	 * @return array Enumerated array or null
	 **/
	public function fetchNum($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		return mysql_fetch_row($result);
	}

	/**
	 * Returns the current row of a result set as an object.
	 *
	 * Returns the current row result set as an object where the attributes of the object represent the names
	 * of the fields found within the result set. If no more rows exist in the current result set, null is returned.
	 * If two or more columns of the result have the same field names, the last column will take precedence.
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 *
	 * @param resource Result set
	 * @return object Object or null
	 **/
	public function fetchObject($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		return mysql_fetch_object($result);
	}

	/**
	 * Frees the memory associated with a result.
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 *
	 * @param resource Result set
	 **/
	public function freeResults($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		if ($this->isResultSet($result) == true) {
			mysql_free_result($result);
		}
	}

	/**
	 * Returns the ID used in the last INSERT query.
	 *
	 * null is returned on failure or when there was no last INSERT query.
	 *
	 * @return int ID or null
	 **/
	public function insertID() {
		$id = mysql_insert_id($this->connection);
		return String::isNatural($id) ? $id : null;
	}

	/**
	 * Returns true if connection has been established successfully.
	 *
	 * @return boolean
	 */
	function hasConnection(){
		return is_resource($this->connection);
	}

	/**
	 * Returns true if the specified parameter is a valid result set.
	 *
	 * @return boolean
	 */
	function isResultSet($result){
		return is_resource($result);
	}

	/**
	 * Gets the number of rows in a result.
	 *
	 * The use depends on whether you use buffered or unbuffered result sets.
	 * In case you use unbuffered resultsets this function will not correct the
	 * correct number of rows until all the rows in the result have been retrieved.
	 *
	 * @param resource Result set
	 * @return int Number of rows
	 **/
	public function numRows($result = null) {
		if ($result == null) {
	    	$result = $this->result;
	    }
	    return mysql_num_rows($result);
	}

	/**
	 * Performs a raw query on the database.
	 *
	 * On failure a QueryExcpetion will be thrown.
	 *
	 * @throws QueryException
	 * @param string Single raw Query
	 * @return mixed Result set for a select statement, a boolean for other statements (true on success, false on failure)
	 **/
	public function rawQuery($query){
		$this->benchmark['count']++;

		$this->debug->startClock($query);

		$this->result = @mysql_query($query, $this->connection);
		if ($this->result === false) {
			$this->queryError($query);
		}

		$time = $this->debug->stopClock($query);

		$this->benchmark['time'] += $time;
		$this->benchmark['queries'][] = array('query' => $query, 'time' => $time);

	    return $this->result;
	}

	/**
	 * Adjusts the result pointer to the first row in the result set.
	 *
	 * @param resource Result set
	 * @return boolean Returns true on success, false on failure
	 **/
	public function resetResult($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		return mysql_data_seek($result, 0);
	}

	/**
	 * Selects the default database to be used when performing queries against the database connection.
	 *
	 * @param string Database name
	 * @param string Prefix for tables
	 * @return boolean true on success, false on failure
	 **/
	public function selectDB($database, $prefix = '') {
		$this->database = $database;
		$this->pre = $prefix;
		return @mysql_select_db($this->database, $this->connection);
	}

	/**
	 * Returns a string representing the version of the database server.
	 *
	 * @return string Version or an empty string on failure
	 **/
	public function version() {
		$version = @mysql_get_server_info();
		return empty($version) ? '' : $version;
	}

}
?>
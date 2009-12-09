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
	 * No connection will be established. Use the methods ExtMySQL::connect() and
	 * ExtMySQL::selectDB() to open a connection and select a database.
	 */
	public function __construct() {
		parent::__construct();
		$this->system = 'MySQL';
	}

	/**
	 * Returns the number of rows affected by the last INSERT, UPDATE or DELETE query.
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
	 * Note: The database will not be selected! You have to call ExtMySQL::selectDB() before you can
	 * work with the database.
	 *
	 * The charset is set to UTF-8 with VendorMySQL::setUTF8().
	 *
	 * @param string Host
	 * @param string Username
	 * @param string Passwort
	 * @param int Port
	 * @param string Socket
	 * @throws DatabaseException
	 */
	public function connect($username = null, $password = null, $host = null,
							$port = null, $socket = null) {
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
			throw new DatabaseException(
				'Could not connect to database! '.
					'Pleasy try again later or check the database settings!'
			);
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
	 */
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
	 */
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
	 */
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
	 * @param string|array String or array to escape
	 * @return string|array Escaped string or array
	 **/
	public function escapeString($data) {
		if (is_array($data) == true) {
			foreach($data as $key => $value) {
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
	 * {@inheritdoc}
	 *
	 * @param	mixed	Result set or null
	 * @return	array	Associative array or null
	 */
	public function fetchAssoc($result = null) {
		if ($result == null) {
			$result = $this->result;
		}

		$row = mysql_fetch_assoc($result);
		return ($row !== false) ? $row : null;
	}

	/**
	 * Get a result row as an enumerated array.
	 *
	 * {@inheritdoc}
	 *
	 * @param resource Result set
	 * @return array Enumerated array or null
	 **/
	public function fetchNum($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		$row = mysql_fetch_row($result);
		return ($row !== false) ? $row : null;
	}

	/**
	 * Returns the current row of a result set as an object.
	 *
	 * {@inheritdoc}
	 *
	 * @param resource Result set
	 * @return object Object or null
	 **/
	public function fetchObject($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		$row = mysql_fetch_object($result);
		return ($row !== false) ? $row : null;
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
	 */
	public function insertID() {
		$id = mysql_insert_id($this->connection);
		return String::isNatural($id) ? $id : null;
	}

	/**
	 * Returns true if connection has been established successfully.
	 *
	 * @return boolean
	 */
	public function hasConnection() {
		return is_resource($this->connection);
	}

	/**
	 * Returns true if the specified parameter is a valid result set.
	 *
	 * @return boolean
	 */
	public function isResultSet($result) {
		return is_resource($result);
	}

	/**
	 * Gets the number of rows in a result set.
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
	 * Returns the result set for a select statement, a boolean for other statements (true on
	 * success, false on failure). On failure a QueryExcpetion will be thrown.
	 *
	 * @throws QueryException
	 * @param string Single raw Query
	 * @return mixed Result set for a select statement, a boolean for other statements.
	 */
	public function rawQuery($query) {
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
	 */
	public function resetResult($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		return mysql_data_seek($result, 0);
	}

	/**
	 * Select the default database to use when performing queries against the database.
	 *
	 * @param string Database name
	 * @param string Prefix for tables
	 * @return boolean true on success, false on failure
	 */
	public function selectDB($database, $prefix = null) {
		$this->database = $database;
		$this->pre = $prefix;
		return @mysql_select_db($this->database, $this->connection);
	}

	/**
	 * Returns a string representing the version of the database server.
	 *
	 * @return string Version or an empty string on failure
	 */
	public function version() {
		$version = @mysql_get_server_info();
		return empty($version) ? '' : $version;
	}

}
?>
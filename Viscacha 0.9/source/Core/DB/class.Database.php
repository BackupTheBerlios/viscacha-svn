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
 * Abstract class database, that has to be extended by all database drivers.
 *
 * Note: All data should be in UTF-8. Keep this in mind!
 *
 * @package		Core
 * @subpackage	DB
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */
abstract class Database {

	/**
	 * Contains the prefix for the database tables.
	 * @var string
	 */
	protected $pre;
	/**
	 * Contains the database driver name.
	 * @var string
	 */
	protected $system;
	/**
	 * Contains the database vendor name.
	 * @var string
	 */
	protected $vendor;
	/**
	 * Benchmark data.
	 * @var array
	 */
	protected $benchmark;
	/**
	 * The connection resource.
	 * @var resource
	 */
	protected $connection;
	/**
	 * Contains the host name.
	 * @var string
	 */
	protected $host;
	/**
	 * Contains the user name for the database.
	 * @var string
	 */
	protected $username;
	/**
	 * Contains the password for the database.
	 * @var string
	 */
	protected $password;
	/**
	 * Contains the database.
	 * @var string
	 */
	protected $database;
	/**
	 * Path to socket
	 * @var string
	 */
	protected $socket;
	/**
	 * Port number for the connection.
	 * @var int
	 */
	protected $port;
	/**
	 * Last result resource.
	 * @var resource
	 */
	protected $result;
	/**
	 * Object for debugging purposes.
	 * @var Debug
	 */
	protected $debug;
	/**
	 * Tells whether an transaction is in progress (true) or not (false).
	 * @var boolean
	 */
	protected $inProgress;
	/**
	 * Keyowrd NULL for database.
	 *
	 * @var string
	 */
	protected $null;

	/**
	 * Constructs the Database class and sets some default values.
	 *
	 * Auto-commit is off at the beginning!
	 * The log file for debugging will be saved in data/logs/database.log.
	 */
	public function __construct() {
		$this->system = null;
		$this->vendor = null;
		$this->result = null;
		$this->benchmark = array(
			'time' => 0,
			'count' => 0,
			'queries' => array()
		);
		$this->connection = null;
		$this->host = 'localhost';
		$this->username = '';
		$this->password = '';
		$this->database = '';
		$this->socket = null;
		$this->port = 0;
		$this->pre = null;
		$this->debug = new Debug('database.log');
		$this->inProgress = false;
		$this->null = null;
	}

	/**
	 * Disconnect from the database and roll back open transactions.
	 */
	public function __destruct() {
		if ($this->inProgress == true) {
			try {
				$this->rollback();
			} catch (QueryException $e) {
				$this->debug->add($e);
				throw $e;
			}
		}
		$this->disconnect();
	}

	/**
	 * Returns the number of rows affected by the last INSERT, UPDATE or DELETE query.
	 *
	 * If the last query was invalid, this function will return -1.
	 *
	 * @return int Affected rows by the last query
	 **/
	public abstract function affectedRows();

	/**
	 * Turns on or off auto-commiting database modifications.
	 *
	 * If there are open queries that are not already committed a commit will be started.
	 *
	 * @param boolean TRUE turns auto-commit on, FALSE disables it.
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 **/
	public abstract function autocommit($status);

	/**
	 * Tells the server to begin a new transaction.
	 *
	 * Note: For most databases the transaction will be started by turning auto-commit off.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 **/
	public abstract function begin();

	/**
	 * Returns an array with benchmark information.
	 *
	 * Structure of the array:<br>
	 * time => time all queries took<br>
	 * count => number of queries that were executed<br>
	 * queries => array with all queries and the time they took (Keys: time and query)
	 *
	 * @return array Array with benchmark information
	 **/
	public function benchmark() {
		return $this->benchmark;
	}

	/**
	 * Commits the current transaction.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 **/
	public abstract function commit();

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
	 *
	 * Note: The database will not be selected! You have to call Database::selectDB() before you can
	 * work with the database.
	 *
	 * The charset should be set to UTF-8 in the best case.
	 *
	 * @param string Host
	 * @param string Username
	 * @param string Passwort
	 * @param int Port
	 * @param string Socket
	 */
	public abstract function connect($username = null, $password = null, $host = null,
									 $port = null, $socket = null);

	/**
	 * Returns the create statement for creating the specified table.
	 *
	 * @param string Name of the table
	 * @return string Create statement or null
	 */
	public abstract function createStatement($table);

	/**
	 * Closes a previously opened database connection.
	 *
	 * If no connection is open TRUE will be returned.
	 *
	 * @return boolean true on success, false on failure
	 **/
	public abstract function disconnect();

	/**
	 * Returns an internal error message for the last error.
	 *
	 * @return string Error message
	 **/
	public abstract function error();

	/**
	 * Returns an internal error number for the last error.
	 *
	 * @return int Error number
	 **/
	public abstract function errno();

	/**
	 * Escapes special characters in a string for use in a SQL statement.
	 *
	 * You can either specify an string to be escaped or an array.
	 * Each array element will be escaped recursively.
	 *
	 * @param string|array String or array to escape
	 * @return string|array Escaped string or array
	 */
	public abstract function escapeString($data);

	/**
	 * Fetch all result rows as a multidimensional associative array.
	 *
	 * Each first level element contains an associative array that corresponds to the fetched row.
	 * The keys of the first level array can be set to the values of a field from the received
	 * result rows. If no key is specified, the array will be an enumerated array, where each column
	 * is stored in an array offset starting from 0 (zero).
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 * After the result is returned, the resource will be cleared using freeResults().
	 *
	 * @param resource Result set
	 * @param string Field to use for the keys or null to get an enumerated array
	 * @return array All results in a multimensional associative array
	 */
	public function fetchAll($result = null, $key = null) {
		$cache = array();
		$error = false;
		while($row = $this->fetchAssoc($result)){
			if ($key != null) {
				if (isset($row[$key]) == false) {
					Core::throwError(
						"Key assigned in fetchAll() was not found in result set. ".
							"The keys will be enumerated.",
						INTERNAL_NOTICE
					);
					$error = true;
				}
				$cache[$row[$key]] = $row;
			}
			else {
				$cache[] = $row;
			}
		}
		$this->freeResults($result);
		if ($error == true) {
			$cache = array_map('array_values', $cache);
		}
		return $cache;
	}

	/**
	 * Fetch a result row as an associative array
	 *
	 * Returns an associative array that corresponds to the fetched row or null if there are no
	 * more rows. The function is used to return an associative array representing the next row in
	 * the result set for the result represented by the result parameter, where each key in the
	 * array represents the name of one of the result set's columns. If two or more columns of the
	 * result have the same field names, the last column will take precedence. To access the other
	 * column(s) of the same name, you either need to access the result with numeric indices by
	 * using fetchNum() or add alias names.
	 *
	 * If the result set parameter is not specified (null), the last result will be used.
	 *
	 * @param	mixed	Result set or null
	 * @return	array	Associative array or null
	 */
	public abstract function fetchAssoc($result = null);

	/**
	 * Get a result row as an enumerated array.
	 *
	 * Fetches one row of data from the result set represented by result and returns it as an
	 * enumerated array, where each column is stored in an array offset starting from 0 (zero). 
	 * Each subsequent call to the function will return the next row within the result set, or 
	 * null if there are no more rows. If the result set parameter is not specified, the last
	 * result will be used.
	 *
	 * @param mixed Result set
	 * @return array Enumerated array or null
	 **/
	public abstract function fetchNum($result = null);

	/**
	 * Returns the current row of a result set as an object.
	 *
	 * Returns the current row result set as an object where the attributes of the object represent
	 * the names of the fields found within the result set. If no more rows exist in the current
	 * result set, null is returned. If two or more columns of the result have the same field names,
	 * the last column will take precedence. If the result set parameter is not specified, the last
	 * result will be used.
	 *
	 * @param mixed Result set
	 * @return object Object or null
	 */
	public abstract function fetchObject($result = null);

	/**
	 * Fetches the first result row and returns the first field in this row.
	 *
	 * A result set (resource) already returned by a function call or a query (string), that will be
	 * executed, can be specified. If nothing is specified, the last result set will be used.
	 * If no result set is available a warning will be thrown and null will be returned.
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 *
	 * @param mixed Result set (resource) or query (string) or null
	 * @return mixed First field in first row or null
	 * @todo Replace Core::throwError with Exception
	 */
	public function fetchOne($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		if(is_string($result) == true) {
			$result = $this->rawQuery($result);
		}
		if ($this->isResultSet($result) == false) {
			Core::throwError('No valid result set specified.');
		}
		if ($this->numRows($result) > 0) {
			$this->resetResult($result);
			$first = $this->fetchNum($result);
			return $first[0];
		}
		else {
			return null;
		}
	}

	/**
	 * Frees the memory associated with a result.
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 *
	 * @param resource Result set
	 */
	public abstract function freeResults($result = null);

	/**
	 * Returns insert statements for the specified table.
	 *
	 * If offset is = -1: all rows will be returned at once and $limit parameter won't be used.<br />
	 * Is offset is >= 0: all rows starting at row $offset until we reached the row ($offset + $limit).
	 *
	 * @param string Table name
	 * @param int Offset to begin with
	 * @param int Limit that is used per call
	 * @return string Insert statements
	 */
	public abstract function getData($table, $offset = -1, $limit = 1000);

	/**
	 * Returns the internal used Debug-object.
	 *
	 * @return Debug Object used for debugging
	 */
	public function getDebug() {
		return $this->debug;
	}

	/**
	 * Returns the prefix of the tables.
	 *
	 * @return string Prefix
	 */
	public function getPrefix() {
		return $this->pre;
	}

	/**
	 * Returns the create statement for creating the specified table.
	 *
	 * Set the second parameter to true to add "DROP TABLE IF EXISTS" statements before the 
	 * create statement.
	 *
	 * @param string Name of the table
	 * @param boolean Add drop statements (default: false, no drop statements)
	 * @return string Create statement or null
	 */
	public abstract function getStructure($table, $drop = false);

	/**
	 * Returns the ID used in the last INSERT query.
	 *
	 * null is returned on failure or when there was no last INSERT query.
	 *
	 * @return int ID or null
	 */
	public abstract function insertID();

	/**
	 * Returns true if the specified parameter is a valid result set.
	 *
	 * @return boolean
	 */
	public abstract function isResultSet($result);

	/**
	 * Returns true if connection has been established successfully.
	 *
	 * @return boolean
	 */
	public abstract function hasConnection();

	/**
	 * Returns true if the current result set is a valid result set.
	 *
	 * @return boolean
	 */
	public function hasResultSet() {
		return $this->isResultSet($this->result);
	}

	/**
	 * Returns an array containing all fields of the specified table.
	 *
	 * @param string Table
	 * @return array Array containing all fields of a table
	 */
	public function listFields($table);

	/**
	 * Returns an array containing all tables of the specified database.
	 *
	 * If no database is specified, the currently selected database will be used.
	 *
	 * @param string Database or null
	 * @return array Array containing all tables of a database
	 **/
	public function listTables($database = null);

	/**
	 * Performs one or more raw queries on the database.
	 *
	 * Executes one or multiple queries which are concatenated by a semicolon and a linebreak.
	 * You get back an array with all the results in the order of the queries.
	 * Comments are stripped.
	 *
	 * @param string Queries
	 */
	public function multiQuery($query);

	/**
	 * Gets the number of rows in a result.
	 *
	 * @param resource Result set
	 * @return int Number of rows
	 * @todo Replace error with exception
	 */
	public abstract function numRows($result = null);

	protected function parseFloat($var) {
		$var = str_replace(',', '.', strval(floatval($var)));
		return "'".$this->escapeString($var)."'";
	}

	//@todo Replace error with exception
	protected function parseInt($var) {
		if (!is_string($var) && !is_numeric($var)) {
			Core::throwError(
				'Database::parseInt() can only convert strings and numerical data to integers.'
			);
		}
		if (is_string($var)) {
			$var = trim($var);
		}
		return intval($var);
	}

	protected function parseString($var) {
		return "'".$this->escapeString($var)."'";
	}

	/**
	 * Prepares a query and secures/escapes the data.
	 *
	 * The first parameter is a query with placeholders.
	 * The second parameter is an array with the data for the query as an array.
	 * The keys of the array are the placeholders (in the query between the chars < and >).
	 * Placeholders can have only alphanumerical chars plus "_", "-" and ".".
	 * After the placeholder you can specify the type of the placeholder separated by ":".
	 * If you do not specify the type then string will be assumed to be the correct one. You do not need to quote the placeholders.
	 * The values of the array is the data for the placeholder. NULL in php is NULL in the database.<br />
	 * Reserved placeholders: p, prefix for prefix of tables.<br />
	 * Possible types: <br />
	 * - raw: no quotes, no escaping <br />
	 * - string /: quotes, escaping<br />
	 * - string[]: every element -> seperated by a comma, qupotes, escaping<br />
	 * - int: no quotes, conversion to integer<br />
	 * - int[]: every element -> seperated by a comma, no quotes, conversion to integer<br />
	 * - float: quotes, conversion to float
	 *
	 * @param string Single query
	 * @param array Data for the query
	 * @return string Prepared query
	 */
	public function prepareQuery($query, $data = array()) {
		$query = str_ireplace(array('<p>', '<prefix>'), $this->pre, $query);
		if (count($data) > 0) {
			$this->tempData = $data;
			$query = preg_replace_callback(
				'~<([a-zA-Z0-9_\-\.]+)(:([a-z]+(\[\])?))?>~',
				array($this, 'replaceQueryPlaceholder'),
				$query
			);
			$this->tempData = null;
		}
		return $query;
	}

	/**
	 * Performs a secure query on the database.
	 *
	 * Returns an result set for a select statement or a boolean for other statements (true on
	 * success or false on failure). Additionally on failure a QueryExcpetion will be thrown.
	 * For information about the parameters see the method prepareQuery().
	 *
	 * @param string Single query
	 * @param array Data for the query
	 * @throws QueryException
	 * @see Database::prepareQuery()
	 * @return mixed Result set for a select statement, a boolean for other statements.
	 **/
	public function query($query, $data = array()) {
		return $this->rawQuery($this->prepareQuery($query, $data));
	}

	/**
	 * Handles an Query Error: A QueryExcpetion will be thrown.
	 *
	 * @see QueryException
	 * @throws QueryException
	 * @param string Last Query
	 **/
	protected function queryError($query) {
		$e = new QueryException($this->error(), $this->errno());
		$e->setQuery($query);
		$e->setLine(__LINE__);
		$e->setFile(__FILE__);

		// Try to get better results for line and file.
		if (function_exists('debug_backtrace') == true) {
			$backtraceInfo = debug_backtrace();
			// 0 is class.mysql.php, 1 is the calling code...
			if (isset($backtraceInfo[1]) == true) {
				$e->setLine($backtraceInfo[1]['line']);
				$e->setFile($backtraceInfo[1]['file']);
			}
		}

		$this->debug->add($e);

		throw $e;
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
	public abstract function rawQuery($query);

	protected function replaceQueryPlaceholder($matches) {
		$key = $matches[1];
		if (!isset($this->tempData[$key])) {
			return $matches[0];
		}
		else {
			if (empty($matches[3])) {
				$type = 'string';
			}
			else {
				$type = strtolower($matches[3]);
			}
			$data = $this->tempData[$key];
			if ($data === null) {
				return $this->null;
			}
			else {
				switch($type) {
					case 'raw':
						return $data;
					break;
					case 'float':
						return $this->parseFloat($data);
					break;
					case 'int':
						return $this->parseInt($data);
					break;
					case 'string[]':
						$data = array_map(array($this, 'parseString'), $data);
						return implode(',', $data);
					break;
					case 'int[]':
						$data = array_map(array($this, 'parseInt'), $data);
						return implode(',', $data);
					break;
					default:
						return $this->parseString($data);
					break;
				}
			}
		}
	}

	/**
	 * Adjusts the result pointer to the first row in the result set.
	 *
	 * @param resource Result set
	 * @return boolean Returns true on success, false on failure
	 */
	public abstract function resetResult($result = null);

	/**
	 * Rolls back current transaction.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public abstract function rollback();

	/**
	 * Select the default database to use when performing queries against the database.
	 *
	 * @param string Database name
	 * @param string Prefix for tables
	 * @return boolean true on success, false on failure
	 */
	public abstract function selectDB($database, $prefix = '');

	/**
	 * Sets the prefix of the tables.
	 *
	 * @param string Prefix
	 */
	public function setPrefix($prefix) {
		$this->pre = $prefix;
	}

	/**
	 * Returns a string containing which database system (database abstraction layer) is used.
	 *
	 * @return string Database System
	 */
	public function system() {
		return $this->system;
	}

	/**
	 * Returns a string representing the version of the database server.
	 *
	 * @return string Version
	 */
	public abstract function version();

}
?>